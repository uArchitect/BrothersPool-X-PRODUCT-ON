<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\MenuItem;

class StockService extends BaseService
{
    /**
     * Deduct stock when creating a sale
     */
    public function deductStockFromSale(int $saleId, array $items): bool
    {
        return DB::transaction(function () use ($saleId, $items) {
            foreach ($items as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);

                if (!$menuItem || !$menuItem->is_stock) {
                    continue;
                }

                $this->createStockMovement([
                    'menu_item_id' => $item['menu_item_id'],
                    'warehouse_id' => 1, // Default warehouse
                    'movement_type' => 'out',
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_price'],
                    'total_cost' => $item['unit_price'] * $item['quantity'],
                    'reference_type' => 'sale',
                    'reference_id' => $saleId,
                    'notes' => 'Sale stock deduction',
                ]);
            }

            Log::info('Stock deducted from sale', [
                'sale_id' => $saleId,
                'user_id' => Auth::id(),
            ]);

            return true;
        });
    }

    /**
     * Add stock movement
     */
    public function createStockMovement(array $data): int
    {
        return DB::table('stock_movements')->insertGetId([
            'menu_item_id' => $data['menu_item_id'],
            'warehouse_id' => $data['warehouse_id'] ?? 1,
            'movement_type' => $data['movement_type'],
            'quantity' => $data['quantity'],
            'unit_cost' => $data['unit_cost'] ?? null,
            'total_cost' => $data['total_cost'] ?? null,
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Get current stock level for a menu item
     */
    public function getCurrentStock(int $menuItemId, int $warehouseId = null): int
    {
        $query = DB::table('stock_movements')
            ->where('menu_item_id', $menuItemId);

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        $movements = $query->get();

        $totalIn = $movements->whereIn('movement_type', ['in', 'purchase', 'return', 'transfer'])->sum('quantity');
        $totalOut = $movements->whereIn('movement_type', ['out', 'sale', 'waste'])->sum('quantity');

        return $totalIn - $totalOut;
    }

    /**
     * Check if menu item has sufficient stock
     */
    public function hasSufficientStock(int $menuItemId, int $requiredQuantity, int $warehouseId = null): bool
    {
        $currentStock = $this->getCurrentStock($menuItemId, $warehouseId);

        return $currentStock >= $requiredQuantity;
    }

    /**
     * Get low stock products
     */
    public function getLowStockProducts(int $threshold = 5): array
    {
        $menuItems = DB::table('menu_items')
            ->where('is_stock', true)
            ->get();

        $lowStockProducts = [];

        foreach ($menuItems as $item) {
            $currentStock = $this->getCurrentStock($item->id);

            if ($currentStock > 0 && $currentStock <= $threshold) {
                $lowStockProducts[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'current_stock' => $currentStock,
                    'min_stock_level' => $item->min_stock_level ?? 0,
                ];
            }
        }

        return $lowStockProducts;
    }

    /**
     * Calculate stock for all products (optimized version)
     */
    public function calculateAllStock(): array
    {
        $products = DB::table('menu_items')
            ->where('is_stock', true)
            ->get(['id', 'name', 'min_stock_level']);

        $productIds = $products->pluck('id')->toArray();

        if (empty($productIds)) {
            return [];
        }

        $movements = DB::table('stock_movements')
            ->whereIn('menu_item_id', $productIds)
            ->get()
            ->groupBy('menu_item_id');

        $stockData = [];

        foreach ($products as $product) {
            $productMovements = $movements->get($product->id, collect());
            $totalIn = $productMovements->whereIn('movement_type', ['in', 'purchase', 'return', 'transfer'])->sum('quantity');
            $totalOut = $productMovements->whereIn('movement_type', ['out', 'sale', 'waste'])->sum('quantity');
            $currentStock = $totalIn - $totalOut;

            $stockData[] = [
                'id' => $product->id,
                'name' => $product->name,
                'current_stock' => $currentStock,
                'min_stock_level' => $product->min_stock_level ?? 0,
                'is_low_stock' => $currentStock <= ($product->min_stock_level ?? 0),
            ];
        }

        return $stockData;
    }

    /**
     * Reverse stock movements for order cancellation
     */
    public function reverseStockMovements(int $referenceId, string $referenceType = 'sale'): void
    {
        $stockMovements = DB::table('stock_movements')
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->get();

        foreach ($stockMovements as $movement) {
            // Create reverse movement
            $reverseType = ($movement->movement_type === 'out') ? 'in' : 'out';

            $this->createStockMovement([
                'menu_item_id' => $movement->menu_item_id,
                'warehouse_id' => $movement->warehouse_id,
                'movement_type' => $reverseType,
                'quantity' => $movement->quantity,
                'unit_cost' => $movement->unit_cost,
                'total_cost' => $movement->total_cost,
                'reference_type' => $referenceType . '_reversal',
                'reference_id' => $referenceId,
                'notes' => 'Stock reversal for ' . $referenceType . ' cancellation',
            ]);
        }

        // Delete original movements
        DB::table('stock_movements')
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->delete();
    }

    /**
     * Get stock movement history for a menu item
     */
    public function getStockMovementHistory(int $menuItemId, int $limit = 50): array
    {
        return DB::table('stock_movements')
            ->where('menu_item_id', $menuItemId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get warehouse stock summary
     */
    public function getWarehouseStockSummary(int $warehouseId = null): array
    {
        $query = DB::table('stock_movements')
            ->join('menu_items', 'stock_movements.menu_item_id', '=', 'menu_items.id')
            ->select(
                'menu_items.id',
                'menu_items.name',
                DB::raw('SUM(CASE WHEN stock_movements.movement_type IN ("in", "purchase", "return", "transfer") THEN stock_movements.quantity ELSE 0 END) as total_in'),
                DB::raw('SUM(CASE WHEN stock_movements.movement_type IN ("out", "sale", "waste") THEN stock_movements.quantity ELSE 0 END) as total_out'),
                DB::raw('(SUM(CASE WHEN stock_movements.movement_type IN ("in", "purchase", "return", "transfer") THEN stock_movements.quantity ELSE 0 END) - SUM(CASE WHEN stock_movements.movement_type IN ("out", "sale", "waste") THEN stock_movements.quantity ELSE 0 END)) as current_stock')
            )
            ->groupBy('menu_items.id', 'menu_items.name');

        if ($warehouseId) {
            $query->where('stock_movements.warehouse_id', $warehouseId);
        }

        return $query->get()->toArray();
    }
}
