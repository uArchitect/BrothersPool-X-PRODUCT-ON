<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoyaltyController extends Controller
{
    /**
     * Display the loyalty program management page
     */
    public function index()
    {
        $customers = DB::table('customers')
            ->where('parapuan', '>', 0)
            ->orderBy('parapuan', 'desc')
            ->get();

        $loyaltyStats = [
            'total_customers' => DB::table('customers')->count(),
            'loyalty_members' => $customers->count(),
            'total_points' => $customers->sum('parapuan'),
            'average_points' => $customers->count() > 0 ? round($customers->avg('parapuan'), 2) : 0,
            'top_customer' => $customers->first()
        ];

        $loyaltyTransactions = DB::table('loyalty_transactions')
            ->join('customers', 'loyalty_transactions.customer_id', '=', 'customers.id')
            ->select('loyalty_transactions.*', 'customers.name as first_name', 'customers.last_name')
            ->orderBy('loyalty_transactions.created_at', 'desc')
            ->limit(50)
            ->get();

        return view('loyalty.index', compact('customers', 'loyaltyStats', 'loyaltyTransactions'));
    }

    /**
     * Add points to customer
     */
    public function addPoints(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Add points to customer
            DB::table('customers')
                ->where('id', $request->customer_id)
                ->increment('parapuan', $request->points);

            // Record transaction
            DB::table('loyalty_transactions')->insert([
                'customer_id' => $request->customer_id,
                'transaction_type' => 'earned',
                'points' => $request->points,
                'reason' => $request->reason,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Puanlar başarıyla eklendi.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Loyalty add points error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Redeem points from customer
     */
    public function redeemPoints(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:255'
        ]);

        try {
            $customer = DB::table('customers')->where('id', $request->customer_id)->first();
            
            if ($customer->parapuan < $request->points) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yetersiz puan. Mevcut puan: ' . $customer->parapuan
                ], 400);
            }

            DB::beginTransaction();

            // Deduct points from customer
            DB::table('customers')
                ->where('id', $request->customer_id)
                ->decrement('parapuan', $request->points);

            // Record transaction
            DB::table('loyalty_transactions')->insert([
                'customer_id' => $request->customer_id,
                'transaction_type' => 'redeemed',
                'points' => -$request->points,
                'reason' => $request->reason,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Puanlar başarıyla kullanıldı.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Loyalty redeem points error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer loyalty details
     */
    public function getCustomerLoyalty($id)
    {
        $customer = DB::table('customers')
            ->where('id', $id)
            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Müşteri bulunamadı.'
            ], 404);
        }

        $transactions = DB::table('loyalty_transactions')
            ->where('customer_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'customer' => $customer,
                'transactions' => $transactions
            ]
        ]);
    }

    /**
     * Get loyalty program settings
     */
    public function getSettings()
    {
        $settings = DB::table('loyalty_settings')->first();
        
        if (!$settings) {
            // Create default settings
            $settings = (object) [
                'points_per_tl' => 1,
                'tl_per_point' => 0.01,
                'min_redemption' => 100,
                'max_redemption_percent' => 50,
                'birthday_bonus' => 50,
                'is_active' => true
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Update loyalty program settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'points_per_tl' => 'required|numeric|min:0.01',
            'tl_per_point' => 'required|numeric|min:0.01',
            'min_redemption' => 'required|integer|min:1',
            'max_redemption_percent' => 'required|integer|min:1|max:100',
            'birthday_bonus' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        try {
            $settings = DB::table('loyalty_settings')->first();
            
            $data = $request->except('_token');
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
            $data['updated_at'] = now();

            if ($settings) {
                DB::table('loyalty_settings')->where('id', $settings->id)->update($data);
            } else {
                $data['created_at'] = now();
                DB::table('loyalty_settings')->insert($data);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sadakat programı ayarları güncellendi.'
            ]);

        } catch (\Exception $e) {
            Log::error('Loyalty settings update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
