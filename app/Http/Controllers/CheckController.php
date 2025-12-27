<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckController extends Controller
{
    public function index()
    {
        $checks = DB::table('checks')
            ->join('customers', 'checks.customer_id', '=', 'customers.id')
            ->select('checks.*', 'customers.title as customer_name')
            ->orderBy('checks.created_at', 'desc')
            ->get();

        return view('checks.index', compact('checks'));
    }

    public function create()
    {
        $customers = DB::table('customers')->select('id', 'title', 'current_balance')->get();
        $accounts = DB::table('accounts')->select('id', 'name', 'balance')->get();

        return view('checks.create', compact('customers', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'account_id' => 'nullable|exists:accounts,id',
            'type' => 'required|in:verilen,alınan',
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'check_number' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'issue_date' => 'required|date',
            'maturity_date' => 'required|date|after_or_equal:issue_date',
            'description' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // Create check
            $checkId = DB::table('checks')->insertGetId([
                'customer_id' => $request->customer_id,
                'account_id' => $request->account_id,
                'type' => $request->type,
                'bank_name' => $request->bank_name,
                'branch_name' => $request->branch_name,
                'check_number' => $request->check_number,
                'amount' => $request->amount,
                'issue_date' => $request->issue_date,
                'maturity_date' => $request->maturity_date,
                'description' => $request->description,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update account balance based on check type
            // verilen = Gider (restoran çek verdi, ödeme yaptı) → bakiye azalır
            // alınan = Gelir (restoran çek aldı, para geldi) → bakiye artar
            if ($request->account_id) {
                if ($request->type === 'verilen') {
                    DB::table('accounts')
                        ->where('id', $request->account_id)
                        ->decrement('balance', $request->amount);
                } elseif ($request->type === 'alınan') {
                    DB::table('accounts')
                        ->where('id', $request->account_id)
                        ->increment('balance', $request->amount);
                }

                // Create transaction record for account tracking
                $transactionNumber = 'TXN-' . str_pad((DB::table('transactions')->max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);
                $transactionType = $request->type === 'verilen' ? 'expense' : 'income';
                DB::table('transactions')->insert([
                    'transaction_number' => $transactionNumber,
                    'type' => $transactionType,
                    'account_id' => $request->account_id,
                    'reference_id' => $checkId,
                    'reference_type' => 'check',
                    'amount' => $request->amount,
                    'description' => 'Çek #' . $request->check_number . ' - ' . ($request->type === 'verilen' ? 'Verilen Çek' : 'Alınan Çek'),
                    'date' => $request->issue_date,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Determine transaction type and customer balance change
            $transactionType = $request->type === 'verilen' ? 'Check Issued' : 'Note Issued';
            $customerBalanceChange = $request->type === 'verilen' ? -1 : 1; // -1 for decrement, 1 for increment

            // Add to customer transaction history
            DB::table('customers_account_transactions')->insert([
                'customer_id' => $request->customer_id,
                'date' => $request->issue_date,
                'account' => 'Check',
                'type' => $transactionType,
                'amount' => $request->amount,
                'description' => 'Check #' . $request->check_number,
                'reference_id' => $checkId,
                'transaction_type' => 'check',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update customer balance
            if ($request->type === 'verilen') {
                // Verilen çek = Gider, müşteri bakiyesi azalır
                DB::table('customers')
                    ->where('id', $request->customer_id)
                    ->decrement('current_balance', $request->amount);
            } elseif ($request->type === 'alınan') {
                // Alınan çek = Gelir, müşteri bakiyesi artar
                DB::table('customers')
                    ->where('id', $request->customer_id)
                    ->increment('current_balance', $request->amount);
            }

            DB::commit();

            return redirect()->route('checks.index')->with('success', 'Check created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create check: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $check = DB::table('checks')
            ->join('customers', 'checks.customer_id', '=', 'customers.id')
            ->leftJoin('accounts', 'checks.account_id', '=', 'accounts.id')
            ->select('checks.*', 'customers.title as customer_name', 'accounts.name as account_name')
            ->where('checks.id', $id)
            ->first();

        if (!$check) {
            return redirect()->route('checks.index')->with('error', 'Check not found');
        }

        return view('checks.show', compact('check'));
    }

    public function edit($id)
    {
        $check = DB::table('checks')->find($id);

        if (!$check) {
            return redirect()->route('checks.index')->with('error', 'Check not found');
        }

        $customers = DB::table('customers')->select('id', 'title', 'current_balance')->get();
        $accounts = DB::table('accounts')->select('id', 'name', 'balance')->get();

        return view('checks.edit', compact('check', 'customers', 'accounts'));
    }

    public function update(Request $request, $id)
    {
        $check = DB::table('checks')->find($id);

        if (!$check) {
            return redirect()->route('checks.index')->with('error', 'Check not found');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'account_id' => 'nullable|exists:accounts,id',
            'type' => 'required|in:verilen,alınan',
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'check_number' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'issue_date' => 'required|date',
            'maturity_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|in:PENDING,CLEARED,BOUNCED,CANCELLED',
            'description' => 'nullable|string'
        ]);

        DB::table('checks')->where('id', $id)->update([
            'customer_id' => $request->customer_id,
            'account_id' => $request->account_id,
            'type' => $request->type,
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'check_number' => $request->check_number,
            'amount' => $request->amount,
            'issue_date' => $request->issue_date,
            'maturity_date' => $request->maturity_date,
            'status' => $request->status,
            'description' => $request->description,
            'updated_at' => now()
        ]);

        return redirect()->route('checks.index')->with('success', 'Check updated successfully');
    }

    public function destroy($id)
    {
        $check = DB::table('checks')->find($id);

        if (!$check) {
            return redirect()->route('checks.index')->with('error', 'Check not found');
        }

        DB::beginTransaction();

        try {
            // Reverse the financial operations based on check type
            if ($check->account_id) {
                if ($check->type === 'verilen') {
                    // Verilen çek silinince bakiye geri artar
                    DB::table('accounts')
                        ->where('id', $check->account_id)
                        ->increment('balance', $check->amount);
                } elseif ($check->type === 'alınan') {
                    // Alınan çek silinince bakiye geri azalır
                    DB::table('accounts')
                        ->where('id', $check->account_id)
                        ->decrement('balance', $check->amount);
                }
            }

            // Get customer transaction to reverse customer balance
            $customerTransaction = DB::table('customers_account_transactions')
                ->where('reference_id', $id)
                ->where('transaction_type', 'check')
                ->first();

            if ($customerTransaction) {
                // Reverse customer balance
                if ($check->type === 'verilen') {
                    // Verilen çek silinince müşteri bakiyesi artar
                    DB::table('customers')
                        ->where('id', $customerTransaction->customer_id)
                        ->increment('current_balance', $check->amount);
                } elseif ($check->type === 'alınan') {
                    // Alınan çek silinince müşteri bakiyesi azalır
                    DB::table('customers')
                        ->where('id', $customerTransaction->customer_id)
                        ->decrement('current_balance', $check->amount);
                }
            }

            // Delete transactions table records
            DB::table('transactions')
                ->where('reference_id', $id)
                ->where('reference_type', 'check')
                ->delete();

            // Delete customer account transactions
            DB::table('customers_account_transactions')
                ->where('reference_id', $id)
                ->where('transaction_type', 'check')
                ->delete();

            // Delete check record
            DB::table('checks')->where('id', $id)->delete();

            DB::commit();

            return redirect()->route('checks.index')->with('success', 'Check deleted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('checks.index')->with('error', 'Failed to delete check: ' . $e->getMessage());
        }
    }
}
