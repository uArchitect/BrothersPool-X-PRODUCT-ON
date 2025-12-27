<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Yajra\DataTables\Facades\DataTables;


class AjaxController extends Controller
{
    private function jsonResponse($data, $status = 'success', $message = '', $code = 200)
    {
        $response = ['status' => $status];
        if ($message) $response['message'] = $message;
        if ($data) $response['data'] = $data;
        return response()->json($response, $code);
    }

    private function notFoundResponse($message)
    {
        return $this->jsonResponse(null, 'error', $message, 404);
    }

    private function errorResponse($message, $details = null)
    {
        $response = ['status' => 'error', 'message' => $message];
        if ($details) $response['error_details'] = $details;
        return response()->json($response, 500);
    }

    public function findEmployeeByTable($tableId)
    {
        $employees = DB::table('employees')
            ->where('table_id', $tableId)
            ->where('is_active', 1)
            ->get();

        return $this->jsonResponse($employees);
    }



    public function findEmployeeForService($serviceId)
    {
        $employee = DB::table('menu_items')
            ->join('employees', 'services.employee_id', '=', 'employees.id')
            ->select('employees.*')
            ->where('services.id', $serviceId)
            ->get();

        return $this->jsonResponse($employee);
    }

    public function deleteReservationItem($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $item = DB::table('reservations_items')->where('id', $id)->first();
                if (!$item) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Rezervasyon öğesi bulunamadı.'
                    ], 404);
                }

                $reservation = DB::table('reservations')->where('id', $item->reservation_id)->first();
                if (!$reservation) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Rezervasyon bulunamadı.'
                    ], 404);
                }

                $remainingPrice = $reservation->total_price - $item->price;

                DB::table('reservations_items')->where('id', $id)->delete();
                DB::table('reservations')
                    ->where('id', $reservation->id)
                    ->update(['total_price' => $remainingPrice]);

                return response()->json([
                    'success' => true,
                    'message' => 'Rezervasyon hizmeti başarıyla silindi.',
                    'data' => [
                        'remaining_price' => $remainingPrice,
                        'reservation_id' => $reservation->id
                    ]
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Silme işleminde bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRooms()
    {
        $rooms = DB::table('tables')->get();
        return $this->jsonResponse($rooms);
    }

    public function getReservationItems($reservationId)
    {
        $reservationItems = DB::table('reservations_items')
            ->join('menu_items', 'reservations_items.service_id', '=', 'services.id')
            ->where('reservations_items.reservation_id', $reservationId)
            ->select('services.name as service_name', 'reservations_items.id as item_id', 'reservations_items.price as service_price')
            ->get();

        if ($reservationItems->isEmpty()) {
            return $this->notFoundResponse('No reservation items found for the given reservation ID.');
        }

        return $this->jsonResponse($reservationItems);
    }

    public function addReservation(Request $request)
    {


         $settings = DB::table('settings')->first();
        

        try {
            // Telefon numarası validasyonu ekle
            $validatedData = $request->validate([
                'phone' => 'nullable|string|regex:/^[0-9+\-\s\(\)]{10,15}$/',
                'customercurrentphone' => 'nullable|string|regex:/^[0-9+\-\s\(\)]{10,15}$/',
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'menu_item_id' => 'required|array',
                'menu_item_id.*' => 'exists:menu_items,id',
                'doctor_id' => 'required|exists:employees,id',
                'room_id' => 'required|exists:tables,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'customer_id' => 'nullable', // Customer module disabled
                'newCustomer' => 'nullable|boolean'
            ], [
                'phone.regex' => 'Geçerli bir telefon numarası giriniz (10-15 haneli, sadece rakam ve +, -, (, ), boşluk karakterleri kullanılabilir).',
                'customercurrentphone.regex' => 'Geçerli bir telefon numarası giriniz (10-15 haneli, sadece rakam ve +, -, (, ), boşluk karakterleri kullanılabilir).',
                'first_name.max' => 'Ad en fazla 255 karakter olabilir.',
                'last_name.max' => 'Soyad en fazla 255 karakter olabilir.',
                'services_id.required' => 'En az bir hizmet seçmelisiniz.',
                'services_id.*.exists' => 'Seçilen hizmet geçersiz.',
                'doctor_id.required' => 'Doktor seçimi zorunludur.',
                'doctor_id.exists' => 'Seçilen doktor geçersiz.',
                'room_id.required' => 'Oda seçimi zorunludur.',
                'room_id.exists' => 'Seçilen oda geçersiz.',
                'start_date.required' => 'Başlangıç tarihi zorunludur.',
                'start_date.date' => 'Geçerli bir başlangıç tarihi giriniz.',
                'end_date.required' => 'Bitiş tarihi zorunludur.',
                'end_date.date' => 'Geçerli bir bitiş tarihi giriniz.',
                'end_date.after' => 'Bitiş tarihi başlangıç tarihinden sonra olmalıdır.',
                'customer_id.exists' => 'Seçilen müşteri geçersiz.'
            ]);

            return DB::transaction(function () use ($request) {
                $customerId = $this->getOrCreateCustomer($request);
                if (!$customerId) {
                    return $this->errorResponse('Geçerli bir müşteri seçilmelidir.');
                }

                $services = DB::table('menu_items')
                    ->whereIn('id', (array)$request->menu_item_id)
                    ->get(['id', 'total_price']);

                $totalPrice = $services->sum('total_price');
                
                // Telefon numarası güncelleme - formatlanmış telefon numarası ile
                if ($request->customercurrentphone) {
                    $phoneNumber = $this->formatPhoneNumber($request->customercurrentphone);
                    $updated = DB::table('customers')->where('id', $customerId)->update(['phone' => $phoneNumber]);
                }

                // Ana rezervasyonu oluştur
                $reservationData = [
                    'customer_id' => $customerId,
                    'start_date' => Carbon::parse($request->start_date),
                    'end_date' => Carbon::parse($request->end_date),
                    'total_price' => $totalPrice,
                    'employee_id' => $request->doctor_id,
                    'table_id' => $request->room_id,
                    'color' => $request->color
                ];

                $settings = DB::table('settings')->first();

                $reservationId = DB::table('reservations')->insertGetId($reservationData);
                $customer = DB::table('customers')->where('id', $customerId)->first();

                $this->createReservationItems($reservationId, $services);

                
        // Calendar/online reservation disabled: skip SMS booking link
        // $this->BookingSMS($customer->phone, $reservationData['start_date'], $reservationId);

                return $this->jsonResponse(null, 'success', 'Rezervasyon başarıyla oluşturuldu.');
            });
        } catch (\Exception $e) {
            // Hata mesajını günlüğe yazdır ve konsola yazdır
            Log::error('Failed to create reservation: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create reservation.',
                'error_details' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    private function getOrCreateCustomer(Request $request)
    {
        // Customer module disabled
        return null;
    }
	
	
	 private function formatPhoneNumber($phone)
    {
        // Sadece rakamları al
        $digits = preg_replace('/\D/', '', $phone);

        // 10 haneli ise başına 0 ekle
        if (strlen($digits) == 10) {
            $digits = '0' . $digits;
        }
        // 11 haneli ise başında 0 olmalı
        if (strlen($digits) == 11 && $digits[0] !== '0') {
            $digits = '0' . substr($digits, -10);
        }
        // 12 haneli ise başında 90 varsa 0 ile değiştir
        if (strlen($digits) == 12 && substr($digits, 0, 2) == '90') {
            $digits = '0' . substr($digits, 2);
        }
        return $digits;
    }


    private function BookingSMS($phone, $startDate, $reservationId)
    {
        $settings = DB::table('settings')->first();

        if($settings->booking_sms_message_status == 1){
            $date = Carbon::parse($startDate)->format('d.m.Y');
            $hour = Carbon::parse($startDate)->format('H:i');
            $companyPhone = $settings->phone_number;
            $message = str_replace(
                ['[Tarih]', '[Saat]', '[Telefon Numarası]', '[Link]'],
                [$date, $hour, $companyPhone, request()->getSchemeAndHttpHost() . '/online/rezervasyon/' . $reservationId],
                $settings->booking_sms_message
            );

            // Hizmetleri al
            $services = DB::table('reservations_items')
                ->join('menu_items', 'reservations_items.service_id', '=', 'services.id')
                ->where('reservations_items.reservation_id', $reservationId)
                ->pluck('services.name')
                ->implode(', ');

            $message = str_replace('[HİZMETLER]', $services, $message);

            // SMS Gönderme
            $client = new Client();
            $client->post('http://api.mesajpaneli.com/index.php', [
                'form_params' => [
                    'islem' => 1,
                    'user' => $settings->sms_username,
                    'pass' => $settings->sms_password,
                    'mesaj' => $message,
                    'numaralar' => $phone,
                    'baslik' => $settings->sms_header,
                ]
            ]);

            // Veritabanı güncellemeleri
            DB::table('send_sms_code')->insert([
                'phone' => $phone,
                'status' => 'Gönderildi',
                'contents' => $message,
                'type' => 'booking',
                'created_at' => now()
            ]);

            DB::table('settings')->where('id', 1)->decrement('remaining_sms_limit', 1);
        }
        
        return true;
    }


    private function createReservationItems($reservationId, $services)
    {
        $reservationItems = $services->map(fn($service) => [
            'reservation_id' => $reservationId,
            'service_id' => $service->id,
            'price' => $service->total_price,
            'quantity' => 1,
        ])->toArray();

        DB::table('reservations_items')->insert($reservationItems);
    }

    // Calendar module disabled; keep function for compatibility but can be removed later
    public function deleteReservation($reservationId)
    {
        try {
            return DB::transaction(function () use ($reservationId) {
                $deleted = DB::table('reservations')->where('id', $reservationId)->delete();
                DB::table('reservations_items')->where('reservation_id', $reservationId)->delete();

                if (!$deleted) {
                    return $this->errorResponse('Failed to delete reservation.');
                }

                return $this->jsonResponse(null, 'success', 'Reservation successfully deleted.');
            });
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete reservation.', $e->getMessage());
        }
    }


    public function getReservations()
    {
        $reservations = DB::table('reservations')
            ->join('tables', 'reservations.table_id', '=', 'tables.id')
            ->join('employees', 'reservations.employee_id', '=', 'employees.id')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('reservations_items', 'reservations.id', '=', 'reservations_items.reservation_id')
            ->leftJoin('services as service', 'reservations_items.service_id', '=', 'service.id')
            ->select(
                'tables.id as table_id',
                'tables.name as table_name',
                'employees.name as employee_name',
                'service.name as service_name',
                'reservations.start_date',
                'reservations.end_date',
                'reservations.id',
                DB::raw("COALESCE(customers.name, CONCAT(COALESCE(customers.first_name, ''), ' ', COALESCE(customers.last_name, '')), 'Misafir') as customer_name")
            )
            ->get();

        return response()->json($reservations);
    }


    public function update($id, Request $request)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $reservation->update($request->all());

            // İlişkili verileri de yükle
            $reservation->load(['customer', 'service']);

            return response()->json([
                'message' => 'Rezervasyon başarıyla güncellendi',
                'reservation' => $reservation
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Güncelleme sırasında bir hata oluştu'], 500);
        }
    }

    public function getReservation($id)
    {
        try {
            // Ana rezervasyon ve müşteri bilgilerini al
            $reservation = DB::table('reservations')
                ->join('customers', 'reservations.customer_id', '=', 'customers.id')
                ->join('employees', 'reservations.employee_id', '=', 'employees.id')
                ->where('reservations.id', $id)
                ->select(
                    'reservations.*',
                    'customers.id as customer_id',
                    'customers.name as first_name',
                    'customers.last_name',
                    'employees.id as employee_id',
                    'employees.name as employee_name'
                )
                ->first();

            if (!$reservation) {
                return $this->notFoundResponse('Rezervasyon bulunamadı.');
            }

            // Rezervasyona ait tüm hizmetleri al
            $services = DB::table('reservations_items')
                ->join('menu_items', 'reservations_items.service_id', '=', 'services.id')
                ->where('reservations_items.reservation_id', $id)
                ->select(
                    'services.id as service_id',
                    'services.name as service_name',
                    'reservations_items.price as service_price'
                )
                ->get();

            // Rezervasyon verisini düzenle
            $reservationData = (array) $reservation;
            $reservationData['menu_items'] = $services;

            return $this->jsonResponse(['reservation' => $reservationData]);
        } catch (\Exception $e) {
            return $this->errorResponse('Rezervasyon bilgileri alınırken bir hata oluştu.', $e->getMessage());
        }
    }

    public function getCustomerReservation($id)
    {
        try {
            $reservation = DB::table('reservations')
                ->join('customers', 'reservations.customer_id', '=', 'customers.id')
                ->join('employees', 'reservations.employee_id', '=', 'employees.id')
                ->join('reservations_items', 'reservations.id', '=', 'reservations_items.reservation_id')
                ->join('menu_items', 'reservations_items.service_id', '=', 'services.id')
                ->join('tables', 'reservations.table_id', '=', 'tables.id')
                ->where('reservations.id', $id)
                ->select(
                    //table name
                    'reservations.id',
                    'tables.name as table_name',
                    'tables.id as table_id',
                    'customers.name as first_name',
                    'customers.last_name',
                    'services.name as service_name',
                    'employees.name as employee_name',
                    'reservations.start_date',
                    'reservations.end_date',
                    'reservations.status',
                    'reservations.color'
                )
                ->first();

            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rezervasyon bulunamadı'
                ], 404);
            }

            $reservationData = [
                'reservation_id' => $reservation->id,
                'table_name' => $reservation->table_name,
                'table_id' => $reservation->table_id,
                'customer_name' => $reservation->first_name . ' ' . $reservation->last_name,
                'service_name' => $reservation->service_name,
                'employee_name' => $reservation->employee_name,
                'start_date' => $reservation->start_date,
                'end_date' => $reservation->end_date,
                'status' => $reservation->status,
                'color' => $reservation->color
            ];

            return response()->json([
                'success' => true,
                'reservation' => $reservationData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }



    public function getParapuanSettings($coin)
    {
        try {
            $updated = DB::table('settings')->where('id', 1)->update(['parapuan' => $coin, 'parapuan_system_enabled' => true]);

            if (!$updated) {
                return $this->notFoundResponse('Parapuan ayarı bulunamadı.');
            }

            return $this->jsonResponse(
                ['coin' => $coin],
                'success',
                'Parapuan ayarı başarıyla güncellendi'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Parapuan ayarı güncellenirken bir hata oluştu.', $e->getMessage());
        }
    }


    public function getSellers(Request $request)
    {
        $query = DB::table('employees')
            ->where('is_active', 1)
            ->select('id', 'name', 'phone');

        if ($request->has('q')) {
            $query->where('name', 'LIKE', '%' . $request->q . '%');
        }

        $limit = 10;
        $sellers = $query->paginate($limit);

        return response()->json([
            'data' => $sellers->items(),
            'has_more' => $sellers->hasMorePages()
        ]);
    }

    public function getCustomers(Request $request)
    {
        // Customer module disabled
        return response()->json([
            'success' => false,
            'message' => 'Müşteri modülü devre dışı bırakılmıştır.',
            'data' => [],
            'pagination' => null
        ], 403);
    }

    public function getProducts(Request $request)
    {
        $query = DB::table('menu_items')
            ->join('categories', 'services.category_id', '=', 'categories.id')
            ->join('tax_rates', 'services.tax_id', '=', 'tax_rates.id')
            ->select(
                'services.id',
                'services.name as product_name',
                'services.total_price as product_price',
                'services.stock as product_stock',
                'tax_rates.rate as tax_rate',
                'categories.name as category_name'
            )
            ->where('services.is_stock', 1);

        if ($request->has('q')) {
            $query->where('services.name', 'LIKE', '%' . $request->q . '%');
        }

        $limit = 10;
        $products = $query->paginate($limit);

        return response()->json([
            'data' => $products->items(),
            'has_more' => $products->hasMorePages()
        ]);
    }

    public function getProductRow()
    {
        $product = DB::table('products')->get();
        return response()->json($product);
    }

    public function getProductDetails($id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        return response()->json($product);
    }

    public function getServices()
    {
        $services = DB::table('menu_items')->get();
        return response()->json($services);
    }

    public function getExpenseItems($id)
    {
        $expenseItems = DB::table('expense_items')
            ->leftJoin('expense_categories', 'expense_items.expense_category_id', '=', 'expense_categories.id')
            ->where('expense_items.expense_id', $id)
            ->select(
                'expense_items.*',
                'expense_categories.name as category_name'
            )
            ->get();
        
        return response()->json($expenseItems);
    }


    public function getRecommendations()
    {
        $findCustomerReservationProccsess = DB::table('reservations')
            ->join('reservations_items', 'reservations.id', '=', 'reservations_items.reservation_id')
            ->join('menu_items', 'reservations_items.service_id', '=', 'services.id')
            ->select('services.id as service_id', 'services.name as service_name', DB::raw('count(services.id) as service_count'), DB::raw('sum(reservations_items.price) as grand_total'))
            ->groupBy('services.id', 'services.name')
            ->orderBy('service_count', 'desc')
            ->get();

        // Öneri listesi oluştur
        $Recommendations = $findCustomerReservationProccsess->map(function ($item) {
            return [
                'service_id' => $item->service_id,
                'service_name' => $item->service_name,
                'grand_total' => $item->grand_total,
                'service_count' => $item->service_count
            ];
        });

        // Eğer geçmiş rezervasyon yoksa, rastgele öneriler sun
        if ($Recommendations->isEmpty()) {
            $Recommendations = DB::table('menu_items')
                ->where('is_active', 1)
                ->inRandomOrder()
                ->limit(5)
                ->get(['id as service_id', 'name as service_name', 'price as grand_total'])
                ->map(function ($item) {
                    return [
                        'service_id' => $item->service_id,
                        'service_name' => $item->service_name,
                        'grand_total' => $item->grand_total,
                        'service_count' => 0
                    ];
                });
        } else {
            // Öneri listesini rastgele sırala ve belirli bir sayıda öneri döndür
            $Recommendations = $Recommendations->shuffle()->take(3);
        }

        return response()->json(['success' => true, 'recommendedProcess' => $Recommendations]);
    }

    public function getCustomerProcess($id)
    {
        // Customer module disabled
        return response()->json([]);
    }

    public function getCustomerPaymentInformation($id)
    {
        // Customer module disabled
        return response()->json([]);
    }


    public function getCustomerInformationSales($id)
    {
        // Customer module disabled
        return response()->json(null);
    }

    public function SaleItems($sale_id)
    {

        $saleItems = DB::table('sale_items')
            ->leftJoin('tax_rates', 'sale_items.tax_id', '=', 'tax_rates.id')
            ->where('sale_items.sale_id', $sale_id)
            ->select(
                'sale_items.id as id',
                'sale_items.product_name as name',
                'sale_items.sale_id as sale_id',
                'sale_items.service_id as service_id',
                'sale_items.net_unit_price as net_unit_price',
                'sale_items.unit_price as unit_price',
                'sale_items.item_tax as item_tax',
                'sale_items.tax_id as tax_id',
                'tax_rates.rate as tax_rate',
                'sale_items.discount as discount',
                'sale_items.subtotal as subtotal',
                'sale_items.created_at as created_at',
                'sale_items.updated_at as updated_at'
            )
            ->get();

        return response()->json($saleItems);
    }



    /**
     * Çalışanın komisyon geçmişini getirir.
     * Her bir komisyon kaydı için tam employee_commissions tablosu alanlarını ve ilgili hizmetleri döner.
     * Komisyonlar id'ye göre büyükten küçüğe sıralanır.
     */
    public function getEmployeeHistoryModal($employee_id)
    {
        // employee_commissions tablosundan, ilgili çalışanın ve tamamlanan satışların komisyon kayıtlarını al
        // sale_id bazında gruplayıp, amount'u toplayacağız
        $employeeCommissions = DB::table('employee_commissions')
            ->join('sales', 'employee_commissions.sale_id', '=', 'sales.id')
            ->where('employee_commissions.employee_id', $employee_id)
            ->where('sales.status', 'completed')
            ->orderBy('employee_commissions.id', 'desc')
            ->select(
                'employee_commissions.sale_id',
                DB::raw('SUM(employee_commissions.amount) as total_commission'),
                DB::raw('MAX(employee_commissions.created_at) as created_at'),
                DB::raw('MAX(employee_commissions.updated_at) as updated_at'),
                DB::raw('MAX(employee_commissions.status) as status'),
                DB::raw('MAX(employee_commissions.reservation_id) as reservation_id'),
                DB::raw('MAX(employee_commissions.employee_id) as employee_id'),
                'sales.date as sale_date'
            )
            ->groupBy('employee_commissions.sale_id', 'sales.date')
            ->get();

        $result = [];
        foreach ($employeeCommissions as $commission) {
            // Komisyonun müşteri bilgisini al
            $customer = DB::table('sales')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->where('sales.id', $commission->sale_id)
                ->select('customers.first_name', 'customers.last_name')
                ->first();

            // Komisyonun ödeme durumu ve tarihi
            $commission->payment_status = $commission->status == 1 ? 1 : 0;
            $commission->payment_date = $commission->created_at ? date('Y-m-d', strtotime($commission->created_at)) : null;

            // Komisyonun müşteri ad-soyadını ekle
            $commission->customer_first_name = $customer ? $customer->first_name : null;
            $commission->customer_last_name = $customer ? $customer->last_name : null;

            // Komisyonun bağlı olduğu satıştaki tüm hizmetleri döndür
            $items = DB::table('sale_items')
                ->join('menu_items', 'sale_items.menu_item_id', '=', 'menu_items.id')
                ->where('sale_items.sale_id', $commission->sale_id)
                ->select(
                    'menu_items.name as service_name',
                    DB::raw('ROUND(sale_items.total_price, 2) as service_price'),
                    'sale_items.quantity as quantity',
                    DB::raw('ROUND(sale_items.unit_price, 2) as unit_price')
                )
                ->get();

            // Her hizmet için toplam komisyonu ve status'u ekle
            $commission->items = $items->map(function($item) use ($commission) {
                return [
                    'service_name' => $item->service_name,
                    'service_price' => $item->service_price,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'commission_amount' => number_format($commission->total_commission, 2),
                    'commission_status' => $commission->status
                ];
            })->values();

            // Sonuç dizisine ekle
            $result[] = [
                'sale_id' => $commission->sale_id,
                'reservation_id' => $commission->reservation_id,
                'employee_id' => $commission->employee_id,
                'total_commission' => number_format($commission->total_commission, 2),
                'created_at' => $commission->created_at,
                'updated_at' => $commission->updated_at,
                'status' => $commission->status,
                'sale_date' => $commission->sale_date,
                'payment_status' => $commission->payment_status,
                'payment_date' => $commission->payment_date,
                'customer_first_name' => $commission->customer_first_name,
                'customer_last_name' => $commission->customer_last_name,
                'items' => $commission->items
            ];
        }

        return response()->json($result);
    }




    public function getEmployeeFeedback($id)
    {
        $feedback = DB::table('feedback')
            ->where('employee_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($feedback);
    }


    public function getAllCustomers()
    {
        // Customer module disabled
        return response()->json([]);
    }

    public function getAllServices()
    {
        $services = DB::table('menu_items')->get();
        return response()->json($services);
    }

    public function getReservationItemsFind($reservationId)
    {
        //rezervasyonun itemslarını bul
        $reservationItems = DB::table('reservations_items')
            ->join('menu_items', 'reservations_items.service_id', '=', 'services.id')
            ->where('reservation_id', $reservationId)
            ->select('services.id as service_id', 'services.name as service_name', 'reservations_items.price as price')
            ->get();
    }

    public function getReportData($type)
    {
        $reports = [];
        switch ($type) {
            case 'services-daily':
                $reports = $this->getServicesDailyReport();
                break;
            case 'services-monthly':
                $reports = $this->getServicesMonthlyReport();
                break;
            case 'services-popular':
                $reports = $this->getServicesPopularReport();
                break;
            case 'employee-performance':
                $reports = $this->getStaffPerformanceReport();
                break;
            case 'employee-commission':
                $reports = $this->getStaffCommissionReport();
                break;
            case 'employee-attendance':
                $reports = $this->getStaffAttendanceReport();
                break;
            case 'customer-loyalty':
                $reports = $this->getCustomerLoyaltyReport();
                break;
            case 'customer-frequency':
                $reports = $this->getCustomerFrequencyReport();
                break;
            case 'customer-new':
                $reports = $this->getCustomerNewReport();
                break;
            case 'financial-daily':
                $reports = $this->getFinancialDailyReport();
                break;
            case 'financial-monthly':
                $reports = $this->getFinancialMonthlyReport();
                break;
            case 'financial-yearly':
                $reports = $this->getFinancialYearlyReport();
                break;
            case 'appointment-daily':
                $reports = $this->getAppointmentDailyReport();
                break;
            case 'appointment-cancel':
                $reports = $this->getAppointmentCancelReport();
                break;
            case 'appointment-occupancy':
                $reports = $this->getAppointmentOccupancyReport();
                break;
            case 'inventory-current':
                $reports = $this->getInventoryCurrentReport();
                break;
            case 'inventory-movement':
                $reports = $this->getInventoryMovementReport();
                break;
            case 'inventory-alert':
                $reports = $this->getInventoryAlertReport();
                break;
        }

        return response()->json($reports);
    }


    public function getServicesDailyReport()
    {
        $report = DB::table('reservations')
            ->join('reservations_items', 'reservations.id', '=', 'reservations_items.reservation_id')
            ->join('menu_items', 'reservations_items.service_id', '=', 'services.id')
            ->select(
                DB::raw('services.name as "Toplam Hizmet"'),
                DB::raw('SUM(reservations_items.price) as "Toplam Fiyat"'),
                DB::raw('COUNT(reservations_items.id) as "Toplam Adet"')
            )
            ->groupBy('services.id', 'services.name')
            ->get();

        return $report;
    }

    public function getServicesMonthlyReport()
    {
        $report = DB::table('reservations')
            ->join('reservations_items', 'reservations.id', '=', 'reservations_items.reservation_id')
            ->join('menu_items', 'reservations_items.service_id', '=', 'services.id')
            ->select(
                DB::raw('services.name as "Toplam Hizmet"'),
                DB::raw('SUM(reservations_items.price) as "Toplam Fiyat"'),
                DB::raw('COUNT(reservations_items.id) as "Toplam Adet"')
            )
            ->groupBy('services.id', 'services.name')
            ->get();

        return $report;
    }

    public function getServicesPopularReport()
    {
        $report = DB::table('reservations_items')
            ->join('menu_items', 'reservations_items.service_id', '=', 'services.id')
            ->select(
                DB::raw('services.name as "Toplam Hizmet"'),
                DB::raw('SUM(reservations_items.price) as "Toplam Fiyat"'),
                DB::raw('COUNT(reservations_items.id) as "Toplam Adet"')
            )
            ->groupBy('services.id', 'services.name')
            ->get();

        return $report;
    }

    public function getStaffPerformanceReport()
    {
        $report = DB::table('reservations')
            ->join('employees', 'reservations.employee_id', '=', 'employees.id')
            ->select(
                DB::raw('employees.name as "Çalışan"'),
                DB::raw('COUNT(reservations.id) as "Toplam Rezervasyon"'),
                DB::raw('SUM(reservations.total_price) as "Toplam Fiyat"')
            )
            ->groupBy('employees.id', 'employees.name')
            ->get();

        return $report;
    }

    public function getStaffCommissionReport()
    {
        $report = DB::table('reservations')
            ->join('employees', 'reservations.employee_id', '=', 'employees.id')
            ->select(
                DB::raw('employees.name as "Çalışan"'),
                DB::raw('COUNT(reservations.id) as "Toplam Rezervasyon"'),
                DB::raw('SUM(reservations.total_price) as "Toplam Fiyat"')
            )
            ->groupBy('employees.id', 'employees.name')
            ->get();

        return $report;
    }

    public function getStaffAttendanceReport()
    {
        $report = DB::table('reservations')
            ->join('employees', 'reservations.employee_id', '=', 'employees.id')
            ->select(
                DB::raw('employees.name as "Çalışan"'),
                DB::raw('COUNT(reservations.id) as "Toplam Rezervasyon"'),
                DB::raw('SUM(reservations.total_price) as "Toplam Fiyat"')
            )
            ->groupBy('employees.id', 'employees.name')
            ->get();

        return $report;
    }

    public function getCustomerLoyaltyReport()
    {
        // Customer module disabled
        return collect([]);
    }

    public function getCustomerFrequencyReport()
    {
        // Customer module disabled
        return collect([]);
    }

    public function getCustomerNewReport()
    {
        // Customer module disabled
        return collect([]);
    }

    public function getFinancialDailyReport()
    {
        $report = DB::table('reservations')
            ->select(
                DB::raw('DATE(reservations.start_date) as "Tarih"'),
                DB::raw('SUM(reservations.total_price) as "Toplam Fiyat"'),
                DB::raw('COUNT(reservations.id) as "Toplam Rezervasyon"')
            )
            ->groupBy('Tarih')
            ->get();

        return $report;
    }

    public function getFinancialMonthlyReport()
    {
        $report = DB::table('reservations')
            ->select(
                DB::raw('DATE(reservations.start_date) as "Tarih"'),
                DB::raw('SUM(reservations.total_price) as "Toplam Fiyat"'),
                DB::raw('COUNT(reservations.id) as "Toplam Rezervasyon"')
            )
            ->groupBy('Tarih')
            ->get();

        return $report;
    }


    public function getisAvailableServices()
    {
        $report = DB::table('menu_items')
            ->where('is_stock', 1)
            ->where('is_active', 1)
            ->get();

        return response()->json($report);
    }


    //sadece bugunun rezervasyonlarını getir
    public function getTodayReservations()
    {
        $report = DB::table('reservations')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->join('employees', 'reservations.employee_id', '=', 'employees.id')
            ->join('tables', 'reservations.table_id', '=', 'tables.id')
            ->where('start_date', '>=', now()->startOfDay())
            ->where('start_date', '<=', now()->endOfDay())
            ->select(
                'reservations.id',
                'reservations.start_date',
                'reservations.end_date',
                'customers.first_name',
                'customers.last_name',
                'employees.name',
                'reservations.total_price',
                'tables.name'
            )
            ->where('reservations.status', 'pending')
            ->orderBy('reservations.start_date', 'desc')
            ->get();

        return response()->json($report);
    }

    public function getEmployeesList(Request $request)
    {
        try {
            // Sayfalama parametreleri
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            
            // Sıralama parametreleri
            $orderColumn = 'employees.name'; // Varsayılan sıralama
            $orderDir = 'asc';
            
            if ($request->has('order')) {
                $orderColumnIndex = $request->input('order.0.column', 0);
                $columns = [
                    'employees.name',
                    'categories.name',
                    'employees.phone',
                    'employees.hire_date',
                    'paid_amount',
                    'unpaid_amount'
                ];
                $orderColumn = $columns[$orderColumnIndex] ?? 'employees.name';
                $orderDir = $request->input('order.0.dir', 'asc');
            }
            
            // Arama parametresi
            $search = $request->input('search.value');
            
            // Ana sorgu
            $query = DB::table('employees')
                ->select([
                    'employees.id',
                    'employees.name',
                    'employees.phone',
                    'employees.hire_date',
                    'employees.role as position_name',
                    DB::raw('COALESCE(SUM(CASE WHEN employee_commissions.status = 1 THEN employee_commissions.amount ELSE 0 END), 0) as paid_amount'),
                    DB::raw('COALESCE(SUM(CASE WHEN employee_commissions.status = 0 THEN employee_commissions.amount ELSE 0 END), 0) as unpaid_amount')
                ])
                ->leftJoin('employee_commissions', 'employees.id', '=', 'employee_commissions.employee_id')
                ->groupBy('employees.id', 'employees.name', 'employees.phone', 'employees.hire_date', 'employees.role');
            
            // Arama filtresi
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('employees.name', 'like', "%{$search}%")
                      ->orWhere('employees.phone', 'like', "%{$search}%")
                      ->orWhere('employees.role', 'like', "%{$search}%");
                });
            }
            
            // Toplam kayıt sayısı (sayfalama öncesi)
            $totalQuery = DB::table('employees')
                ->leftJoin('employee_commissions', 'employees.id', '=', 'employee_commissions.employee_id')
                ->groupBy('employees.id');
            
            if ($search) {
                $totalQuery->where(function($q) use ($search) {
                    $q->where('employees.name', 'like', "%{$search}%")
                      ->orWhere('employees.phone', 'like', "%{$search}%")
                      ->orWhere('employees.role', 'like', "%{$search}%");
                });
            }
            
            $totalRecords = $totalQuery->count();
            $filteredRecords = $totalRecords;
            
            // Sıralama ve sayfalama
            $records = $query
                ->orderBy($orderColumn, $orderDir)
                ->offset($start)
                ->limit($length)
                ->get();
            
            // Verileri formatlama
            $data = [];
            foreach ($records as $record) {
                $data[] = [
                    'id' => $record->id,
                    'name' => $record->name,
                    'specialty_name' => $record->specialty_name ?? '-',
                    'phone' => $record->phone,
                    'hire_date' => Carbon::parse($record->hire_date)->format('d/m/Y'),
                    'paid_amount' => number_format($record->paid_amount ?? 0, 2) . ' TL',
                    'unpaid_amount' => number_format($record->unpaid_amount ?? 0, 2) . ' TL',
                    'actions' => view('layouts.module.employee_actions', ['employee' => (object)$record])->render()
                ];
            }
            
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            Log::error('Employee list error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'error' => true,
                'message' => 'Veri yüklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStockMovements($id)
    {
        try {
            // Ürün bilgilerini al
            $product = DB::table('menu_items')
                ->leftJoin('categories', 'services.category_id', '=', 'categories.id')
                ->where('services.id', $id)
                ->where('services.is_stock', 1)
                ->select(
                    'services.id',
                    'services.name',
                    'services.stock as current_stock',
                    'categories.name as category_name'
                )
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ürün bulunamadı veya stok takibi yapılmıyor.'
                ], 404);
            }

            // Hem giriş hem çıkış hareketlerini al
            $movements = collect([]);
            
            // Önce stock_movements tablosu var mı kontrol et
            $tableExists = DB::select("SHOW TABLES LIKE 'stock_movements'");
            $hasMovementsTable = !empty($tableExists);

            if ($hasMovementsTable) {
                $movements = DB::table('stock_movements')
                    ->where('stock_movements.menu_item_id', $id)
                    ->select(
                        'stock_movements.id',
                        'stock_movements.type',
                        'stock_movements.quantity',
                        'stock_movements.notes',
                        'stock_movements.created_at'
                    )
                    ->orderBy('stock_movements.created_at', 'desc')
                    ->limit(100)
                    ->get();
            } else {
                // Eğer stock_movements tablosu yoksa, satışlardan çıkış hareketlerini oluştur
                $movements = DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sale_items.service_id', $id)
                    ->where('sales.sale_status', 1)
                    ->select(
                        DB::raw("'out' as type"),
                        'sale_items.quantity',
                        DB::raw("CONCAT('Satış #', sales.id) as notes"),
                        'sales.created_at'
                    )
                    ->orderBy('sales.created_at', 'desc')
                    ->limit(100)
                    ->get();
            }

            // Hareket tiplerini Türkçeleştir ve formatla
            $formattedMovements = $movements->map(function ($movement) use ($product) {
                $typeMapping = [
                    'in' => ['label' => 'Giriş', 'class' => 'success', 'icon' => 'fa-plus'],
                    'out' => ['label' => 'Çıkış', 'class' => 'danger', 'icon' => 'fa-minus'],
                    'adjustment' => ['label' => 'Düzeltme', 'class' => 'info', 'icon' => 'fa-edit'],
                    'sale' => ['label' => 'Satış', 'class' => 'warning', 'icon' => 'fa-shopping-cart'],
                    'purchase' => ['label' => 'Alış', 'class' => 'success', 'icon' => 'fa-shopping-bag'],
                    'return' => ['label' => 'İade', 'class' => 'primary', 'icon' => 'fa-undo'],
                ];

                $type = $typeMapping[$movement->type] ?? ['label' => ucfirst($movement->type), 'class' => 'secondary', 'icon' => 'fa-circle'];

                // Quantity değerini güvenli şekilde al
                $quantity = abs($movement->quantity ?? 0);
                $isPositive = in_array($movement->type, ['in', 'purchase', 'return', 'adjustment']);
                $quantityFormatted = $isPositive ? '+' . $quantity : '-' . $quantity;

                return [
                    'id' => $movement->id ?? uniqid(),
                    'type' => $movement->type,
                    'type_label' => $type['label'],
                    'type_class' => $type['class'],
                    'type_icon' => $type['icon'],
                    'quantity' => $quantity,
                    'quantity_formatted' => $quantityFormatted,
                    'is_positive' => $isPositive,
                    'note' => $movement->notes ?? $movement->note ?? '-',
                    'date' => Carbon::parse($movement->created_at)->format('d.m.Y H:i'),
                    'user_name' => $movement->user_name ?? 'Sistem',
                    'created_at_human' => Carbon::parse($movement->created_at)->diffForHumans()
                ];
            });

            // Toplam hesaplamaları
            $totalIn = $formattedMovements->where('is_positive', true)->sum('quantity');
            $totalOut = $formattedMovements->where('is_positive', false)->sum('quantity');
            $netChange = $totalIn - $totalOut;

            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category_name' => $product->category_name ?? 'Kategori Yok',
                    'current_stock' => $product->current_stock
                ],
                'movements' => $formattedMovements,
                'movements_count' => $formattedMovements->count(),
                'totals' => [
                    'total_in' => $totalIn,
                    'total_out' => $totalOut,
                    'net_change' => $netChange,
                    'current_stock' => $product->current_stock
                ],
                'has_movements_table' => $hasMovementsTable
            ]);

        } catch (\Exception $e) {
            Log::error('Stock movements error: ' . $e->getMessage());
            $msg = 'Stok hareketleri yüklenirken bir hata oluştu: ' . $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => $msg
            ], 500);
        }
    }

    /**
     * Grup seçildiğinde görevleri getir
     */
    public function getPositionsByGroup(Request $request) {
        $groupId = $request->get('group_id');
        
        if (!$groupId) {
            return response()->json([]);
        }
        
        $positions = DB::table('employee_positions')
            ->where('group_id', $groupId)
            ->orderBy('name')
            ->get(['id', 'name']);
        
        return response()->json($positions);
    }
    
}
