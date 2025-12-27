<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Müşteri bakiyesi yönetimi için merkezi servis
 * Tüm müşteri bakiyesi güncellemeleri bu servis üzerinden yapılmalı
 */
class CustomerBalanceService
{
    /**
     * Müşteri bakiyesini hesapla ve güncelle
     * Bu fonksiyon her zaman customers_account_transactions'dan hesaplar
     */
    public function recalculateAndUpdate($customerId)
    {
        $calculatedBalance = $this->calculateBalance($customerId);
        
        DB::table('customers')
            ->where('id', $customerId)
            ->update(['current_balance' => $calculatedBalance]);
        
        return $calculatedBalance;
    }

    /**
     * Müşteri bakiyesini hesapla (güncelleme yapmadan)
     */
    public function calculateBalance($customerId)
    {
        // Expenses (borçlar) - müşteri bakiyesini azaltır
        $expenses = DB::table('customers_account_transactions')
            ->where('customer_id', $customerId)
            ->whereIn('type', ['expense', 'Gider', 'Check Issued'])
            ->sum('amount') ?? 0;

        // Borç ödemeleri - müşteri bakiyesini artırır (borç azaldı)
        $debtPayments = DB::table('customers_account_transactions')
            ->where('customer_id', $customerId)
            ->where('type', 'Borç Ödemesi')
            ->sum('amount') ?? 0;

        // Fazla ödemeler (alacaklar) - müşteri bakiyesini artırır
        $overPayments = DB::table('customers_account_transactions')
            ->where('customer_id', $customerId)
            ->where('transaction_type', 'over_payment')
            ->whereIn('type', ['Gelir', 'income'])
            ->sum('amount') ?? 0;

        // Diğer gelirler (müşteriye borç) - müşteri bakiyesini artırır
        $otherIncome = DB::table('customers_account_transactions')
            ->where('customer_id', $customerId)
            ->whereIn('type', ['income', 'Gelir', 'Note Issued'])
            ->where(function($query) {
                $query->where('transaction_type', '!=', 'over_payment')
                      ->orWhereNull('transaction_type');
            })
            ->sum('amount') ?? 0;

        // Müşteri bakiyesi = Borç Ödemeleri + Fazla Ödemeler + Diğer Gelirler - Expenses
        // Pozitif değer = müşteriye borç, Negatif değer = müşteriden alacak
        return $debtPayments + $overPayments + $otherIncome - $expenses;
    }

    /**
     * Müşteri bakiyesini artır (manuel güncelleme yerine kullanılmalı)
     * @deprecated Bu fonksiyon yerine recalculateAndUpdate kullanılmalı
     */
    public function increment($customerId, $amount, $reason = '')
    {
        Log::warning('CustomerBalanceService::increment called', [
            'customer_id' => $customerId,
            'amount' => $amount,
            'reason' => $reason
        ]);
        
        DB::table('customers')
            ->where('id', $customerId)
            ->increment('current_balance', $amount);
    }

    /**
     * Müşteri bakiyesini azalt (manuel güncelleme yerine kullanılmalı)
     * @deprecated Bu fonksiyon yerine recalculateAndUpdate kullanılmalı
     */
    public function decrement($customerId, $amount, $reason = '')
    {
        Log::warning('CustomerBalanceService::decrement called', [
            'customer_id' => $customerId,
            'amount' => $amount,
            'reason' => $reason
        ]);
        
        DB::table('customers')
            ->where('id', $customerId)
            ->decrement('current_balance', $amount);
    }

    /**
     * Tüm müşteri bakiyelerini yeniden hesapla
     */
    public function recalculateAll()
    {
        $customers = DB::table('customers')->get();
        $updated = 0;
        
        foreach ($customers as $customer) {
            $oldBalance = $customer->current_balance;
            $newBalance = $this->recalculateAndUpdate($customer->id);
            
            if (abs($oldBalance - $newBalance) > 0.01) {
                $updated++;
                Log::info('Customer balance recalculated', [
                    'customer_id' => $customer->id,
                    'old_balance' => $oldBalance,
                    'new_balance' => $newBalance
                ]);
            }
        }
        
        return $updated;
    }
}

