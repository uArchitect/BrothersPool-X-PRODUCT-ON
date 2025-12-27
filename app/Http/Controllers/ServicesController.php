<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicesController extends Controller
{
    /**
     * Ortak: is_active ve is_stock alanlarını normalize et
     */
    private function normalizeStatusFields(array &$data)
    {
        $data['is_active'] = !empty($data['is_active']) && ($data['is_active'] === 'on' || $data['is_active'] == 1) ? 1 : 0;
        $data['is_stock'] = !empty($data['is_stock']) && ($data['is_stock'] === 'on' || $data['is_stock'] == 1) ? 1 : 0;
    }

    /**
     * Ortak: Stoklu ürün için depo kontrolü
     */
    private function validateWarehouse($data)
    {
        if (!empty($data['is_stock']) && empty($data['warehouse_id'])) {
            return response()->json(['message' => 'Stoklu ürünler için depo seçimi zorunludur!'], 422);
        }
        return null;
    }

    /**
     * Ortak: warehouse_id'yi stoksuz ürünlerde null yap
     */
    private function handleWarehouseId(array &$data)
    {
        if (empty($data['is_stock'])) {
            $data['warehouse_id'] = null;
        }
    }

    /**
     * Ortak: Servis + depo bilgisi getir
     */
    private function getServiceWithWarehouse($id)
    {
        return DB::table('menu_items')
            ->leftJoin('warehouses', 'services.warehouse_id', '=', 'warehouses.id')
            ->select('services.*', 'warehouses.name as warehouse_name')
            ->where('services.id', $id)
            ->first();
    }

    public function update(Request $request)
    {
        try {
            $data = $request->all();
            $this->normalizeStatusFields($data);
            unset($data['_token']);
            $id = $data['id'];
            unset($data['id']);

            $currentService = DB::table('menu_items')->where('id', $id)->first();

            // is_stock 1'den 0'a çekildiyse stok hareketlerini sil
            if ($currentService && $currentService->is_stock == 1 && empty($data['is_stock'])) {
                DB::table('stock_movements')->where('menu_item_id', $id)->delete();
            }

            if ($resp = $this->validateWarehouse($data)) {
                return $resp;
            }
            $this->handleWarehouseId($data);

            $updated = DB::table('menu_items')->where('id', $id)->update($data);

            if ($updated !== false) {
                $service = $this->getServiceWithWarehouse($id);
                return response()->json(['message' => 'Hizmet başarıyla güncellendi', 'service' => $service]);
            }
            return response()->json(['message' => 'Hizmet güncellenemedi'], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Hizmet güncellenirken bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $id = $request->id;
            if (!$id) {
                return response()->json(['message' => 'Hizmet ID\'si gerekli'], 422);
            }

            DB::table('stock_movements')->where('menu_item_id', $id)->delete();
            $deleted = DB::table('menu_items')->where('id', $id)->delete();

            if ($deleted) {
                return response()->json(['message' => 'Hizmet ve ilgili stok hareketleri başarıyla silindi']);
            }
            return response()->json(['message' => 'Hizmet bulunamadı veya silinemedi'], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Hizmet silinirken bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function add(Request $request)
    {
        try {
            $data = $request->all();
            $this->normalizeStatusFields($data);

            if ($resp = $this->validateWarehouse($data)) {
                return $resp;
            }
            $this->handleWarehouseId($data);

            unset($data['_token']);

            $id = DB::table('menu_items')->insertGetId($data);
            if ($id) {
                $service = $this->getServiceWithWarehouse($id);
                return response()->json(['message' => 'Hizmet başarıyla eklendi', 'service' => $service], 201);
            }
            return response()->json(['message' => 'Hizmet eklenemedi'], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Hizmet eklenirken bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function addStockMovement(Request $request)
    {
        try {
            $data = $request->all();

            // Validasyon
            if (empty($data['menu_item_id']) || empty($data['type']) || empty($data['quantity'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gerekli alanlar eksik!'
                ], 422);
            }

            if (floatval($data['quantity']) <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Miktar 0\'dan büyük olmalıdır!'
                ], 422);
            }

            $service = DB::table('menu_items')->where('id', $data['menu_item_id'])->first();
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ürün bulunamadı!'
                ], 404);
            }

            $movementData = [
                'menu_item_id' => $data['menu_item_id'],
                'warehouse_id' => $data['warehouse_id'] ?? $service->warehouse_id,
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'reference_type' => 'manual',
                'reference_id' => null,
                'notes' => $data['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $movementId = DB::table('stock_movements')->insertGetId($movementData);

            if ($movementId) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stok hareketi başarıyla kaydedildi!',
                    'movement_id' => $movementId
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Stok hareketi kaydedilemedi!'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stok hareketi kaydedilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
