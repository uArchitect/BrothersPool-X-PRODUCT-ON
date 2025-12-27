<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class QuickController extends Controller
{
    public function add(Request $request)
    {
        try {
            // Input validation ve hazırlık
            $total = floatval(str_replace(' TL', '', $request->total_price));
            $customerId = $request->customer_id;
            $sellerId = $request->seller_id ?? 1; // seller_id field'ını kullan

            // Customer bilgisini doğrula
            $customerInformation = $this->validateCustomer($customerId);

            DB::beginTransaction();

            // Sales kaydı oluştur
            $saleId = $this->createSale($request, $total, $customerId, $customerInformation, $sellerId);

            // Sale items ekle
            $this->createSaleItems($request, $saleId);

            // Transaction kaydı ekle
            $this->createTransaction($customerId, $saleId, $total);

            // Employee commission hesapla ve ekle
            $this->calculateEmployeeCommissions($saleId, $request->services, $request->quantities);

            DB::commit();
            return redirect()->back()->with('success', 'Satış başarıyla tamamlandı');

        } catch (\Exception $e) {
            DB::rollBack();
            dd('Genel hata: ' . $e->getMessage());
        }
    }

    /**
     * Customer bilgisini doğrula
     */
    private function validateCustomer($customerId)
    {
        try {
            $customerInformation = DB::table('customers')->where('id', $customerId)->first();
            if (!$customerInformation) {
                dd('Müşteri bulunamadı. Customer ID: ' . $customerId);
            }
            return $customerInformation;
        } catch (\Exception $e) {
            dd('Müşteri bilgisi alınırken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Sales kaydı oluştur
     */
    private function createSale($request, $total, $customerId, $customerInformation, $sellerId)
    {
        try {
            // Payment method mapping
            $paymentMethodMap = [
                'cash' => ['id' => 1, 'name' => 'Nakit'],
                'credit_card' => ['id' => 2, 'name' => 'Kredi Kartı'],
                'transfer' => ['id' => 3, 'name' => 'Havale'],
                'eft' => ['id' => 4, 'name' => 'EFT']
            ];
            
            $paymentMethod = $request->payment_method ?? 'cash';
            $paymentInfo = $paymentMethodMap[$paymentMethod] ?? $paymentMethodMap['cash'];

            $saleId = DB::table('sales')->insertGetId([
                'date' => Carbon::now(),
                'customer_id' => $customerId,
                'customer' => $customerInformation->first_name . ' ' . $customerInformation->last_name,
                'reservation_id' => $request->reservation_id ?? null,
                'total' => $total,
                'discount_type' => $request->discount_type ?? null,
                'discount_percent' => $request->discount_percent ?? 0,
                'product_discount' => $request->product_discount ?? 0,
                'total_discount' => $request->total_discount ?? 0,
                'product_tax' => $request->product_tax ?? 0,
                'total_tax' => $request->total_tax ?? 0,
                'grand_total' => $total,
                'paid' => $total,
                'invoice_status' => $request->invoice_status ?? 2,
                'sale_status' => $request->sale_status ?? 1,
                'seller_id' => $sellerId,
                'payment_method_id' => $paymentInfo['id'],
                'payment_method' => $paymentInfo['name'],
                'note' => $request->note ?? 'Hızlı satış işlemi',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            if (!$saleId) {
                dd('Sales kaydı eklenemedi. Data: ', [
                    'customer_id' => $customerId,
                    'total' => $total,
                    'customer_name' => $customerInformation->first_name . ' ' . $customerInformation->last_name
                ]);
            }

            return $saleId;
        } catch (\Exception $e) {
            dd('Sales tablosuna ekleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Sale items oluştur
     */
    private function createSaleItems($request, $saleId)
    {
        try {
            foreach ($request->services as $index => $serviceId) {
                // Servis bilgilerini çek
                $service = $this->getServiceInfo($serviceId);
                $quantity = isset($request->quantities[$index]) ? (int)$request->quantities[$index] : 1;

                // Sale item ekle
                $this->insertSaleItem($saleId, $service, $quantity);

                // Stok hareketi (eğer gerekirse)
                $this->handleStockMovement($service, $serviceId, $quantity, $saleId);
            }
        } catch (\Exception $e) {
            dd('Sale items oluşturma hatası: ' . $e->getMessage());
        }
    }

    /**
     * Servis bilgilerini getir
     */
    private function getServiceInfo($serviceId)
    {
        try {
            $service = DB::table('menu_items')->where('id', $serviceId)->first();
            if (!$service) {
                dd('Servis bulunamadı. Service ID: ' . $serviceId);
            }
            return $service;
        } catch (\Exception $e) {
            dd('Servis bilgisi alma hatası. Service ID: ' . $serviceId . ', Hata: ' . $e->getMessage());
        }
    }

    /**
     * Sale item kaydı ekle
     */
    private function insertSaleItem($saleId, $service, $quantity)
    {
        try {
            $unitPrice = $service->total_price;
            $taxRate = $service->tax_rate ?? 0;
            $itemTax = ($unitPrice * $taxRate / 100) * $quantity;
            $netUnitPrice = $unitPrice - ($unitPrice * $taxRate / 100);
            $subtotal = $unitPrice * $quantity;

            $saleItemResult = DB::table('sale_items')->insert([
                'sale_id' => $saleId,
                'service_id' => $service->id,
                'product_name' => $service->name,
                'net_unit_price' => $netUnitPrice,
                'unit_price' => $unitPrice,
                'item_tax' => $itemTax,
                'tax_id' => $service->tax_id ?? null,
                'tax_rate' => $taxRate,
                'discount' => 0,
                'subtotal' => $subtotal,
                'quantity' => $quantity,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            if (!$saleItemResult) {
                dd('Sale item eklenemedi. Data: ', [
                    'sale_id' => $saleId,
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'quantity' => $quantity
                ]);
            }
        } catch (\Exception $e) {
            dd('Sale item ekleme hatası. Service ID: ' . $service->id . ', Hata: ' . $e->getMessage());
        }
    }

    /**
     * Stok hareketini işle
     */
    private function handleStockMovement($service, $serviceId, $quantity, $saleId)
    {
        try {
            if (isset($service->is_stock) && $service->is_stock == 1) {
                $stockMovementResult = DB::table('stock_movements')->insert([
                    'menu_item_id' => $serviceId,
                    'warehouse_id' => 1, // Varsayılan depo
                    'type' => 'out',
                    'quantity' => $quantity,
                    'reference_type' => 'sale',
                    'reference_id' => $saleId,
                    'notes' => 'Hızlı satış işlemi',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                if (!$stockMovementResult) {
                    dd('Stok hareketi eklenemedi. Data: ', [
                        'menu_item_id' => $serviceId,
                        'quantity' => $quantity,
                        'sale_id' => $saleId,
                        'service_name' => $service->name
                    ]);
                }
            }
        } catch (\Exception $e) {
            dd('Stok hareketi ekleme hatası. Service ID: ' . $serviceId . ', Hata: ' . $e->getMessage());
        }
    }

    /**
     * Transaction kaydı oluştur
     */
    private function createTransaction($customerId, $saleId, $total)
    {
        try {
            $transactionResult = DB::table('transactions')->insert([
                'account_id' => 1,
                'customer_id' => $customerId,
                'sale_id' => $saleId,
                'type' => 'Gelir',
                'amount' => $total,
                'description' => 'Hızlı satış ödemesi',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            if (!$transactionResult) {
                dd('Transaction kaydı eklenemedi. Data: ', [
                    'sale_id' => $saleId,
                    'customer_id' => $customerId,
                    'amount' => $total
                ]);
            }

            // Müşteri hareket kaydı ekle
            DB::table('customers_account_transactions')->insert([
                'customer_id' => $customerId,
                'date' => Carbon::now()->toDateString(),
                'account' => 'Hızlı Satış',
                'type' => 'Gelir',
                'amount' => $total,
                'description' => 'Hızlı satış ödemesi',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            // Müşteri bakiyesini güncelle
            DB::table('customers')
                ->where('id', $customerId)
                ->increment('current_balance', $total);
        } catch (\Exception $e) {
            dd('Transaction ekleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Employee commission hesapla ve ekle
     */
    private function calculateEmployeeCommissions($saleId, $services, $quantities = null, $reservationId = null)
    {
        try {
            // Global commission rate al
            $globalCommissionRate = $this->getGlobalCommissionRate();

            foreach ($services as $index => $serviceId) {
                $service = $this->getServiceInfo($serviceId);
                $quantity = isset($quantities[$index]) ? (int)$quantities[$index] : 1;
                $servicePrice = $service->total_price * $quantity;

                if ($service->is_stock == 1) {
                    // Stock item için global commission
                    $this->createStockItemCommissions($saleId, $serviceId, $servicePrice, $globalCommissionRate, $reservationId);
                } else {
                    // Service için özel commission
                    $this->createServiceSpecificCommissions($saleId, $serviceId, $servicePrice, $reservationId);
                }
            }
        } catch (\Exception $e) {
            dd('Employee commission hesaplama genel hatası: ' . $e->getMessage());
        }
    }

    /**
     * Global commission rate getir
     */
    private function getGlobalCommissionRate()
    {
        try {
            $settingsRecord = DB::table('settings')->first();
            if (!$settingsRecord) {
                dd('Settings tablosunda kayıt bulunamadı');
            }
            return $settingsRecord && isset($settingsRecord->employee_commission)
                ? floatval($settingsRecord->employee_commission)
                : 0;
        } catch (\Exception $e) {
            dd('Settings tablosundan employee_commission alınırken hata: ' . $e->getMessage());
        }
    }

    /**
     * Stock item için commission oluştur
     */
    private function createStockItemCommissions($saleId, $serviceId, $servicePrice, $globalCommissionRate, $reservationId)
    {
        try {
            $activeEmployees = DB::table('employees')
                ->where('is_active', 1)
                ->get();

            foreach ($activeEmployees as $employee) {
                $commissionAmount = ($globalCommissionRate / 100) * $servicePrice;

                $commissionResult = DB::table('employee_commissions')->insert([
                    'sale_id' => $saleId,
                    'reservation_id' => $reservationId,
                    'employee_id' => $employee->id,
                    'service_id' => $serviceId,
                    'amount' => $commissionAmount,
                    'status' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                if (!$commissionResult) {
                    dd('Employee commission eklenemedi (stock item). Data: ', [
                        'sale_id' => $saleId,
                        'employee_id' => $employee->id,
                        'service_id' => $serviceId,
                        'amount' => $commissionAmount
                    ]);
                }
            }
        } catch (\Exception $e) {
            dd('Stock item commission ekleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Service specific commission oluştur
     */
    private function createServiceSpecificCommissions($saleId, $serviceId, $servicePrice, $reservationId)
    {
        try {
            $employeeCommissions = DB::table('employee_service_commissions')
                ->where('service_id', $serviceId)
                ->get();

            foreach ($employeeCommissions as $empComm) {
                $commissionRate = floatval($empComm->commission_rate);
                $commissionAmount = ($commissionRate / 100) * $servicePrice;

                $commissionResult = DB::table('employee_commissions')->insert([
                    'sale_id' => $saleId,
                    'reservation_id' => $reservationId,
                    'employee_id' => $empComm->employee_id,
                    'service_id' => $serviceId,
                    'amount' => $commissionAmount,
                    'status' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                if (!$commissionResult) {
                    dd('Employee commission eklenemedi (service specific). Data: ', [
                        'sale_id' => $saleId,
                        'employee_id' => $empComm->employee_id,
                        'service_id' => $serviceId,
                        'amount' => $commissionAmount,
                        'rate' => $commissionRate
                    ]);
                }
            }
        } catch (\Exception $e) {
            dd('Service specific commission ekleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Commission ödemelerini işaretle
     */
    public function markCommissionsPaid($saleId)
    {
        try {
            $result = DB::table('employee_commissions')
                ->where('sale_id', $saleId)
                ->update([
                    'status' => 1,
                    'updated_at' => Carbon::now()
                ]);

            if (!$result) {
                dd('Commission ödeme durumu güncellenemedi. Sale ID: ' . $saleId);
            }

            return $result;
        } catch (\Exception $e) {
            dd('Commission paid marking hatası: ' . $e->getMessage());
        }
    }

    /**
     * Employee için ödenmemiş commission'ları getir
     */
    public function getUnpaidCommissions($employeeId)
    {
        try {
            return DB::table('employee_commissions')
                ->join('menu_items', 'employee_commissions.service_id', '=', 'services.id')
                ->join('sales', 'employee_commissions.sale_id', '=', 'sales.id')
                ->where('employee_commissions.employee_id', $employeeId)
                ->where('employee_commissions.status', 0)
                ->select(
                    'employee_commissions.*',
                    'services.name as service_name',
                    'sales.date as sale_date',
                    'sales.customer'
                )
                ->get();
        } catch (\Exception $e) {
            dd('Unpaid commission sorgulama hatası: ' . $e->getMessage());
        }
    }
}
