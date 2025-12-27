<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers with search and pagination
     */
    public function index(Request $request)
    {
        $query = DB::table('customers');

        // Simple filter by account type (from quick access boxes)
        if ($request->filled('account_type')) {
            $query->where('account_type', $request->account_type);
        }

        // Filter by balance range (from quick access boxes)
        if ($request->filled('balance_min')) {
            $query->where('current_balance', '>=', $request->balance_min);
        }

        // DataTables kullanıldığı için tüm müşterileri getir (pagination client-side yapılıyor)
        // title'a göre alfabetik sırala (NULL'lar en sonda)
        $customers = $query->orderByRaw('CASE WHEN title IS NULL OR title = "" THEN 1 ELSE 0 END')
                           ->orderBy('title', 'asc')
                           ->orderBy('id', 'desc')
                           ->get();

        // Her müşteri için borç özeti ekle
        $debtService = app(\App\Services\CustomerDebtService::class);
        foreach ($customers as $customer) {
            $customer->debt_summary = $debtService->getCustomerDebtSummary($customer->id);
        }

        return view('financial.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return view('financial.customers.create');
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        try {
            DB::table('customers')->insert([
                'code' => $request->code,
                'title' => $request->title,
                'account_type' => $request->account_type,
                'tax_office' => $request->tax_office,
                'tax_number' => $request->tax_number,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'authorized_person' => $request->authorized_person,
                'credit_limit' => $request->credit_limit ?? 0,
                'current_balance' => $request->current_balance ?? 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            // try-catch ile, cache kullanmadan hatayı dd ile döndür
            dd($e->getMessage());
        }

        return redirect()->route('customers.index')
            ->with('success', 'Müşteri başarıyla oluşturuldu.');
    }

    /**
     * Display the specified customer
     */
    public function show($id)
    {
        $customer = DB::table('customers')->where('id', $id)->first();
        
        if (!$customer) {
            return redirect()->route('customers.index')
                ->with('error', 'Müşteri bulunamadı.');
        }

        // Tüm işlemleri birleştir (Hareketler + Borçlar)
        $allTransactions = collect();
        
        // 1. Müşteri hesap hareketleri (borç ödemeleri ve müşteriye atanmış expense'ler hariç)
        // Önce müşteriye atanmış expense ID'lerini al (bunlar borç olarak gösterilecek)
        $customerExpenseIds = DB::table('expenses')
            ->where('customer_id', $id)
            ->pluck('id')
            ->toArray();
        
        $accountTransactions = DB::table('customers_account_transactions')
            ->where('customer_id', $id)
            ->where(function($q) use ($customerExpenseIds) {
                $q->where(function($subQ) use ($customerExpenseIds) {
                    // Borç ödemesi olmayan kayıtlar
                    $subQ->where('transaction_type', '!=', 'debt_payment')
                         ->where('type', '!=', 'Borç Ödemesi')
                         ->where('type', '!=', 'borç ödemesi')
                         // Müşteriye atanmış expense'leri de hariç tut (borç olarak gösterilecek)
                         ->where(function($expenseQ) use ($customerExpenseIds) {
                             if (!empty($customerExpenseIds)) {
                                 $expenseQ->where('transaction_type', '!=', 'expense')
                                          ->orWhereNotIn('reference_id', $customerExpenseIds);
                             } else {
                                 $expenseQ->where('transaction_type', '!=', 'expense');
                             }
                         });
                })
                ->orWhereNull('transaction_type');
            })
            ->get()
            ->map(function($transaction) {
                $accountName = $transaction->account;
                
                if ($transaction->reference_id && $transaction->transaction_type) {
                    if ($transaction->transaction_type === 'expense') {
                        $expense = DB::table('expenses')
                            ->join('accounts', 'expenses.account_id', '=', 'accounts.id')
                            ->where('expenses.id', $transaction->reference_id)
                            ->select('accounts.name')
                            ->first();
                        if ($expense) {
                            $accountName = $expense->name;
                        }
                    } elseif ($transaction->transaction_type === 'income') {
                        $income = DB::table('incomes')
                            ->join('accounts', 'incomes.account_id', '=', 'accounts.id')
                            ->where('incomes.id', $transaction->reference_id)
                            ->select('accounts.name')
                            ->first();
                        if ($income) {
                            $accountName = $income->name;
                        }
                    }
                }
                
                return (object)[
                    'id' => $transaction->id,
                    'date' => $transaction->date,
                    'type' => $transaction->type,
                    'amount' => $transaction->amount,
                    'description' => $transaction->description,
                    'account_name' => $accountName,
                    'transaction_type' => $transaction->transaction_type ?? 'account_transaction',
                    'reference_id' => $transaction->reference_id,
                    'created_at' => $transaction->created_at,
                ];
            });
        
        $allTransactions = $allTransactions->merge($accountTransactions);
        
        // 2. Borç kayıtları (expenses) - sadece borç olarak göster
        $debtService = app(\App\Services\CustomerDebtService::class);
        $debtSummary = $debtService->getCustomerDebtSummary($id);
        $debts = $debtService->getCustomerDebts($id);
        
        // İlk ödenmemiş borcu bul (modal için)
        $firstUnpaidDebt = $debts->where('is_paid', false)->first();
        
        $debtTransactions = $debts->map(function($debt) {
            return (object)[
                'id' => 'debt_' . $debt->id,
                'date' => $debt->debt_date,
                'type' => 'Borç',
                'amount' => $debt->debt_amount,
                'description' => 'Borç: ' . ($debt->description ?? $debt->expense_number),
                'account_name' => $debt->account_name ?? '-',
                'transaction_type' => 'debt',
                'reference_id' => $debt->id,
                'created_at' => $debt->created_at,
                'debt_info' => (object)[
                    'expense_number' => $debt->expense_number,
                    'paid_amount' => $debt->paid_amount,
                    'remaining_amount' => $debt->remaining_amount,
                    'is_paid' => $debt->is_paid,
                ]
            ];
        });
        
        $allTransactions = $allTransactions->merge($debtTransactions);
        
        // 3. Borç ödemeleri
        $debtPayments = DB::table('customer_debt_payments')
            ->where('customer_id', $id)
            ->where('status', 'completed')
            ->get()
            ->map(function($payment) {
                $account = DB::table('accounts')
                    ->where('id', $payment->account_id)
                    ->first();
                
                return (object)[
                    'id' => 'payment_' . $payment->id,
                    'date' => $payment->payment_date,
                    'type' => 'Borç Ödemesi',
                    'amount' => $payment->paid_amount,
                    'description' => 'Borç Ödemesi - ' . $payment->payment_number,
                    'account_name' => $account->name ?? '-',
                    'transaction_type' => 'debt_payment',
                    'reference_id' => $payment->id,
                    'created_at' => $payment->created_at,
                    'payment_info' => (object)[
                        'payment_number' => $payment->payment_number,
                        'payment_method' => $payment->payment_method,
                    ]
                ];
            });
        
        $allTransactions = $allTransactions->merge($debtPayments);
        
        // Tarihe göre sırala
        $allTransactions = $allTransactions->sortByDesc(function($item) {
            return $item->date . ' ' . ($item->created_at ?? '');
        })->values();

        // Kasa hesapları (borç ödemesi için) - Tüm kasalar
        $cashAccounts = DB::table('accounts')
            ->where('is_active', true)
            ->where(function($q) {
                $q->where('type', 'cash')
                  ->orWhere('name', 'like', '%kasa%')
                  ->orWhere('name', 'like', '%Kasa%')
                  ->orWhere('name', 'like', '%KASA%')
                  ->orWhere('name', 'like', '%cash%')
                  ->orWhere('name', 'like', '%Cash%')
                  ->orWhere('name', 'like', '%CASH%');
            })
            ->orderBy('name')
            ->get();

        // Eğer kasa hesabı yoksa, tüm aktif hesapları getir
        if ($cashAccounts->isEmpty()) {
            $cashAccounts = DB::table('accounts')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('financial.customers.show', compact('customer', 'allTransactions', 'debtSummary', 'cashAccounts', 'firstUnpaidDebt'));
    }

    /**
     * Show the form for editing the specified customer
     */
    public function edit($id)
    {
        $customer = DB::table('customers')->where('id', $id)->first();
        
        if (!$customer) {
            return redirect()->route('customers.index')
                ->with('error', 'Müşteri bulunamadı.');
        }

        return view('financial.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, $id)
    {
        DB::transaction(function() use ($request, $id) {
            $updateData = array_filter([
                'code' => $request->code,
                'title' => $request->title,
                'account_type' => $request->account_type,
                'tax_office' => $request->tax_office,
                'tax_number' => $request->tax_number,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'authorized_person' => $request->authorized_person,
                'credit_limit' => $request->credit_limit,
                'current_balance' => $request->current_balance,
                'updated_at' => now()
            ], function($value) {
                return $value !== null;
            });

            DB::table('customers')->where('id', $id)->update($updateData);
        });

        return redirect()->route('customers.index')
            ->with('success', 'Müşteri başarıyla güncellendi.');
    }

    /**
     * Remove the specified customer
     */
    public function destroy($id)
    {
        DB::transaction(function() use ($id) {
            // customers_account_transactions → cascade ile otomatik silinecek ama manuel de silelim
            DB::table('customers_account_transactions')->where('customer_id', $id)->delete();
            
            // Checks - foreign key yok, manuel silinmeli
            $checks = DB::table('checks')->where('customer_id', $id)->get();
            foreach ($checks as $check) {
                // Reverse account balance if needed
                if ($check->account_id) {
                    if ($check->type === 'verilen') {
                        DB::table('accounts')
                            ->where('id', $check->account_id)
                            ->increment('balance', $check->amount);
                    } elseif ($check->type === 'alınan') {
                        DB::table('accounts')
                            ->where('id', $check->account_id)
                            ->decrement('balance', $check->amount);
                    }
                }
            }
            DB::table('checks')->where('customer_id', $id)->delete();
            
            // Promissory Notes - foreign key yok, manuel silinmeli
            $notes = DB::table('promissory_notes')->where('customer_id', $id)->get();
            foreach ($notes as $note) {
                // Reverse account balance if needed
                if ($note->account_id) {
                    if ($note->type === 'verilen') {
                        DB::table('accounts')
                            ->where('id', $note->account_id)
                            ->increment('balance', $note->amount);
                    } elseif ($note->type === 'alınan') {
                        DB::table('accounts')
                            ->where('id', $note->account_id)
                            ->decrement('balance', $note->amount);
                    }
                }
            }
            DB::table('promissory_notes')->where('customer_id', $id)->delete();
            
            // Expenses ve Incomes → foreign key set null olduğu için customer_id null yapılır, kayıtlar kalır
            // Ancak müşteri silindiği için customer_id'yi null yapalım
            DB::table('expenses')->where('customer_id', $id)->update(['customer_id' => null]);
            DB::table('incomes')->where('customer_id', $id)->update(['customer_id' => null]);
            
            // Delete customer
            DB::table('customers')->where('id', $id)->delete();
        });

        return redirect()->route('customers.index')
            ->with('success', 'Müşteri ve tüm ilişkili kayıtlar başarıyla silindi.');
    }

    /**
     * Get customer balance information
     */
    public function getBalance($id)
    {
        $customer = DB::table('customers')->where('id', $id)->first();
        
        if (!$customer) {
            return response()->json(['error' => 'Müşteri bulunamadı.'], 404);
        }

        return response()->json([
            'current_balance' => $customer->current_balance,
            'credit_limit' => $customer->credit_limit,
            'available_credit' => $customer->credit_limit - $customer->current_balance
        ]);
    }
}