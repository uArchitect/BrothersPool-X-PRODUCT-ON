<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Process payment for an order
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:cash,card,online,wallet,points',
            'amount' => 'required|numeric|min:0.01',
            'customer_id' => 'nullable|exists:customers,id',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Get order details
            $order = DB::table('orders')->where('id', $request->order_id)->first();
            if (!$order) {
                throw new \Exception('Sipariş bulunamadı');
            }

            // Check if order is already paid
            if ($order->payment_status === 'paid') {
                throw new \Exception('Bu sipariş zaten ödenmiş');
            }

            // Validate payment amount
            if ($request->amount < $order->total_amount) {
                throw new \Exception('Ödeme tutarı sipariş tutarından az olamaz');
            }
            
            // Calculate change amount
            $changeAmount = $request->amount - $order->total_amount;
 
            // Create sale record
            $saleNumber = 'SALE-' . str_pad(DB::table('sales')->max('id') + 1, 4, '0', STR_PAD_LEFT);
            $saleId = DB::table('sales')->insertGetId([
                'sale_number' => $saleNumber,
                'order_id' => $order->id,
                'customer_id' => $request->customer_id,
                'seller_id' => Auth::id() ?? 1, // Current user or default seller
                'subtotal' => $order->subtotal ?? 0,
                'tax_amount' => $order->tax_amount ?? 0,
                'discount_amount' => $order->discount_amount ?? 0,
                'total' => $order->total_amount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'notes' => $request->notes,
                'sold_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create sale items
            $orderItems = DB::table('order_items')
                ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->where('order_items.order_id', $order->id)
                ->select('order_items.*', 'menu_items.name as item_name')
                ->get();

            foreach ($orderItems as $item) {
                DB::table('sale_items')->insert([
                    'sale_id' => $saleId,
                    'menu_item_id' => $item->menu_item_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'notes' => $item->special_instructions ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Create transaction record
            $transactionNumber = 'TXN-' . str_pad(DB::table('transactions')->max('id') + 1, 4, '0', STR_PAD_LEFT);
            DB::table('transactions')->insert([
                'transaction_number' => $transactionNumber,
                'type' => 'income',
                'account_id' => 1, // Default cash account
                'reference_id' => $saleId,
                'reference_type' => 'sale',
                'amount' => $order->total_amount,
                'description' => "Sipariş #{$order->order_number} ödemesi",
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update order status
            DB::table('orders')->where('id', $order->id)->update([
                'payment_status' => 'paid',
                'status' => 'served',
                'served_at' => now(),
                'updated_at' => now()
            ]);

            // Update order items status
            DB::table('order_items')->where('order_id', $order->id)->update([
                'status' => 'served',
                'updated_at' => now()
            ]);

            // Update table status to available
            DB::table('tables')->where('id', $order->table_id)->update([
                'status' => 'available',
                'updated_at' => now()
            ]);

            // Update customer stats if customer exists
            if ($request->customer_id) {
                DB::table('customers')->where('id', $request->customer_id)->increment('order_count');
                DB::table('customers')->where('id', $request->customer_id)->increment('total_spent', $order->total_amount);
                DB::table('customers')->where('id', $request->customer_id)->update([
                    'last_order_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ödeme başarıyla işlendi',
                'sale_id' => $saleId,
                'sale_number' => $saleNumber,
                'transaction_number' => $transactionNumber,
                'change_amount' => $changeAmount,
                'total_amount' => $order->total_amount,
                'paid_amount' => $request->amount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Ödeme işlenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment methods
     */
    public function getPaymentMethods()
    {
        try {
            $paymentMethods = DB::table('payment_methods')
                ->where('is_active', true)
                ->select('id', 'name', 'type', 'description')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $paymentMethods
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get payment methods: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Ödeme yöntemleri yüklenirken hata oluştu'
            ], 500);
        }
    }

    /**
     * Get sales summary for a table
     */
    public function getTableSalesSummary($tableId)
    {
        try {
            $sales = DB::table('sales')
                ->join('orders', 'sales.order_id', '=', 'orders.id')
                ->where('orders.table_id', $tableId)
                ->where('sales.status', 'completed')
                ->whereDate('sales.created_at', today())
                ->select(
                    'sales.*',
                    'orders.order_number',
                    'orders.order_time'
                )
                ->orderBy('sales.created_at', 'desc')
                ->get();

            $totalSales = $sales->sum('total');
            $totalOrders = $sales->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'sales' => $sales,
                    'summary' => [
                        'total_sales' => $totalSales,
                        'total_orders' => $totalOrders,
                        'average_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get table sales summary: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Satış özeti yüklenirken hata oluştu'
            ], 500);
        }
    }
}