<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CustomerDebtService extends BaseService
{
    /**
     * Müşterinin borçlarını getir (expenses'ten kaynaklanan)
     */
    public function getCustomerDebts($customerId, $status = null)
    {
        $query = DB::table('expenses')
            ->where('expenses.customer_id', $customerId)
            ->leftJoin('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id')
            ->leftJoin('accounts', 'expenses.account_id', '=', 'accounts.id')
            ->select([
                'expenses.id',
                'expenses.expense_number',
                'expenses.amount as debt_amount',
                'expenses.date as debt_date',
                'expenses.description',
                'expense_types.name as expense_type_name',
                'accounts.name as account_name',
                'expenses.created_at'
            ]);

        // Ödenen borçları hesapla
        $paidDebts = DB::table('customer_debt_payments')
            ->where('customer_id', $customerId)
            ->where('status', 'completed')
            ->where('reference_type', 'expense')
            ->select('reference_id', DB::raw('SUM(paid_amount) as total_paid'))
            ->groupBy('reference_id')
            ->pluck('total_paid', 'reference_id')
            ->toArray();

        $debts = $query->get()->map(function ($debt) use ($paidDebts) {
            $paid = $paidDebts[$debt->id] ?? 0;
            $debt->paid_amount = $paid;
            $debt->remaining_amount = $debt->debt_amount - $paid;
            $debt->is_paid = $debt->remaining_amount <= 0;
            $debt->payment_status = $debt->is_paid ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
            return $debt;
        });

        // Status filtresi
        if ($status) {
            $debts = $debts->filter(function ($debt) use ($status) {
                return $debt->payment_status === $status;
            });
        }

        return $debts;
    }

    /**
     * Kasa üzerinden borç ödemesi yap
     */
    public function payDebtWithCash(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $customerId = $data['customer_id'];
            $referenceId = $data['reference_id']; // expense_id
            $referenceType = $data['reference_type'] ?? 'expense';
            $paymentAmount = $data['paid_amount'];
            $accountId = $data['account_id']; // Kasa hesabı
            $paymentMethod = $data['payment_method'] ?? 'cash';
            $description = $data['description'] ?? null;
            $notes = $data['notes'] ?? null;

            // Expense'i kontrol et
            $expense = DB::table('expenses')
                ->where('id', $referenceId)
                ->where('customer_id', $customerId)
                ->first();

            if (!$expense) {
                throw new \Exception('Borç kaydı bulunamadı veya müşteri eşleşmiyor.');
            }

            // Mevcut ödemeleri hesapla
            $totalPaid = DB::table('customer_debt_payments')
                ->where('customer_id', $customerId)
                ->where('reference_id', $referenceId)
                ->where('reference_type', $referenceType)
                ->where('status', 'completed')
                ->sum('paid_amount') ?? 0;

            $remainingDebt = $expense->amount - $totalPaid;

            if ($paymentAmount <= 0) {
                throw new \Exception('Ödeme tutarı 0\'dan büyük olmalıdır.');
            }

            // Ödeme tutarı kontrolü kaldırıldı - sınırsız ödeme yapılabilir (fazla ödeme/avans ödeme için)
            
            // Fazla ödeme kontrolü
            $isOverPayment = $paymentAmount > $remainingDebt;
            $overPaymentAmount = $isOverPayment ? ($paymentAmount - $remainingDebt) : 0;
            $actualDebtPayment = $isOverPayment ? $remainingDebt : $paymentAmount;
            $finalRemainingAmount = $isOverPayment ? 0 : ($remainingDebt - $paymentAmount);

            // Ödeme numarası oluştur
            $paymentNumber = 'BORC-' . str_pad(DB::table('customer_debt_payments')->max('id') + 1, 6, '0', STR_PAD_LEFT);

            // Borç ödemesi kaydı oluştur
            $debtPaymentId = DB::table('customer_debt_payments')->insertGetId([
                'payment_number' => $paymentNumber,
                'customer_id' => $customerId,
                'account_id' => $accountId,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'debt_amount' => $expense->amount,
                'paid_amount' => $paymentAmount,
                'remaining_amount' => $finalRemainingAmount,
                'payment_method' => $paymentMethod,
                'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                'description' => $description ?? 'Borç Ödemesi - ' . $expense->expense_number,
                'notes' => $notes,
                'status' => 'completed',
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Kasa hesabı bakiyesini güncelle (kasa azalır - ödeme yapıldı)
            DB::table('accounts')
                ->where('id', $accountId)
                ->decrement('balance', $paymentAmount);

            // Transaction kaydı oluştur
            $transactionNumber = 'TXN-' . str_pad(DB::table('transactions')->max('id') + 1, 6, '0', STR_PAD_LEFT);
            DB::table('transactions')->insert([
                'transaction_number' => $transactionNumber,
                'type' => 'expense', // Borç ödemesi bir gider işlemidir
                'account_id' => $accountId,
                'reference_id' => $debtPaymentId,
                'reference_type' => 'customer_debt_payment',
                'amount' => $paymentAmount,
                'description' => 'Borç Ödemesi - ' . $paymentNumber . ' (Gider: ' . $expense->expense_number . ')' . ($isOverPayment ? ' + Fazla Ödeme: ₺' . number_format($overPaymentAmount, 2) : ''),
                'date' => $data['payment_date'] ?? now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Müşteri hesap hareketi oluştur - Borç ödemesi
            DB::table('customers_account_transactions')->insert([
                'customer_id' => $customerId,
                'date' => $data['payment_date'] ?? now()->toDateString(),
                'account' => 'Borç Ödemesi',
                'type' => 'Borç Ödemesi',
                'amount' => $actualDebtPayment,
                'description' => 'Borç Ödemesi - ' . $paymentNumber . ' (Gider: ' . $expense->expense_number . ')',
                'reference_id' => $debtPaymentId,
                'transaction_type' => 'debt_payment',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Eğer fazla ödeme varsa, fazla kısmı alacak olarak kaydet
            if ($isOverPayment && $overPaymentAmount > 0) {
                DB::table('customers_account_transactions')->insert([
                    'customer_id' => $customerId,
                    'date' => $data['payment_date'] ?? now()->toDateString(),
                    'account' => 'Fazla Ödeme / Alacak',
                    'type' => 'Gelir', // Fazla ödeme müşteriye alacak olarak kaydedilir
                    'amount' => $overPaymentAmount,
                    'description' => 'Fazla Ödeme / Alacak - ' . $paymentNumber . ' (Gider: ' . $expense->expense_number . ')',
                    'reference_id' => $debtPaymentId,
                    'transaction_type' => 'over_payment',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Müşteri bakiyesini yeniden hesapla (customers_account_transactions'dan)
            app(\App\Services\CustomerBalanceService::class)->recalculateAndUpdate($customerId);

            Log::info('Customer debt payment completed', [
                'debt_payment_id' => $debtPaymentId,
                'customer_id' => $customerId,
                'expense_id' => $referenceId,
                'amount' => $paymentAmount,
                'user_id' => Auth::id(),
            ]);

            $message = 'Borç ödemesi başarıyla tamamlandı.';
            if ($isOverPayment && $overPaymentAmount > 0) {
                $message .= ' Fazla ödeme (₺' . number_format($overPaymentAmount, 2) . ') müşteriye alacak olarak kaydedildi.';
            }

            return [
                'success' => true,
                'debt_payment_id' => $debtPaymentId,
                'payment_number' => $paymentNumber,
                'transaction_number' => $transactionNumber,
                'remaining_debt' => $finalRemainingAmount,
                'over_payment' => $overPaymentAmount,
                'message' => $message,
            ];
        });
    }

    /**
     * Borç ödemesini iptal et
     */
    public function cancelDebtPayment($debtPaymentId): bool
    {
        return DB::transaction(function () use ($debtPaymentId) {
            $debtPayment = DB::table('customer_debt_payments')
                ->where('id', $debtPaymentId)
                ->where('status', 'completed')
                ->first();

            if (!$debtPayment) {
                throw new \Exception('Borç ödemesi bulunamadı veya iptal edilemez durumda.');
            }

            // Kasa bakiyesini geri al
            DB::table('accounts')
                ->where('id', $debtPayment->account_id)
                ->increment('balance', $debtPayment->paid_amount);

            // Transaction'ı sil
            DB::table('transactions')
                ->where('reference_id', $debtPaymentId)
                ->where('reference_type', 'customer_debt_payment')
                ->delete();

            // Müşteri hesap hareketini sil
            DB::table('customers_account_transactions')
                ->where('reference_id', $debtPaymentId)
                ->where('transaction_type', 'debt_payment')
                ->delete();

            // Müşteri bakiyesini yeniden hesapla (customers_account_transactions'dan)
            app(\App\Services\CustomerBalanceService::class)->recalculateAndUpdate($debtPayment->customer_id);

            // Ödeme kaydını iptal et
            DB::table('customer_debt_payments')
                ->where('id', $debtPaymentId)
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now(),
                ]);

            Log::info('Customer debt payment cancelled', [
                'debt_payment_id' => $debtPaymentId,
                'user_id' => Auth::id(),
            ]);

            return true;
        });
    }

    /**
     * Müşterinin toplam borç bilgilerini getir
     */
    public function getCustomerDebtSummary($customerId): array
    {
        $debts = $this->getCustomerDebts($customerId);

        $totalDebt = $debts->sum('debt_amount');
        $totalPaid = $debts->sum('paid_amount');
        $totalRemaining = $debts->sum('remaining_amount');

        return [
            'total_debt' => $totalDebt,
            'total_paid' => $totalPaid,
            'total_remaining' => $totalRemaining,
            'unpaid_count' => $debts->where('payment_status', 'unpaid')->count(),
            'partial_count' => $debts->where('payment_status', 'partial')->count(),
            'paid_count' => $debts->where('payment_status', 'paid')->count(),
        ];
    }
}

