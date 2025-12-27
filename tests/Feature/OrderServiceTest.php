<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\OrderService;
use App\Models\Order;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = app(OrderService::class);
    }

    /** @test */
    public function it_can_create_an_order_from_pos()
    {
        // Create test data
        $table = Table::factory()->create([
            'status' => 'available',
        ]);

        $menuItem = MenuItem::factory()->create([
            'price' => 25.50,
        ]);

        $orderData = [
            'table_id' => $table->id,
            'waiter_id' => 1,
            'subtotal' => 25.50,
            'total' => 30.10, // 25.50 + 18% tax
            'items' => [
                [
                    'menu_item_id' => $menuItem->id,
                    'quantity' => 1,
                    'unit_price' => 25.50,
                ]
            ],
        ];

        // Create order
        $order = $this->orderService->createOrderFromPOS($orderData);

        // Assertions
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('dine_in', $order->order_source);
        $this->assertEquals($table->id, $order->table_id);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(25.50, $order->subtotal);
        $this->assertEquals(30.10, $order->total_amount);

        // Check that table status was updated
        $table->refresh();
        $this->assertEquals('occupied', $table->status);

        // Check that order items were created
        $this->assertEquals(1, $order->orderItems()->count());
        $orderItem = $order->orderItems()->first();
        $this->assertEquals($menuItem->id, $orderItem->menu_item_id);
        $this->assertEquals(1, $orderItem->quantity);
        $this->assertEquals(25.50, $orderItem->unit_price);
    }

    /** @test */
    public function it_can_update_order_status()
    {
        $order = Order::factory()->create([
            'status' => 'pending',
        ]);

        $success = $this->orderService->updateOrderStatus($order->id, 'preparing');

        $this->assertTrue($success);

        $order->refresh();
        $this->assertEquals('preparing', $order->status);
    }

    /** @test */
    public function it_can_get_table_orders()
    {
        $table = Table::factory()->create();

        // Create some orders for the table
        Order::factory()->create([
            'table_id' => $table->id,
            'status' => 'pending',
            'total_amount' => 50.00,
        ]);

        Order::factory()->create([
            'table_id' => $table->id,
            'status' => 'ready',
            'total_amount' => 30.00,
        ]);

        $ordersData = $this->orderService->getTableOrders($table->id);

        $this->assertEquals(2, $ordersData['total_orders']);
        $this->assertEquals(80.00, $ordersData['total_amount']);
        $this->assertCount(2, $ordersData['orders']);
    }

    /** @test */
    public function it_can_complete_table_orders()
    {
        $table = Table::factory()->create(['status' => 'occupied']);

        $order = Order::factory()->create([
            'table_id' => $table->id,
            'status' => 'ready',
        ]);

        $success = $this->orderService->completeTableOrders($table->id);

        $this->assertTrue($success);

        $order->refresh();
        $this->assertEquals('served', $order->status);

        $table->refresh();
        $this->assertEquals('available', $table->status);
    }

    /** @test */
    public function it_can_get_table_context()
    {
        $table = Table::factory()->create();

        $order = Order::factory()->create([
            'table_id' => $table->id,
            'status' => 'pending',
            'total_amount' => 25.50,
        ]);

        $context = $this->orderService->getTableContext($table->id);

        $this->assertEquals($table->id, $context['table']->id);
        $this->assertTrue($context['has_active_orders']);
        $this->assertEquals(1, $context['total_orders']);
        $this->assertEquals(25.50, $context['total_amount']);
    }

    /** @test */
    public function it_handles_nonexistent_table_gracefully()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Table not found');

        $this->orderService->getTableContext(999);
    }
}