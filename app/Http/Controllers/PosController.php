<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function addSale(Request $request)
    {
        
        $productIds = $request->productIds ?? [];
        $quantities = $request->quantities ?? [];
        $products = $this->findProductInformation($productIds);
        $customerId = $request->customerId;
        $reservationId = $request->reservationId;

        DB::beginTransaction();
        try {
            // Generate unique sale number
            $saleNumber = 'SALE-' . str_pad(DB::table('sales')->max('id') + 1, 6, '0', STR_PAD_LEFT);
            
            // 1. SATIŞ KAYDI (sales tablosuna)
            $saleId = DB::table('sales')->insertGetId([
                'sale_number' => $saleNumber,
                'customer_id' => $customerId,
                'subtotal' => $request->cartTotal,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total' => $request->cartTotal,
                'payment_method' => $request->paymentType,
                'status' => 'completed',
                'notes' => 'POS Satış',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. ÖDEME KAYDI (payments tablosuna)
            DB::table('payments')->insert([
                'customer_id' => $customerId,
                'reservation_id' => $reservationId ?? null,
                'payment_amount' => $request->amountPaid,
                'payment_note' => null,
                'payment_method' => $request->paymentType,
                'payment_status' => 1, // ödeme alındı
                'invoice_status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. ÜRÜN DETAYLARI (sale_items tablosuna)
            foreach ($products as $index => $product) {
                $quantity = isset($quantities[$index]) ? (int)$quantities[$index] : 1;
                $subtotal = $product->price * $quantity;
                
                DB::table('sale_items')->insert([
                    'sale_id' => $saleId,
                    'menu_item_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => $subtotal,
                    'notes' => 'POS Satış',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // STOK HAREKETİ - Eğer ürün stoklu ise (is_stock = 1)
                if (isset($product->is_stock) && $product->is_stock == 1) {
                    DB::table('stock_movements')->insert([
                        'menu_item_id' => $product->id,
                        'warehouse_id' => 1, // Varsayılan depo
                        'movement_type' => 'out',
                        'quantity' => $quantity,
                        'unit_cost' => $product->price,
                        'total_cost' => $product->price * $quantity,
                        'reference_type' => 'sale',
                        'reference_id' => $saleId,
                        'notes' => 'POS üzerinden satış',
                        'created_by' => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 4. TRANSACTION (Kasa işlemi)
            DB::table('transactions')->insert([
                'transaction_number' => 'TXN-' . str_pad(DB::table('transactions')->max('id') + 1, 6, '0', STR_PAD_LEFT),
                'account_id' => 1,
                'reference_id' => $saleId,
                'reference_type' => 'sale',
                'type' => 'income',
                'amount' => $request->amountPaid,
                'description' => 'POS üzerinden satış',
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 5. MÜŞTERİ HAREKET KAYDI (Eğer müşteri seçilmişse)
            if ($customerId) {
                DB::table('customers_account_transactions')->insert([
                    'customer_id' => $customerId,
                    'date' => now()->toDateString(),
                    'account' => 'POS Satış',
                    'type' => 'Gelir',
                    'amount' => $request->amountPaid,
                    'description' => 'POS üzerinden satış - ' . $saleNumber,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Müşteri bakiyesini güncelle
                DB::table('customers')
                    ->where('id', $customerId)
                    ->increment('current_balance', $request->amountPaid);
            }

            if ($reservationId) 
            {
                DB::table('reservations')->where('id', $reservationId)->update([
                    'status' => 'completed',
                    'color' => '#3d85c6',
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Satış başarıyla tamamlandı!');

           

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Satış işlemi sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function findProductInformation(array $productIds)
    {
        return DB::table('menu_items')->whereIn('id', $productIds)->get();
    }

    public function findCustomerInformation($customerId)
    {
        return DB::table('customers')->where('id', $customerId)->first();
    }

    // POSSALES için eklenen metodlar

    public function add(Request $request)
    {
        try {
            $requestData = $request->except('_token');
            
            // Validation
            if (empty($requestData['customer_id']) || empty($requestData['menu_items']) || empty($requestData['total_amount'])) {
                return redirect()->back()->with('error', 'Gerekli alanlar eksik!');
            }

            DB::beginTransaction();

            // Generate unique sale number
            $saleNumber = 'SALE-' . str_pad(DB::table('sales')->max('id') + 1, 6, '0', STR_PAD_LEFT);
            
            // 1. Sales kaydı oluştur
            $saleId = DB::table('sales')->insertGetId([
                'sale_number' => $saleNumber,
                'customer_id' => $requestData['customer_id'],
                'subtotal' => $requestData['total_amount'],
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total' => $requestData['total_amount'],
                'payment_method' => $requestData['payment_method'] ?? 'cash',
                'status' => 'completed',
                'notes' => 'POS Satış',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Sale items kayıtları
            foreach ($requestData['menu_items'] as $index => $serviceId) {
                $service = DB::table('menu_items')->find($serviceId);
                if ($service) {
                    $quantity = $requestData['quantities'][$index] ?? 1;
                    $price = $requestData['prices'][$index] ?? $service->total_price;
                    
                    DB::table('sale_items')->insert([
                        'sale_id' => $saleId,
                        'menu_item_id' => $serviceId,
                        'quantity' => $quantity,
                        'unit_price' => $price,
                        'total_price' => $price * $quantity,
                        'notes' => 'POS Satış',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // STOK HAREKETİ - Eğer ürün stoklu ise (is_stock = 1)
                    if (isset($service->is_stock) && $service->is_stock == 1) {
                        DB::table('stock_movements')->insert([
                            'menu_item_id' => $serviceId,
                            'warehouse_id' => $requestData['warehouse_id'] ?? 1, // Varsayılan depo
                            'movement_type' => 'out',
                            'quantity' => $quantity,
                            'unit_cost' => $price,
                            'total_cost' => $price * $quantity,
                            'reference_type' => 'sale',
                            'reference_id' => $saleId,
                            'notes' => 'POS satış işlemi',
                            'created_by' => Auth::id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // 3. Payment kaydı
            DB::table('payments')->insert([
                'customer_id' => $requestData['customer_id'],
                'payment_amount' => $requestData['paid_amount'] ?? $requestData['total_amount'],
                'payment_method' => $requestData['payment_method'] ?? 'cash',
                'payment_status' => 1,
                'invoice_status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Transaction kaydı
            DB::table('transactions')->insert([
                'transaction_number' => 'TXN-' . str_pad(DB::table('transactions')->max('id') + 1, 6, '0', STR_PAD_LEFT),
                'account_id' => $requestData['account_id'] ?? 1,
                'reference_id' => $saleId,
                'reference_type' => 'sale',
                'type' => 'income',
                'amount' => $requestData['paid_amount'] ?? $requestData['total_amount'],
                'description' => 'POS Satış işlemi',
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 5. MÜŞTERİ HAREKET KAYDI (Eğer müşteri seçilmişse)
            if ($requestData['customer_id']) {
                DB::table('customers_account_transactions')->insert([
                    'customer_id' => $requestData['customer_id'],
                    'date' => now()->toDateString(),
                    'account' => 'POS Satış',
                    'type' => 'Gelir',
                    'amount' => $requestData['paid_amount'] ?? $requestData['total_amount'],
                    'description' => 'POS Satış işlemi - ' . $saleNumber,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Müşteri bakiyesini güncelle
                DB::table('customers')
                    ->where('id', $requestData['customer_id'])
                    ->increment('current_balance', $requestData['paid_amount'] ?? $requestData['total_amount']);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Satış başarıyla eklendi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Satış eklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $requestData = $request->except('_token');
            $saleId = $requestData['id'];

            if (!$saleId) {
                return redirect()->back()->with('error', 'Satış ID gerekli!');
            }

            DB::beginTransaction();

            // Önce mevcut stok hareketlerini geri al (sadece bu satışa ait olanları)
            $this->reverseStockMovements($saleId);

            // Sales kaydını güncelle
            DB::table('sales')->where('id', $saleId)->update([
                'customer_id' => $requestData['customer_id'],
                'subtotal' => $requestData['total_amount'],
                'total' => $requestData['total_amount'],
                'payment_method' => $requestData['payment_method'] ?? 'cash',
                'updated_at' => now(),
            ]);

            // Mevcut sale items'ları sil ve yeniden ekle
            DB::table('sale_items')->where('sale_id', $saleId)->delete();
            
            if (isset($requestData['menu_items'])) {
                foreach ($requestData['menu_items'] as $index => $serviceId) {
                    $service = DB::table('menu_items')->find($serviceId);
                    if ($service) {
                        $quantity = $requestData['quantities'][$index] ?? 1;
                        $price = $requestData['prices'][$index] ?? $service->total_price;
                        
                        DB::table('sale_items')->insert([
                            'sale_id' => $saleId,
                            'menu_item_id' => $serviceId,
                            'quantity' => $quantity,
                            'unit_price' => $price,
                            'total_price' => $price * $quantity,
                            'notes' => 'POS Satış Güncelleme',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // STOK HAREKETİ - Eğer ürün stoklu ise (is_stock = 1)
                        if (isset($service->is_stock) && $service->is_stock == 1) {
                            DB::table('stock_movements')->insert([
                                'menu_item_id' => $serviceId,
                                'warehouse_id' => $requestData['warehouse_id'] ?? 1,
                                'type' => 'out',
                                'quantity' => $quantity,
                                'reference_type' => 'sale',
                                'reference_id' => $saleId,
                                'notes' => 'POS satış güncelleme',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Satış başarıyla güncellendi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Satış güncellenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            // Satış kaydının var olup olmadığını kontrol et
            $sale = DB::table('sales')->find($id);
            if (!$sale) {
                return redirect()->back()->with('error', 'Satış kaydı bulunamadı!');
            }

            DB::beginTransaction();

            // Stok hareketlerini geri al
            $this->reverseStockMovements($id);

            // İlişkili kayıtları sil
            DB::table('sale_items')->where('sale_id', $id)->delete();
            DB::table('payments')->where('customer_id', $sale->customer_id)
                ->whereDate('created_at', $sale->date ?? $sale->created_at)
                ->delete();
            DB::table('transactions')->where('sale_id', $id)->delete();
            
            // Ana satış kaydını sil
            DB::table('sales')->where('id', $id)->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Satış başarıyla silindi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Satış silinirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Stok hareketlerini geri al (satış silme/güncelleme için)
     */
    private function reverseStockMovements($saleId)
    {
        // Bu satışa ait stok hareketlerini bul
        $stockMovements = DB::table('stock_movements')
            ->where('reference_type', 'sale')
            ->where('reference_id', $saleId)
            ->get();

        foreach ($stockMovements as $movement) {
            // Ters hareket ekle (out -> in)
            $reverseType = ($movement->movement_type === 'out') ? 'in' : 'out';
            
            DB::table('stock_movements')->insert([
                'menu_item_id' => $movement->menu_item_id,
                'warehouse_id' => $movement->warehouse_id,
                'movement_type' => $reverseType,
                'quantity' => $movement->quantity,
                'unit_cost' => $movement->unit_cost,
                'total_cost' => $movement->total_cost,
                'reference_type' => 'sale_reversal',
                'reference_id' => $saleId,
                'notes' => 'Satış iptali/güncelleme için ters hareket',
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Orijinal hareketleri sil
        DB::table('stock_movements')
            ->where('reference_type', 'sale')
            ->where('reference_id', $saleId)
            ->delete();
    }

    public function getSalesAjax()
    {
        try {
            $sales = DB::table('sales')
                ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
                ->leftJoin('payment_methods', function($join) {
                    $join->on(DB::raw('CAST(sales.payment_method AS UNSIGNED)'), '=', 'payment_methods.id');
                })
                ->select(
                    'sales.*',
                    DB::raw("COALESCE(customers.name, CONCAT(COALESCE(customers.first_name, ''), ' ', COALESCE(customers.last_name, '')), 'Misafir') as customer_name"),
                    'customers.phone as customer_phone',
                    'payment_methods.name as payment_method_name'
                )
                ->orderBy('sales.created_at', 'desc')
                ->get();

            // Her satış için items'ları da al
            foreach ($sales as $sale) {
                $sale->items = DB::table('sale_items')
                    ->leftJoin('menu_items', 'sale_items.menu_item_id', '=', 'menu_items.id')
                    ->select(
                        'sale_items.*',
                        'menu_items.name as menu_item_name'
                    )
                    ->where('sale_items.sale_id', $sale->id)
                    ->get();
            }

            return response()->json([
                'success' => true,
                'data' => $sales
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Satışlar yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }



    public function addCustomer(Request $request)
    {
        $customer = DB::table('customers')->insert([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
        ]);

        return redirect()->back()->with('success', 'Müşteri başarıyla eklendi!');
    }

    public function getCustomer($id)
    {
        $customer = DB::table('customers')->where('id', $id)->first();
        return response()->json($customer);
    }

    public function getCustomers()
    {
        $customers = DB::table('customers')->get();
        return response()->json($customers);
    }

}
