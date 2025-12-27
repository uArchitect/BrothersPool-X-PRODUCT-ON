<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function addWarehouse(Request $request)
    {
        try {
            $data = $request->except('_token');
            
            // Basic validation
            if (empty($data['name'])) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Depo adı zorunludur!']);
                }
                return redirect()->back()->with('error', 'Depo adı zorunludur!');
            }

            // Add default values
            $data['is_active'] = isset($data['is_active']) ? 1 : 0;
            $data['created_at'] = now();
            $data['updated_at'] = now();

            DB::table('warehouses')->insert($data);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Depo başarıyla eklendi']);
            }
            return redirect()->back()->with('success', 'Depo başarıyla eklendi');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Depo eklenirken hata oluştu: ' . $e->getMessage()]);
            }
            return redirect()->back()->with('error', 'Depo eklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function deleteWarehouse($id)
    {
        try {
            // Check if warehouse exists
            $warehouse = DB::table('warehouses')->find($id);
            if (!$warehouse) {
                return redirect()->back()->with('error', 'Depo bulunamadı!');
            }

            // Check if warehouse is being used
            $servicesCount = DB::table('menu_items')->where('warehouse_id', $id)->count();
            if ($servicesCount > 0) {
                return redirect()->back()->with('error', "Bu depo $servicesCount adet ürün/hizmette kullanılıyor. Önce bu ürünlerin depo bilgilerini güncelleyin.");
            }

            DB::table('warehouses')->where('id', $id)->delete();
            return redirect()->back()->with('success', 'Depo başarıyla silindi');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Depo silinirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function updateWarehouse(Request $request)
    {
        try {
            $data = $request->except('_token');
            $warehouseId = $data['warehouse_id'];
            unset($data['warehouse_id']);

            // Basic validation
            if (empty($data['name'])) {
                return redirect()->back()->with('error', 'Depo adı zorunludur!');
            }

            // Check if warehouse exists
            $warehouse = DB::table('warehouses')->find($warehouseId);
            if (!$warehouse) {
                return redirect()->back()->with('error', 'Depo bulunamadı!');
            }

            // Handle checkbox
            $data['is_active'] = isset($data['is_active']) ? 1 : 0;
            $data['updated_at'] = now();

            $updated = DB::table('warehouses')->where('id', $warehouseId)->update($data);
            
            if ($updated !== false) {
                return redirect()->back()->with('success', 'Depo başarıyla güncellendi');
            } else {
                return redirect()->back()->with('error', 'Güncelleme yapılacak bir değişiklik bulunamadı');
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Depo güncellenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function getWarehouses()
    {
        try {
            $warehouses = DB::table('warehouses')
                ->leftJoin('employees', 'warehouses.manager', '=', 'employees.id')
                ->select('warehouses.*', 'employees.name as manager_name')
                ->orderBy('warehouses.name')
                ->get();

            return response()->json(['data' => $warehouses]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Depolar yüklenirken hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function getWarehouseDetails($id)
    {
        try {
            // Warehouse bilgilerini al
            $warehouse = DB::table('warehouses')
                ->leftJoin('employees', 'warehouses.manager', '=', 'employees.id')
                ->select('warehouses.*', 'employees.name as manager_name')
                ->where('warehouses.id', $id)
                ->first();

            if (!$warehouse) {
                return response()->json(['error' => 'Depo bulunamadı'], 404);
            }

            // O depoya ait services'ları al
            $services = DB::table('menu_items')
                ->leftJoin('categories', 'services.category_id', '=', 'categories.id')
                ->leftJoin('units', 'services.unit_id', '=', 'units.id')
                ->select([
                    'services.id',
                    'services.name',
                    'services.code',
                    'services.description',
                    'services.price',
                    'services.total_price',
                    'services.is_active',
                    'services.is_stock',
                    'services.stock',
                    'services.warehouse_id',
                    'categories.name as category_name',
                    'units.name as unit_name'
                ])
                ->where('services.warehouse_id', $id)
                ->orderBy('services.name')
                ->get();

            // İstatistikleri hesapla
            $totalProducts = $services->count();
            $activeProducts = $services->where('is_active', 1)->count();
            $totalStock = $services->where('is_stock', 1)->sum('stock'); // Toplam stok miktarı
            $totalValue = $services->where('is_active', 1)->sum('total_price');

            return response()->json([
                'success' => true,
                'warehouse' => $warehouse,
                'menu_items' => $services,
                'statistics' => [
                    'total_products' => $totalProducts,
                    'active_products' => $activeProducts,
                    'total_stock' => $totalStock,
                    'total_value' => $totalValue
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Depo detayları yüklenirken hata oluştu: ' . $e->getMessage()], 500);
        }
    }
}
