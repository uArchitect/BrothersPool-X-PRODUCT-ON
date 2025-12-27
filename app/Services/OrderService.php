<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;

class OrderService extends BaseService
{
    /**
     * Create a new order from POS system
     */
    public function createOrderFromPOS(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Update table status to occupied
            if ($data['table_id']) {
                $this->updateTableStatus($data['table_id'], 'occupied');
            }

            // Generate order number
            $orderNumber = 'ORD-' . str_pad(Order::max('id') + 1, 3, '0', STR_PAD_LEFT);

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'order_source' => 'dine_in',
                'table_id' => $data['table_id'],
                'waiter_id' => $data['waiter_id'] ?? 1,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'status' => 'pending',
                'order_time' => now(),
                'notes' => $data['notes'] ?? null,
                'subtotal' => $data['subtotal'],
                'tax_amount' => $data['total'] - $data['subtotal'],
                'total_amount' => $data['total'],
                'payment_status' => 'pending',
                'priority' => 'normal',
            ]);

            // Add order items
            $this->addOrderItems($order, $data['items']);

            Log::info('Order created from POS', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'table_id' => $data['table_id'],
                'user_id' => Auth::id(),
            ]);

            return $order;
        });
    }

    /**
     * Create a new order
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Update table status if provided
            if (isset($data['table_id'])) {
                $this->updateTableStatus($data['table_id'], 'occupied');
            }

            // Generate order number
            $orderNumber = 'ORD-' . str_pad(Order::max('id') + 1, 3, '0', STR_PAD_LEFT);

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'order_source' => $data['order_type'] ?? 'dine_in',
                'table_id' => $data['table_id'] ?? null,
                'waiter_id' => $data['waiter_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'customer_address' => $data['customer_address'] ?? null,
                'customer_city' => $data['customer_city'] ?? null,
                'customer_postal_code' => $data['customer_postal_code'] ?? null,
                'status' => 'pending',
                'order_time' => now(),
                'notes' => $data['notes'] ?? null,
                'subtotal' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
                'payment_status' => 'pending',
                'priority' => 'normal',
            ]);

            // Add order items and calculate totals
            $subtotal = $this->addOrderItems($order, $data['order_items'] ?? []);

            // Calculate tax and total (18% KDV)
            $taxAmount = $subtotal * 0.18;
            $totalAmount = $subtotal + $taxAmount;

            // Update order with calculated totals
            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);

            Log::info('Order created', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'table_id' => $data['table_id'] ?? null,
                'user_id' => Auth::id(),
            ]);

            return $order;
        });
    }

    /**
     * Add items to an order
     */
    private function addOrderItems(Order $order, array $items): float
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $menuItem = DB::table('menu_items')->find($item['menu_item_id']);
            if (!$menuItem) {
                continue;
            }

            $unitPrice = $item['price'] ?? $menuItem->price;
            $totalPrice = $unitPrice * $item['quantity'];
            $subtotal += $totalPrice;

            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'special_instructions' => $item['notes'] ?? null,
                'status' => 'pending',
            ]);
        }

        return $subtotal;
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(int $orderId, string $status, string $notes = null): bool
    {
        return DB::transaction(function () use ($orderId, $status, $notes) {
            $order = Order::find($orderId);
            if (!$order) {
                throw new \Exception('Order not found');
            }

            // Update order status
            $updated = $order->update([
                'status' => $status,
                'updated_at' => now(),
            ]);

            if ($updated) {
                // Update table status based on order status
                $this->updateTableStatusFromOrder($order);

                // Log status change
                Log::info('Order status updated', [
                    'order_id' => $orderId,
                    'old_status' => $order->status,
                    'new_status' => $status,
                    'user_id' => Auth::id(),
                ]);
            }

            return $updated;
        });
    }

    /**
     * Update order item status
     */
    public function updateOrderItemStatus(int $orderId, int $itemId, string $status): bool
    {
        return DB::transaction(function () use ($orderId, $itemId, $status) {
            // Update order item status
            $updated = DB::table('order_items')
                ->where('id', $itemId)
                ->where('order_id', $orderId)
                ->update([
                    'status' => $status,
                    'updated_at' => now()
                ]);

            if (!$updated) {
                throw new \Exception('Order item not found');
            }

            // Update order status if all items are ready
            if ($status === 'ready') {
                $pendingItems = DB::table('order_items')
                    ->where('order_id', $orderId)
                    ->where('status', '!=', 'ready')
                    ->where('status', '!=', 'served')
                    ->count();

                if ($pendingItems === 0) {
                    DB::table('orders')
                        ->where('id', $orderId)
                        ->update([
                            'status' => 'ready',
                            'updated_at' => now()
                        ]);
                }
            }

            return true;
        });
    }

    /**
     * Update table status based on order status
     */
    private function updateTableStatusFromOrder(Order $order): void
    {
        if (!$order->table_id) {
            return;
        }

        $newTableStatus = match($order->status) {
            'served' => 'available',
            'cancelled' => 'available',
            default => 'occupied',
        };

        $this->updateTableStatus($order->table_id, $newTableStatus);
    }

    /**
     * Update table status
     */
    private function updateTableStatus(int $tableId, string $status): void
    {
        DB::table('tables')->where('id', $tableId)->update([
            'status' => $status,
            'updated_at' => now(),
        ]);
    }

    /**
     * Complete all orders for a table
     */
    public function completeTableOrders(int $tableId): bool
    {
        return DB::transaction(function () use ($tableId) {
            // Update all pending orders for this table to served
            $updatedOrders = DB::table('orders')
                ->where('table_id', $tableId)
                ->whereIn('status', ['pending', 'preparing', 'ready'])
                ->update([
                    'status' => 'served',
                    'updated_at' => now(),
                ]);

            // Update all order items to served
            DB::table('order_items')
                ->whereIn('order_id', function($query) use ($tableId) {
                    $query->select('id')
                        ->from('orders')
                        ->where('table_id', $tableId);
                })
                ->update([
                    'status' => 'served',
                    'updated_at' => now(),
                ]);

            // Update table status to available
            $this->updateTableStatus($tableId, 'available');

            Log::info('Table orders completed', [
                'table_id' => $tableId,
                'orders_updated' => $updatedOrders,
                'user_id' => Auth::id(),
            ]);

            return $updatedOrders > 0;
        });
    }

    /**
     * Get orders for a specific table
     */
    public function getTableOrders(int $tableId): array
    {
        $orders = DB::table('orders')
            ->where('table_id', $tableId)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->orderBy('created_at', 'desc')
            ->get();

        $ordersWithItems = $orders->map(function ($order) {
            $items = DB::table('order_items')
                ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->select('order_items.*', 'menu_items.name as item_name')
                ->where('order_items.order_id', $order->id)
                ->get();

            $order->items = $items;
            return $order;
        });

        return [
            'orders' => $ordersWithItems,
            'total_orders' => $orders->count(),
            'total_items' => $ordersWithItems->sum(fn($order) => $order->items->sum('quantity')),
            'total_amount' => $orders->sum('total_amount'),
        ];
    }

    /**
     * Get table context (comprehensive information)
     */
    public function getTableContext(int $tableId): array
    {
        $table = DB::table('tables')->where('id', $tableId)->first();

        if (!$table) {
            throw new \Exception('Table not found');
        }

        $orders = $this->getTableOrders($tableId);

        return [
            'table' => $table,
            'active_orders' => $orders['orders'],
            'has_active_orders' => $orders['total_orders'] > 0,
            'total_orders' => $orders['total_orders'],
            'total_items' => $orders['total_items'],
            'total_amount' => $orders['total_amount'],
        ];
    }
}
