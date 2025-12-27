<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\StockService;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockService $stockService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stockService = app(StockService::class);
    }

    /** @test */
    public function it_can_deduct_stock_from_sale()
    {
        $menuItem = MenuItem::factory()->create([
            'is_stock' => true,
            'stock_quantity' => 100,
        ]);

        $saleId = 123;
        $items = [
            [
                'menu_item_id' => $menuItem->id,
                'quantity' => 5,
                'unit_price' => 10.00,
            ]
        ];

        $success = $this->stockService->deductStockFromSale($saleId, $items);

        $this->assertTrue($success);

        // Check that stock movement was created
        $movements = DB::table('stock_movements')
            ->where('menu_item_id', $menuItem->id)
            ->where('reference_type', 'sale')
            ->where('reference_id', $saleId)
            ->get();

        $this->assertEquals(1, $movements->count());
        $this->assertEquals('out', $movements->first()->movement_type);
        $this->assertEquals(5, $movements->first()->quantity);
    }

    /** @test */
    public function it_can_calculate_current_stock()
    {
        $menuItem = MenuItem::factory()->create();

        // Add initial stock
        DB::table('stock_movements')->insert([
            'menu_item_id' => $menuItem->id,
            'movement_type' => 'in',
            'quantity' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Deduct some stock
        DB::table('stock_movements')->insert([
            'menu_item_id' => $menuItem->id,
            'movement_type' => 'out',
            'quantity' => 25,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $currentStock = $this->stockService->getCurrentStock($menuItem->id);

        $this->assertEquals(75, $currentStock);
    }

    /** @test */
    public function it_can_check_sufficient_stock()
    {
        $menuItem = MenuItem::factory()->create();

        // Add stock
        DB::table('stock_movements')->insert([
            'menu_item_id' => $menuItem->id,
            'movement_type' => 'in',
            'quantity' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Check sufficient stock
        $this->assertTrue($this->stockService->hasSufficientStock($menuItem->id, 25));
        $this->assertFalse($this->stockService->hasSufficientStock($menuItem->id, 75));
    }

    /** @test */
    public function it_can_get_low_stock_products()
    {
        // Create menu items with different stock levels
        $item1 = MenuItem::factory()->create(['is_stock' => true]);
        $item2 = MenuItem::factory()->create(['is_stock' => true]);
        $item3 = MenuItem::factory()->create(['is_stock' => true]);

        // Set up stock movements
        DB::table('stock_movements')->insert([
            ['menu_item_id' => $item1->id, 'movement_type' => 'in', 'quantity' => 100, 'created_at' => now(), 'updated_at' => now()],
            ['menu_item_id' => $item1->id, 'movement_type' => 'out', 'quantity' => 95, 'created_at' => now(), 'updated_at' => now()], // 5 left
            ['menu_item_id' => $item2->id, 'movement_type' => 'in', 'quantity' => 100, 'created_at' => now(), 'updated_at' => now()],
            ['menu_item_id' => $item2->id, 'movement_type' => 'out', 'quantity' => 98, 'created_at' => now(), 'updated_at' => now()], // 2 left
            ['menu_item_id' => $item3->id, 'movement_type' => 'in', 'quantity' => 100, 'created_at' => now(), 'updated_at' => now()],
            ['menu_item_id' => $item3->id, 'movement_type' => 'out', 'quantity' => 50, 'created_at' => now(), 'updated_at' => now()], // 50 left (not low)
        ]);

        $lowStockProducts = $this->stockService->getLowStockProducts(10);

        $this->assertCount(2, $lowStockProducts);
        $this->assertEquals($item1->id, $lowStockProducts[0]['id']);
        $this->assertEquals(5, $lowStockProducts[0]['current_stock']);
        $this->assertEquals($item2->id, $lowStockProducts[1]['id']);
        $this->assertEquals(2, $lowStockProducts[1]['current_stock']);
    }

    /** @test */
    public function it_can_reverse_stock_movements()
    {
        $menuItem = MenuItem::factory()->create();

        // Create original movement
        DB::table('stock_movements')->insert([
            'menu_item_id' => $menuItem->id,
            'movement_type' => 'out',
            'quantity' => 10,
            'reference_type' => 'sale',
            'reference_id' => 123,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->stockService->reverseStockMovements(123, 'sale');

        // Check that original movement is gone and reverse movement exists
        $originalMovements = DB::table('stock_movements')
            ->where('reference_type', 'sale')
            ->where('reference_id', 123)
            ->get();

        $this->assertEquals(0, $originalMovements->count());

        // Check reverse movement exists
        $reverseMovements = DB::table('stock_movements')
            ->where('reference_type', 'sale_reversal')
            ->where('reference_id', 123)
            ->get();

        $this->assertEquals(1, $reverseMovements->count());
        $this->assertEquals('in', $reverseMovements->first()->movement_type);
        $this->assertEquals(10, $reverseMovements->first()->quantity);
    }

    /** @test */
    public function it_can_calculate_all_stock()
    {
        $item1 = MenuItem::factory()->create(['is_stock' => true]);
        $item2 = MenuItem::factory()->create(['is_stock' => true]);

        // Set up stock movements for item1
        DB::table('stock_movements')->insert([
            ['menu_item_id' => $item1->id, 'movement_type' => 'in', 'quantity' => 100, 'created_at' => now(), 'updated_at' => now()],
            ['menu_item_id' => $item1->id, 'movement_type' => 'out', 'quantity' => 25, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Set up stock movements for item2
        DB::table('stock_movements')->insert([
            ['menu_item_id' => $item2->id, 'movement_type' => 'in', 'quantity' => 50, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $stockData = $this->stockService->calculateAllStock();

        $this->assertCount(2, $stockData);

        // Find item1 data
        $item1Data = collect($stockData)->firstWhere('id', $item1->id);
        $this->assertEquals(75, $item1Data['current_stock']);
        $this->assertFalse($item1Data['is_low_stock']);

        // Find item2 data
        $item2Data = collect($stockData)->firstWhere('id', $item2->id);
        $this->assertEquals(50, $item2Data['current_stock']);
        $this->assertFalse($item2Data['is_low_stock']);
    }
}