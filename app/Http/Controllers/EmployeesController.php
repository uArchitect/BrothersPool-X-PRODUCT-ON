<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class EmployeesController extends Controller
{
    private function uploadLogo($logo)
    {
        try {
            $uploadPath = public_path('uploads/employees');
            
            // Klasör yoksa oluştur
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Dosya geçerli mi kontrol et
            if (!$logo->isValid()) {
                throw new \Exception('Geçersiz dosya yüklendi.');
            }
            
            // Dosya boyutu kontrolü (5MB)
            if ($logo->getSize() > 5 * 1024 * 1024) {
                throw new \Exception('Dosya boyutu 5MB\'dan büyük olamaz.');
            }
            
            // Dosya uzantısı kontrolü
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $extension = strtolower($logo->getClientOriginalExtension());
            if (!in_array($extension, $allowedExtensions)) {
                throw new \Exception('Sadece JPG, JPEG, PNG ve GIF dosyaları yüklenebilir.');
            }
            
            $logoName = time() . '_' . uniqid() . '.' . $extension;
            $logo->move($uploadPath, $logoName);
            
            return 'uploads/employees/' . $logoName;
        } catch (\Exception $e) {
            Log::error('Avatar yükleme hatası: ' . $e->getMessage());
            throw $e;
        }
    }

    private function handleTableAssignment($data, $employeeId = null)
    {
        if (!isset($data['table_id']) || !$data['table_id']) {
            return;
        }

        // Eski table_id'yi temizle (sadece update işleminde)
        if ($employeeId) {
            $oldEmployee = DB::table('employees')->where('id', $employeeId)->first();
            if ($oldEmployee && $oldEmployee->table_id) {
                DB::table('tables')->where('id', $oldEmployee->table_id)->update(['employee_id' => null]);
            }
        }

        // Yeni table_id'yi güncelle
        DB::table('tables')->where('id', $data['table_id'])->update(['employee_id' => $employeeId]);
    }

    private function redirectWithMessage($type, $message)
    {
        return redirect()->back()->with($type, $message);
    }

    public function add(Request $request)
    {
        // HR alanları için validasyon
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:employees,name',
            'phone' => ['required', 'regex:/^[0-9]{10,11}$/', 'unique:employees,phone'],
            'tc_no' => ['required', 'string', 'regex:/^[0-9]{11}$/', 'unique:employees,tc_no'],
            'sgk_no' => 'required|string|max:20|unique:employees,sgk_no',
            'hire_date' => 'required|date|before_or_equal:today',
            'group_id' => 'required|exists:employee_groups,id',
            'position_id' => [
                'required',
                'exists:employee_positions,id',
                function ($attribute, $value, $fail) use ($request) {
                    $position = DB::table('employee_positions')
                        ->where('id', $value)
                        ->where('group_id', $request->group_id)
                        ->first();
                    if (!$position) {
                        $fail('Seçilen görev, seçilen gruba ait değil!');
                    }
                }
            ],
            'payment_frequency' => 'required|in:daily,weekly,monthly,hourly',
            'working_days_per_month' => 'required|integer|min:1|max:31',
            'monthly_salary' => 'required_if:payment_frequency,monthly|nullable|numeric|min:0',
            'weekly_wage' => 'required_if:payment_frequency,weekly|nullable|numeric|min:0',
            'daily_wage' => 'required_if:payment_frequency,daily|nullable|numeric|min:0',
            'hourly_wage' => 'required_if:payment_frequency,hourly|nullable|numeric|min:0',
            'iban' => ['required', 'string', 'max:255', 'unique:employees,iban'],
            'bank_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'skills' => 'nullable|integer|exists:categories,id',
            'table_id' => 'nullable|integer|exists:tables,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Form doğrulama hatası!');
        }

        DB::beginTransaction();
        
        $data = $request->except('_token');
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->uploadLogo($request->file('avatar'));
        }

        $data['created_at'] = now();
        $data['updated_at'] = now();
        
        // Komisyon verisini işle ve $data'dan çıkar
        $commissionData = null;
        if (isset($data['commission']) && is_array($data['commission'])) {
            $commissionData = [
                'employee_id' => null, // employeeId henüz oluşmadı
                'menu_items' => []
            ];
            foreach ($data['commission'] as $serviceId => $commission) {
                if (isset($commission['enabled']) && $commission['enabled'] === 'on') {
                    $commissionData['menu_items'][] = [
                        'service_id' => $serviceId,
                        'commission_rate' => $commission['rate'] ?? 0
                    ];
                }
            }
            unset($data['commission']); // employees tablosuna kaydedilmesin
        }
        
        $employeeId = DB::table('employees')->insertGetId($data);
        
        // Komisyonları kaydet (varsa)
        if ($commissionData) {
            $commissionData['employee_id'] = $employeeId;
            $this->addEmployeeServiceComission($commissionData);
        }
        
        // Handle table assignment
        if (isset($data['table_id']) && $data['table_id']) {
            $this->handleTableAssignment($data, $employeeId);
        }
        
        DB::commit();
        
        // Session mesajını temizle ve yeni mesaj ekle
        session()->forget('success');
        return redirect()->route('employees')->with('success', 'Çalışan başarıyla eklendi');
    }

    public function update(Request $request)
    {
        // HR alanları için validasyon eklendi
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:employees,id',
            'name' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^[0-9]{10,11}$/'],
            'tc_no' => ['required', 'string', 'regex:/^[0-9]{11}$/', \Illuminate\Validation\Rule::unique('employees', 'tc_no')->ignore($request->id)],
            'sgk_no' => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('employees', 'sgk_no')->ignore($request->id)],
            'hire_date' => 'required|date|before_or_equal:today',
            'group_id' => 'required|exists:employee_groups,id',
            'position_id' => [
                'required',
                'exists:employee_positions,id',
                function ($attribute, $value, $fail) use ($request) {
                    $position = DB::table('employee_positions')
                        ->where('id', $value)
                        ->where('group_id', $request->group_id)
                        ->first();
                    if (!$position) {
                        $fail('Seçilen görev, seçilen gruba ait değil!');
                    }
                }
            ],
            'payment_frequency' => 'required|in:daily,weekly,monthly,hourly',
            'working_days_per_month' => 'required|integer|min:1|max:31',
            'monthly_salary' => 'required_if:payment_frequency,monthly|nullable|numeric|min:0',
            'weekly_wage' => 'required_if:payment_frequency,weekly|nullable|numeric|min:0',
            'daily_wage' => 'required_if:payment_frequency,daily|nullable|numeric|min:0',
            'hourly_wage' => 'required_if:payment_frequency,hourly|nullable|numeric|min:0',
            'iban' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('employees', 'iban')->ignore($request->id)],
            'bank_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Form doğrulama hatası!');
        }

        try {
            $data = $request->except('_token');
            $id = $data['id'];
            unset($data['id']);
            
            // Avatar yükleme işlemi
            if ($request->hasFile('avatar')) {
                try {
                    $data['avatar'] = $this->uploadLogo($request->file('avatar'));
                } catch (\Exception $e) {
                    return $this->redirectWithMessage('error', 'Avatar yüklenirken hata oluştu: ' . $e->getMessage());
                }
            }
            
            $data['updated_at'] = now();
            
            // Komisyon verisini işle ve $data'dan çıkar
            $commissionData = null;
            if (isset($data['commission']) && is_array($data['commission'])) {
                $commissionData = [
                    'employee_id' => $id,
                    'menu_items' => []
                ];
                foreach ($data['commission'] as $serviceId => $commission) {
                    if (isset($commission['enabled']) && $commission['enabled'] === 'on') {
                        $commissionData['menu_items'][] = [
                            'service_id' => $serviceId,
                            'commission_rate' => $commission['rate'] ?? 0
                        ];
                    }
                }
                unset($data['commission']); // employees tablosuna kaydedilmesin
            }

            $this->handleTableAssignment($data, $id);
            DB::table('employees')->where('id', $id)->update($data);

            if ($commissionData) {
                $this->addEmployeeServiceComission($commissionData);
            }

            // Session mesajını temizle ve yeni mesaj ekle
            session()->forget('success');
            return redirect()->route('employees')->with('success', 'Çalışan başarıyla güncellendi');
        } catch (\Exception $e) {
            Log::error('Çalışan güncelleme hatası: ' . $e->getMessage());
            return $this->redirectWithMessage('error', 'Çalışan güncellenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function addEmployeeServiceComission($commission)
    {

        $employeeId = $commission['employee_id'] ?? null;
        $services = $commission['menu_items'] ?? [];

        if (!$employeeId) {
            throw new \Exception('Çalışan ID\'si belirtilmedi.');
        }

        DB::table('employee_service_commissions')->where('employee_id', $employeeId)->delete();

        $now = now();
        $insertData = [];
        foreach ($services as $item) {
            if (!isset($item['service_id']) || !isset($item['commission_rate'])) {
                continue;
            }
            $insertData[] = [
                'employee_id' => $employeeId,
                'service_id' => $item['service_id'],
                'commission_rate' => $item['commission_rate'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($insertData)) {
            DB::table('employee_service_commissions')->insert($insertData);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        try {
            // Çalışanın rezervasyonları var mı kontrol et
            


            // Avatar dosyasını sil
            $employee = DB::table('employees')->where('id', $id)->first();
            if ($employee && $employee->avatar) {
                $avatarPath = public_path($employee->avatar);
                if (file_exists($avatarPath)) {
                    unlink($avatarPath);
                }
            }

            

            // Çalışanı sil
            DB::table('employees')->where('id', $id)->delete();
            
            // Session mesajını temizle ve yeni mesaj ekle
            session()->forget('success');
            return $this->redirectWithMessage('success', 'Çalışan başarıyla silindi');
        } catch (\Exception $e) {
            Log::error('Çalışan silme hatası: ' . $e->getMessage());
            return $this->redirectWithMessage('error', 'Çalışan silinirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function addCommission(Request $request)
    {
        $commissionIds = $request->input('commission_ids');

        DB::beginTransaction();

        try {
            // Sadece ödenmemiş ve var olan komisyonları al
            $existingCommissions = DB::table('employee_commissions')
                ->whereIn('sale_id', $commissionIds)
                ->where('status', 0)
                ->get(['id', 'amount', 'employee_id', 'sale_id']);



            if ($existingCommissions->isEmpty()) {
                DB::rollBack();
                return back()->with('error', 'Seçilen komisyonlar bulunamadı veya zaten ödenmiş durumda.');
            }

            $foundIds = $existingCommissions->pluck('id')->toArray();
            $notFoundIds = array_diff($commissionIds, $foundIds);

            if (!empty($notFoundIds)) {
                Log::warning('Commission ödeme - bazı ID\'ler bulunamadı:', [
                    'requested_ids' => $commissionIds,
                    'not_found_ids' => $notFoundIds,
                    'found_ids' => $foundIds
                ]);
            }

            // Komisyonları ödenmiş olarak işaretle
            $updated = DB::table('employee_commissions')
                ->whereIn('id', $foundIds)
                ->update([
                    'status' => 1,
                    'updated_at' => now()
                ]);

            if ($updated === 0) {
                DB::rollBack();
                return back()->with('error', 'Hiçbir komisyon güncellenemedi.');
            }

            $totalAmount = 0;

            foreach ($existingCommissions as $commission) {
                $totalAmount += $commission->amount;

                // Çalışan adı
                $employee = DB::table('employees')->where('id', $commission->employee_id)->first();
                $employeeName = $employee->name ?? 'Bilinmeyen Çalışan';

                // Hizmet adı
                $serviceInfo = DB::table('sale_items')
                    ->where('sale_id', $commission->sale_id)
                    ->where('service_id', $commission->service_id ?? 0)
                    ->first();

                if (!$serviceInfo) {
                    $serviceInfo = DB::table('sale_items')
                        ->where('sale_id', $commission->sale_id)
                        ->first();
                }

                $serviceName = $serviceInfo->product_name ?? 'Komisyon İşlemi';

                $timestamp = now();
                $description = "{$employeeName} - {$serviceName} Komisyonu";
                $expenseTitle = "Komisyon: {$serviceName}";

                // Gider kaydı
                $expenseId = DB::table('expenses')->insertGetId([
                    'date' => $timestamp,
                    'document_number' => 'PRIM-' . $commission->id . '-' . date('Ymd'),
                    'expense_type_id' => 1,
                    'employee_id' => $commission->employee_id,
                    'note' => $description,
                    'total' => $commission->amount,
                    'account_id' => 1,
                    'invoice_photo' => null,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                // Muhasebe kaydı
                DB::table('transactions')->insert([
                    'account_id' => 1,
                    'reservation_id' => null,
                    'customer_id' => null,
                    'payment_id' => null,
                    'sale_id' => $commission->sale_id,
                    'expense_id' => $expenseId,
                    'type' => 'Gider',
                    'amount' => $commission->amount,
                    'description' => $description,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                // Gider kalemi kaydı
                DB::table('expense_items')->insert([
                    'expense_id' => $expenseId,
                    'expense_category_id' => 1,
                    'expense' => $expenseTitle,
                    'amount' => $commission->amount,
                    'quantity' => 1,
                    'total' => $commission->amount,
                    'date' => $timestamp,
                    'description' => 'Satış #' . $commission->sale_id . ' - ' . $serviceName,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }

            Log::info('Commission ödemeleri ve gider kayıtları tamamlandı:', [
                'updated_count' => $updated,
                'total_amount' => $totalAmount,
                'commission_ids' => $foundIds,
                'expense_records_created' => count($existingCommissions)
            ]);

            DB::commit();

            $message = "{$updated} adet komisyon başarıyla ödendi olarak işaretlendi.";
            if (!empty($notFoundIds)) {
                $message .= ' (' . count($notFoundIds) . ' komisyon zaten ödenmiş veya bulunamadı.)';
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Commission ödeme işaretleme hatası:', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Komisyon ödeme işleminde hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    private function processCommission($reservation, $employee)
    {
        $commission = round((float) str_replace(',', '.', $reservation['commission']), 2);
        $description = 'Personel komisyonu: ' . $employee->name;
        $timestamp = now();

        // Employee commission durumunu güncelle
        DB::table('employee_commissions')
            ->where('reservation_id', $reservation['reservationId'])
            ->update(['status' => 1]);

        // Expense kaydı
        $expenseId = DB::table('expenses')->insertGetId([
            'date' => $timestamp,
            'document_number' => 'DOC' . uniqid(),
            'expense_type_id' => 1,
            'employee_id' => $employee->id,
            'note' => $description,
            'total' => $commission,
            'account_id' => 1,
            'invoice_photo' => null,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        // Transaction kaydı
        DB::table('transactions')->insert([
            'account_id' => 1,
            'reservation_id' => $reservation['reservationId'],
            'customer_id' => null,
            'payment_id' => null,
            'sale_id' => null,
            'type' => 'Gider',
            'amount' => $commission,
            'description' => $description,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        // Expense item kaydı
        DB::table('expense_items')->insert([
            'expense_id' => $expenseId,
            'expense_category_id' => 1,
            'expense' => $description,
            'amount' => $commission,
            'quantity' => 1,
            'total' => $commission,
            'date' => $timestamp,
            'description' => $description,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    /**
     * Commission ödemelerini işaretle (AJAX endpoint)
     */
    public function markCommissionsPaid(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'commission_ids' => 'required|array',
                'commission_ids.*' => 'integer|exists:employee_commissions,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz commission ID\'leri: ' . implode(', ', $validator->errors()->all())
                ], 400);
            }

            $commissionIds = $request->input('commission_ids');
            
            DB::beginTransaction();
            
            try {
                // Commission durumlarını güncelle
                $updated = DB::table('employee_commissions')
                    ->whereIn('id', $commissionIds)
                    ->where('status', 0) // Sadece ödenmemiş olanları güncelle
                    ->update([
                        'status' => 1,
                        'updated_at' => now()
                    ]);

                if ($updated === 0) {
                    throw new \Exception('Güncellenecek commission bulunamadı veya zaten ödenmiş.');
                }

                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => $updated . ' adet commission başarıyla ödendi olarak işaretlendi.',
                    'updated_count' => $updated
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Commission ödeme işaretleme hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Commission ödeme işaretleme hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSpecialty(Request $request)
    {
        $id = $request->input('id');
        $deleted = DB::table('categories')->where('id', $id)->delete();
        if ($deleted) {
            // Session mesajını temizle ve yeni mesaj ekle
            session()->forget('success');
            return redirect()->route('employees.specialties')->with('success', 'Uzmanlık alanı başarıyla silindi');
        } else {
            return redirect()->route('employees.specialties')->with('error', 'Silme sırasında hata oluştu');
        }
    }

    public function getSpecialties()
    {
        $categories = DB::table('categories')->get();
        return response()->json($categories);
    }

    public function specialtiesPage() {
        $categories = DB::table('categories')->orderBy('name')->get();
        return view('categories', ['categories' => $categories]);
    }

    public function addSpecialty(Request $request)
    {
        $specialties = $request->input('specialties', []);
        $timestamp = now();
        $data = array_map(function($specialty) use ($timestamp) {
            return [
                'name' => $specialty['name'],
                'description' => $specialty['description'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }, $specialties);
        DB::table('categories')->insert($data);
        // Session mesajını temizle ve yeni mesaj ekle
        session()->forget('success');
        return redirect()->route('employees.specialties')->with('success', 'Uzmanlık alanı başarıyla eklendi');
    }

    public function updateSpecialty(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $description = $request->input('description');
        $updated = DB::table('categories')->where('id', $id)->update([
            'name' => $name,
            'description' => $description,
            'updated_at' => now(),
        ]);
        if ($updated) {
            // Session mesajını temizle ve yeni mesaj ekle
            session()->forget('success');
            return redirect()->route('employees.specialties')->with('success', 'Uzmanlık alanı başarıyla güncellendi');
        } else {
            return redirect()->route('employees.specialties')->with('error', 'Güncelleme sırasında hata oluştu');
        }
    }
}
