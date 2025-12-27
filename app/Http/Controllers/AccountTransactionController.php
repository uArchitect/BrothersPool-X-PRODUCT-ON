<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountTransactionController extends Controller
{
    /**
     * Display a listing of account transactions
     */
    public function index(Request $request)
    {
        $query = DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select([
                'transactions.*',
                'accounts.name as account_name'
            ]);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('transactions.date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('transactions.date', '<=', $request->end_date);
        }
        if ($request->filled('type')) {
            $query->where('transactions.type', $request->type);
        }
        if ($request->filled('account_id')) {
            $query->where('transactions.account_id', $request->account_id);
        }

        $transactions = $query->orderBy('transactions.date', 'desc')
            ->orderBy('transactions.created_at', 'desc')
            ->paginate(25);

        // Get accounts for filter dropdown
        $accounts = DB::table('accounts')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Calculate summary
        $summary = [
            'total_transactions' => DB::table('transactions')->count(),
            'total_income' => DB::table('transactions')
                ->where('type', 'income')
                ->sum('amount'),
            'total_expense' => DB::table('transactions')
                ->where('type', 'expense')
                ->sum('amount'),
            'total_transfer' => DB::table('transactions')
                ->where('type', 'transfer')
                ->count()
        ];

        return view('financial.account_transactions.index', compact('transactions', 'accounts', 'summary'));
    }

    /**
     * Get transaction for editing
     */
    public function getTransaction($id)
    {
        $transaction = DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select([
                'transactions.*',
                'accounts.name as account_name'
            ])
            ->where('transactions.id', $id)
            ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Hareket bulunamadı.'], 404);
        }

        return response()->json($transaction);
    }

    /**
     * Update transaction
     */
    public function update(Request $request, $id)
    {
        $transaction = DB::table('transactions')->where('id', $id)->first();
        
        if (!$transaction) {
            return response()->json(['error' => 'Hareket bulunamadı.'], 404);
        }

        $data = [
            'account_id' => $request->account_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
            'updated_at' => now()
        ];

        DB::table('transactions')->where('id', $id)->update($data);

        return response()->json(['success' => 'Hareket başarıyla güncellendi.']);
    }

    /**
     * Delete transaction
     */
    public function destroy($id)
    {
        $transaction = DB::table('transactions')->where('id', $id)->first();
        
        if (!$transaction) {
            return redirect()->route('account-transactions.index')
                ->with('error', 'Hareket bulunamadı.');
        }

        DB::beginTransaction();

        // 1. Account balance'ı geri al
        if ($transaction->account_id) {
            if ($transaction->type === 'income') {
                // Gelir silinirken balance azaltılmalı (çünkü gelir eklendiğinde artmıştı)
                DB::table('accounts')
                    ->where('id', $transaction->account_id)
                    ->decrement('balance', $transaction->amount);
            } elseif ($transaction->type === 'expense') {
                // Gider silinirken balance artırılmalı (çünkü gider eklendiğinde azalmıştı)
                DB::table('accounts')
                    ->where('id', $transaction->account_id)
                    ->increment('balance', $transaction->amount);
            }
            // transfer için balance değişikliği yok (iki hesap arası transfer)
        }

        // 2. Eğer bu bir borç ödemesi transaction'ı ise, customer_debt_payments kaydını sil
        if ($transaction->reference_type === 'customer_debt_payment' && $transaction->reference_id) {
            $debtPayment = DB::table('customer_debt_payments')
                ->where('id', $transaction->reference_id)
                ->first();

            if ($debtPayment) {
                // Customer balance'ı geri al (borç ödemesi silindiği için bakiye azalmalı)
                DB::table('customers')
                    ->where('id', $debtPayment->customer_id)
                    ->decrement('current_balance', $debtPayment->paid_amount);

                // Customer account transaction'ı sil
                DB::table('customers_account_transactions')
                    ->where('reference_id', $transaction->reference_id)
                    ->where('transaction_type', 'debt_payment')
                    ->delete();

                // Customer debt payment kaydını sil
                DB::table('customer_debt_payments')
                    ->where('id', $transaction->reference_id)
                    ->delete();
            }
        } else {
            // 3. Diğer durumlar için ilişkili customer transactions'ı sil
            if ($transaction->reference_id && $transaction->reference_type) {
                DB::table('customers_account_transactions')
                    ->where('reference_id', $transaction->reference_id)
                    ->where('transaction_type', $transaction->reference_type)
                    ->delete();
            }
        }

        // 4. Transaction'ı sil
        DB::table('transactions')->where('id', $id)->delete();

        DB::commit();

        return redirect()->route('account-transactions.index')
            ->with('success', 'Hareket başarıyla silindi.');
    }
}

