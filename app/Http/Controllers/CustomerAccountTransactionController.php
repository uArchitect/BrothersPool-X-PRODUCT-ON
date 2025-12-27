<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerAccountTransactionController extends Controller
{
    /**
     * Display customer transactions
     */
    public function index(Request $request, $customerId)
    {
        $customer = DB::table('customers')->where('id', $customerId)->first();
        
        if (!$customer) {
            return redirect()->route('customers.index')
                ->with('error', 'Müşteri bulunamadı.');
        }

        // Get transactions with filters
        $query = DB::table('customers_account_transactions')
            ->where('customer_id', $customerId);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('account')) {
            $query->where('account', 'like', '%' . $request->account . '%');
        }

        $transactions = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Calculate summary
        $summary = [
            'total_transactions' => DB::table('customers_account_transactions')
                ->where('customer_id', $customerId)
                ->count(),
            'total_income' => DB::table('customers_account_transactions')
                ->where('customer_id', $customerId)
                ->where('type', 'Gelir')
                ->sum('amount'),
            'total_expense' => DB::table('customers_account_transactions')
                ->where('customer_id', $customerId)
                ->where('type', 'Gider')
                ->sum('amount'),
            'net_balance' => DB::table('customers_account_transactions')
                ->where('customer_id', $customerId)
                ->where('type', 'Gelir')
                ->sum('amount') - DB::table('customers_account_transactions')
                ->where('customer_id', $customerId)
                ->where('type', 'Gider')
                ->sum('amount')
        ];

        return view('financial.customers.transactions.index', compact('customer', 'transactions', 'summary'));
    }

    /**
     * Quick income entry
     * amacı: 
     */
    public function quickIncome(Request $request)
    {
        // Validation removed for testing

        DB::beginTransaction();
        
        // Update account balance
        DB::table('accounts')
            ->where('id', $request->account_id)
            ->increment('balance', $request->amount);

        // Generate income number
        $maxId = DB::table('incomes')->max('id') ?? 0;
        $incomeNumber = 'INC-' . str_pad($maxId + 1, 6, '0', STR_PAD_LEFT);

        // Create income record
        $incomeData = [
            'income_number' => $incomeNumber,
            'income_category_id' => 1, // Default category (Satış Geliri)
            'account_id' => $request->account_id,
            'customer_id' => $request->customer_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => is_array($request->description) ? implode(', ', array_filter($request->description)) : ($request->description ?? 'Hızlı gelir girişi'),
            'payment_method' => 'cash',
            'reference_number' => null,
            'status' => 'TAMAMLANDI',
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ];

        $incomeId = DB::table('incomes')->insertGetId($incomeData);

        // Generate transaction number
        $maxTxnId = DB::table('transactions')->max('id') ?? 0;
        $transactionNumber = 'TXN-' . str_pad($maxTxnId + 1, 6, '0', STR_PAD_LEFT);

        // Create transaction record
        DB::table('transactions')->insert([
            'transaction_number' => $transactionNumber,
            'type' => 'income',
            'account_id' => $request->account_id,
            'reference_id' => $incomeId,
            'reference_type' => 'income',
            'amount' => $request->amount,
            'description' => 'Hızlı Gelir Eklendi - Belge No: ' . $incomeNumber,
            'date' => $request->date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create customer transaction (customer is required)
        Log::info('[Quick Income] Customer selected:', [
            'customer_id' => $request->customer_id,
            'amount' => $request->amount
        ]);
        
        $transactionData = [
            'customer_id' => $request->customer_id,
            'date' => $request->date,
            'account' => 'Genel Hesap',
            'type' => 'Gelir',
            'amount' => $request->amount,
            'description' => is_array($request->description) ? implode(', ', array_filter($request->description)) : ($request->description ?? 'Hızlı gelir girişi'),
            'created_at' => now(),
            'updated_at' => now()
        ];

        $insertResult = DB::table('customers_account_transactions')->insert($transactionData);
        
        // Update customer balance
        DB::table('customers')
            ->where('id', $request->customer_id)
            ->increment('current_balance', $request->amount);
            
        Log::info('[Quick Income] Customer transaction created successfully');

        DB::commit();

        Log::info('[Quick Income]', [
            'amount' => $request->amount,
            'account_id' => $request->account_id,
            'customer_id' => $request->customer_id,
            'income_id' => $incomeId
        ]);

        return redirect()->back()
            ->with('success', 'Gelir başarıyla kaydedildi.');
    }

    /**
     * Quick expense entry
     */
    public function quickExpense(Request $request)
    {
        // Validation removed for testing


        DB::beginTransaction();
        
            // Update account balance (decrease) - sadece hesap seçilmişse
            if ($request->account_id) {
                DB::table('accounts')
                    ->where('id', $request->account_id)
                    ->decrement('balance', $request->amount);
            }

            // Create customer transaction (customer is required)
            Log::info('[Quick Expense] Customer selected:', [
                'customer_id' => $request->customer_id,
                'amount' => $request->amount
            ]);
            
            $transactionData = [
                'customer_id' => $request->customer_id,
                'date' => $request->date,
                'account' => 'Genel Hesap',
                'type' => 'Gider',
                'amount' => $request->amount,
                'description' => is_array($request->description) ? implode(', ', array_filter($request->description)) : ($request->description ?? 'Hızlı gider girişi'),
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            $insertResult = DB::table('customers_account_transactions')->insert($transactionData);
       

            // Update customer balance (decrease)
            DB::table('customers')
                ->where('id', $request->customer_id)
                ->decrement('current_balance', $request->amount);
                
            Log::info('[Quick Expense] Customer transaction created successfully');

            DB::commit();

            Log::info('[Quick Expense]', [
                'amount' => $request->amount,
                'account_id' => $request->account_id,
                'customer_id' => $request->customer_id
            ]);

            return redirect()->back()
                ->with('success', 'Gider başarıyla kaydedildi.');

    }
}