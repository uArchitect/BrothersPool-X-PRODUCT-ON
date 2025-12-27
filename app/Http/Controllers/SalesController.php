<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use App\Models\User;
use App\Models\Sale;
use App\Models\SaleItem;

class SalesController extends Controller
{
    /**
     * Müşteri bilgilerini getir
     */
    public function getCustomer($id)
    {
        try {
            $customer = User::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'first_name' => $customer->first_name ?? $customer->name ?? '',
                    'last_name' => $customer->last_name ?? '',
                    'email' => $customer->email,
                    'phone' => $customer->phone ?? '',
                    'address_line1' => $customer->address_line1 ?? '',
                    'address_line2' => $customer->address_line2 ?? '',
                    'city' => $customer->city ?? '',
                    'state' => $customer->state ?? '',
                    'gender' => $customer->gender ?? '',
                    'date_of_birth' => $customer->date_of_birth ?? '',
                    'is_vip' => $customer->is_vip ?? false,
                    'total_visits' => $customer->total_visits ?? 0,
                    'total_spent' => $customer->total_spent ?? 0,
                    'parapuan' => $customer->parapuan ?? 0,
                    'allergy' => $customer->allergy ?? '',
                    'allergy_note' => $customer->allergy_note ?? '',
                    'preferred_services' => $customer->preferred_services ?? '',
                    'notes' => $customer->notes ?? '',
                    'last_visit' => $customer->last_visit ?? null,
                    'tax_number' => $customer->tax_number ?? '',
                    'tax_office' => $customer->tax_office ?? ''
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Müşteri bulunamadı: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Satış detaylarını getir
     */
    public function getSaleDetails($id)
    {
        try {
            $sale = Sale::with(['customer', 'items.product', 'items.service'])
                ->findOrFail($id);
            
            $items = [];
            foreach ($sale->items as $item) {
                $items[] = [
                    'id' => $item->id,
                    'name' => $item->product ? $item->product->name : ($item->service ? $item->service->name : 'Bilinmeyen'),
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                    'type' => $item->product ? 'product' : 'service'
                ];
            }
            
            return response()->json([
                'success' => true,
                'sale' => [
                    'id' => $sale->id,
                    'invoice_no' => $sale->invoice_no ?? 'INV-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT),
                    'date' => $sale->created_at->format('d.m.Y H:i'),
                    'status' => $sale->status ?? 'Tamamlandı',
                    'uuid' => $sale->e_invoice_uuid ?? '-',
                    'customer' => [
                        'name' => $sale->customer ? ($sale->customer->first_name . ' ' . $sale->customer->last_name) : 'Misafir',
                        'tax_number' => $sale->customer ? ($sale->customer->tax_number ?? 'TCKN') : '-',
                        'address' => $sale->customer ? ($sale->customer->address_line1 ?? 'Adres belirtilmemiş') : '-'
                    ],
                    'items' => $items,
                    'subtotal' => $sale->subtotal ?? 0,
                    'tax_amount' => $sale->tax_amount ?? 0,
                    'discount_amount' => $sale->discount_amount ?? 0,
                    'total_amount' => $sale->total_amount ?? 0,
                    'paid_amount' => $sale->paid_amount ?? 0,
                    'remaining_amount' => ($sale->total_amount ?? 0) - ($sale->paid_amount ?? 0)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Satış detayları alınamadı: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * E-Fatura oluştur
     */
    public function createInvoice(Request $request)
    {
        dd($request->all());
        try {
            $request->validate([
                'sale_id' => 'required|exists:sales,id',
                'invoice_type' => 'required|in:commercial,basic,export,exempt',
                'invoice_scenario' => 'required|in:basic,commercial,export'
            ]);
            
            $sale = Sale::findOrFail($request->sale_id);
            
            // E-Fatura UUID oluştur (gerçek implementasyonda GİB API'si kullanılacak)
            $uuid = 'TR' . date('Ymd') . strtoupper(uniqid());
            
            // Satışı güncelle
            $sale->update([
                'e_invoice_uuid' => $uuid,
                'invoice_type' => $request->invoice_type,
                'invoice_scenario' => $request->invoice_scenario,
                'invoice_status' => 'generated'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'E-Fatura başarıyla oluşturuldu',
                'uuid' => $uuid
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'E-Fatura oluşturulamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Satışı iptal et
     */
    public function cancelSale(Request $request)
    {
        try {
            $request->validate([
                'sale_id' => 'required|exists:sales,id',
                'reason' => 'required|string|max:500'
            ]);
            
            $sale = Sale::findOrFail($request->sale_id);
            
            // Satışı iptal et
            $sale->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->reason,
                'cancelled_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Satış başarıyla iptal edildi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Satış iptal edilemedi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelOrder($id)
    {
        $orderId = $id;
        
        dd($orderId);
    }
}
