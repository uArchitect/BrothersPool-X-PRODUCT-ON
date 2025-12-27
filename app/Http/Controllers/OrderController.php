<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\OrderService;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'nullable|exists:tables,id',
            'waiter_id' => 'nullable|exists:employees,id',
            'order_items' => 'nullable|array',
            'order_items.*.menu_item_id' => 'nullable|exists:menu_items,id',
            'order_items.*.quantity' => 'nullable|integer|min:1',
            'order_items.*.price' => 'nullable|numeric|min:0',
            'order_items.*.notes' => 'nullable|string',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'order_type' => 'nullable|in:dine_in,takeaway,delivery'
        ]);

        try {
            $order = $this->orderService->createOrder($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Sipariş başarıyla oluşturuldu.',
                'data' => ['id' => $order->id, 'order_number' => $order->order_number]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store order from POS system
     */
    public function storeFromPOS(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'table_number' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'total' => 'required|numeric|min:0'
        ]);

        try {
            $orderData = [
                'table_id' => $request->table_id,
                'waiter_id' => 1, // Default waiter
                'subtotal' => $request->subtotal,
                'total' => $request->total,
                'items' => $request->items,
            ];

            $order = $this->orderService->createOrderFromPOS($orderData);

            return response()->json([
                'success' => true,
                'message' => 'Sipariş başarıyla oluşturuldu',
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get orders for a specific table
     */
    public function getTableOrders($tableId)
    {
        try {
            $ordersData = $this->orderService->getTableOrders($tableId);

            return response()->json([
                'success' => true,
                'orders' => $ordersData['orders'],
                'total_orders' => $ordersData['total_orders'],
                'total_items' => $ordersData['total_items'],
                'total_amount' => $ordersData['total_amount'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Siparişler yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all table statuses with order counts
     */
    public function getAllTableStatuses()
    {
        try {
            $tables = DB::table('tables')
                ->where('is_active', 1)
                ->select('id', 'table_number', 'status')
                ->get();

            $tableStatuses = $tables->map(function ($table) {
                $orderCount = DB::table('orders')
                    ->where('table_id', $table->id)
                    ->whereIn('status', ['pending', 'preparing', 'ready'])
                    ->count();

                return [
                    'id' => $table->id,
                    'table_number' => $table->table_number,
                    'status' => $table->status,
                    'order_count' => $orderCount
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $tableStatuses
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Masa durumları yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive table information including active orders
     */
    public function getTableContext($tableId)
    {
        try {
            $tableContext = $this->orderService->getTableContext($tableId);

            return response()->json([
                'success' => true,
                'table' => $tableContext['table'],
                'active_orders' => $tableContext['active_orders'],
                'has_active_orders' => $tableContext['has_active_orders'],
                'total_orders' => $tableContext['total_orders'],
                'total_items' => $tableContext['total_items'],
                'total_amount' => $tableContext['total_amount']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Masa bilgileri yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete all orders for a table
     */
    public function completeTableOrders($tableId)
    {
        try {
            $success = $this->orderService->completeTableOrders($tableId);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Masa başarıyla boşaltıldı' : 'Masa boşaltılırken hata oluştu'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Masa boşaltılırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,served,cancelled',
            'notes' => 'nullable|string',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20'
        ]);

        $order = DB::table('orders')->where('id', $id)->first();
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş bulunamadı.'
            ], 404);
        }

        $data = $request->only(['status', 'notes', 'customer_name', 'customer_phone']);
        $data['updated_at'] = now();

        // If order is served, update table status
        if ($request->status === 'served') {
            DB::table('tables')
                ->where('id', $order->table_id)
                ->update(['status' => 'available', 'updated_at' => now()]);
        }

        DB::table('orders')->where('id', $id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Sipariş başarıyla güncellendi.'
        ]);
    }

    /**
     * Remove the specified order
     */
    public function destroy($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş bulunamadı.'
            ], 404);
        }

        // Only allow deletion of pending or cancelled orders
        if (!in_array($order->status, ['pending', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Sadece bekleyen veya iptal edilmiş siparişler silinebilir.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Delete order items
            DB::table('order_items')->where('order_id', $id)->delete();

            // Delete order
            DB::table('orders')->where('id', $id)->delete();

            // Update table status to available
            DB::table('tables')
                ->where('id', $order->table_id)
                ->update(['status' => 'available', 'updated_at' => now()]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sipariş başarıyla silindi.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Sipariş silinirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,served,cancelled'
        ]);

        try {
            $success = $this->orderService->updateOrderStatus($id, $request->status);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Sipariş durumu başarıyla güncellendi.' : 'Sipariş durumu güncellenirken hata oluştu.',
                'status' => $request->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş durumu güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get orders for API
     */
    public function getOrders(Request $request)
    {
        $query = DB::table('orders')
            ->leftJoin('tables', 'orders.table_id', '=', 'tables.id')
            ->leftJoin('employees', 'orders.waiter_id', '=', 'employees.id')
            ->select(
                'orders.*', 
                'tables.table_number', 
                'employees.name as waiter_name'
            );

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('orders.status', $request->status);
        }

        // Filter by table
        if ($request->has('table_id') && $request->table_id) {
            $query->where('orders.table_id', $request->table_id);
        }

        // Filter by waiter
        if ($request->has('waiter_id') && $request->waiter_id) {
            $query->where('orders.waiter_id', $request->waiter_id);
        }

        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('orders.created_at', $request->date);
        }

        $orders = $query->orderBy('orders.created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get single order with items
     */
    public function getOrder($id)
    {
        $order = DB::table('orders')
            ->leftJoin('tables', 'orders.table_id', '=', 'tables.id')
            ->leftJoin('employees', 'orders.waiter_id', '=', 'employees.id')
            ->select(
                'orders.*', 
                'tables.table_number', 
                'employees.name as waiter_name'
            )
            ->where('orders.id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş bulunamadı.'
            ], 404);
        }

        $orderItems = DB::table('order_items')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->select('order_items.*', 'menu_items.name as item_name', 'menu_items.price')
            ->where('order_items.order_id', $id)
            ->get();

        $order->items = $orderItems;

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Get kitchen orders (pending and preparing) - Optimized with caching
     */
    public function getKitchenOrders()
    {
        // Gerçek mutfak siparişleri verisi
        $orders = DB::table('orders')
            ->join('tables', 'orders.table_id', '=', 'tables.id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->whereIn('orders.status', ['pending', 'preparing', 'ready'])
            ->whereIn('order_items.status', ['pending', 'preparing', 'ready'])
            ->select(
                'orders.id as order_id',
                'orders.status as order_status',
                'orders.created_at as order_time',
                'tables.table_number',
                'order_items.id as item_id',
                'order_items.quantity',
                'order_items.special_instructions as item_notes',
                'order_items.status as item_status',
                'menu_items.name as item_name',
                'menu_items.prep_time',
                'orders.customer_name',
                'orders.notes as order_notes'
            )
            ->orderBy('orders.created_at', 'asc')
            ->orderBy('menu_items.prep_time', 'asc')
            ->get();

        // Convert timestamps to ISO format
        $orders->transform(function ($order) {
            $order->order_time = \Carbon\Carbon::parse($order->order_time)->toISOString();
            return $order;
        });

        return response()->json([
            'success' => true,
            'data' => $orders,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Update order item status
     */
    public function updateOrderItemStatus(Request $request, $orderId, $itemId)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,served'
        ]);

        try {
            $success = $this->orderService->updateOrderItemStatus($orderId, $itemId, $request->status);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sipariş öğesi bulunamadı.'
                ], 404);
            }

            $statusMessages = [
                'pending' => 'Sipariş beklemeye alındı.',
                'preparing' => 'Sipariş hazırlanmaya başlandı.',
                'ready' => 'Sipariş hazır olarak işaretlendi.',
                'served' => 'Sipariş servis edildi.'
            ];

            return response()->json([
                'success' => true,
                'message' => $statusMessages[$request->status],
                'status' => $request->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Durum güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
