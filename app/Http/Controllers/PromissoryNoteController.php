<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromissoryNoteController extends Controller
{
    public function index()
    {
        $notes = DB::table('promissory_notes')
            ->join('customers', 'promissory_notes.customer_id', '=', 'customers.id')
            ->select('promissory_notes.*', 'customers.title as customer_name')
            ->orderBy('promissory_notes.created_at', 'desc')
            ->get();

        return view('promissory_notes.index', compact('notes'));
    }

    public function create()
    {
        $customers = DB::table('customers')->select('id', 'title', 'current_balance')->get();
        $accounts = DB::table('accounts')->select('id', 'name', 'balance')->get();

        return view('promissory_notes.create', compact('customers', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'account_id' => 'nullable|exists:accounts,id',
            'type' => 'required|in:verilen,alınan',
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'note_number' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'issue_date' => 'required|date',
            'maturity_date' => 'required|date|after_or_equal:issue_date',
            'description' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // Create promissory note
            $noteId = DB::table('promissory_notes')->insertGetId([
                'customer_id' => $request->customer_id,
                'account_id' => $request->account_id,
                'type' => $request->type,
                'bank_name' => $request->bank_name,
                'branch_name' => $request->branch_name,
                'note_number' => $request->note_number,
                'amount' => $request->amount,
                'issue_date' => $request->issue_date,
                'maturity_date' => $request->maturity_date,
                'description' => $request->description,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update account balance based on note type
            // verilen = Gider (restoran senet verdi, ödeme yaptı) → bakiye azalır
            // alınan = Gelir (restoran senet aldı, para geldi) → bakiye artar
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
                    'reference_id' => $noteId,
                    'reference_type' => 'promissory_note',
                    'amount' => $request->amount,
                    'description' => 'Senet #' . $request->note_number . ' - ' . ($request->type === 'verilen' ? 'Verilen Senet' : 'Alınan Senet'),
                    'date' => $request->issue_date,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Determine transaction type based on senet type
            // verilen = Gider → Check Issued (restoran ödeme yaptı)
            // alınan = Gelir → Note Issued (restoran para aldı)
            $transactionType = $request->type === 'verilen' ? 'Check Issued' : 'Note Issued';

            // Add to customer transaction history
            DB::table('customers_account_transactions')->insert([
                'customer_id' => $request->customer_id,
                'date' => $request->issue_date,
                'account' => 'Promissory Note',
                'type' => $transactionType,
                'amount' => $request->amount,
                'description' => 'Note #' . $request->note_number,
                'reference_id' => $noteId,
                'transaction_type' => 'promissory_note',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update customer balance
            if ($request->type === 'verilen') {
                // Verilen senet = Gider, müşteri bakiyesi azalır
                DB::table('customers')
                    ->where('id', $request->customer_id)
                    ->decrement('current_balance', $request->amount);
            } elseif ($request->type === 'alınan') {
                // Alınan senet = Gelir, müşteri bakiyesi artar
                DB::table('customers')
                    ->where('id', $request->customer_id)
                    ->increment('current_balance', $request->amount);
            }

            DB::commit();

            return redirect()->route('promissory_notes.index')->with('success', 'Promissory note created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create promissory note: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $note = DB::table('promissory_notes')
            ->join('customers', 'promissory_notes.customer_id', '=', 'customers.id')
            ->leftJoin('accounts', 'promissory_notes.account_id', '=', 'accounts.id')
            ->select('promissory_notes.*', 'customers.title as customer_name', 'accounts.name as account_name')
            ->where('promissory_notes.id', $id)
            ->first();

        if (!$note) {
            return redirect()->route('promissory_notes.index')->with('error', 'Promissory note not found');
        }

        return view('promissory_notes.show', compact('note'));
    }

    public function edit($id)
    {
        $note = DB::table('promissory_notes')->find($id);

        if (!$note) {
            return redirect()->route('promissory_notes.index')->with('error', 'Promissory note not found');
        }

        $customers = DB::table('customers')->select('id', 'title', 'current_balance')->get();
        $accounts = DB::table('accounts')->select('id', 'name', 'balance')->get();

        return view('promissory_notes.edit', compact('note', 'customers', 'accounts'));
    }

    public function update(Request $request, $id)
    {
        $note = DB::table('promissory_notes')->find($id);

        if (!$note) {
            return redirect()->route('promissory_notes.index')->with('error', 'Promissory note not found');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'account_id' => 'nullable|exists:accounts,id',
            'type' => 'required|in:verilen,alınan',
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'note_number' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'issue_date' => 'required|date',
            'maturity_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|in:ACTIVE,PAID,OVERDUE,CANCELLED',
            'description' => 'nullable|string'
        ]);

        DB::table('promissory_notes')->where('id', $id)->update([
            'customer_id' => $request->customer_id,
            'account_id' => $request->account_id,
            'type' => $request->type,
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'note_number' => $request->note_number,
            'amount' => $request->amount,
            'issue_date' => $request->issue_date,
            'maturity_date' => $request->maturity_date,
            'status' => $request->status,
            'description' => $request->description,
            'updated_at' => now()
        ]);

        return redirect()->route('promissory_notes.index')->with('success', 'Promissory note updated successfully');
    }

    public function destroy($id)
    {
        $note = DB::table('promissory_notes')->find($id);

        if (!$note) {
            return redirect()->route('promissory_notes.index')->with('error', 'Promissory note not found');
        }

        DB::beginTransaction();

        try {
            // Reverse the financial operations based on note type
            if ($note->account_id) {
                if ($note->type === 'verilen') {
                    // Verilen senet silinince bakiye geri artar
                    DB::table('accounts')
                        ->where('id', $note->account_id)
                        ->increment('balance', $note->amount);
                } elseif ($note->type === 'alınan') {
                    // Alınan senet silinince bakiye geri azalır
                    DB::table('accounts')
                        ->where('id', $note->account_id)
                        ->decrement('balance', $note->amount);
                }
            }

            // Get customer transaction to reverse customer balance
            $customerTransaction = DB::table('customers_account_transactions')
                ->where('reference_id', $id)
                ->where('transaction_type', 'promissory_note')
                ->first();

            if ($customerTransaction) {
                // Reverse customer balance
                if ($note->type === 'verilen') {
                    // Verilen senet silinince müşteri bakiyesi artar
                    DB::table('customers')
                        ->where('id', $customerTransaction->customer_id)
                        ->increment('current_balance', $note->amount);
                } elseif ($note->type === 'alınan') {
                    // Alınan senet silinince müşteri bakiyesi azalır
                    DB::table('customers')
                        ->where('id', $customerTransaction->customer_id)
                        ->decrement('current_balance', $note->amount);
                }
            }

            // Delete transactions table records
            DB::table('transactions')
                ->where('reference_id', $id)
                ->where('reference_type', 'promissory_note')
                ->delete();

            // Delete customer account transactions
            DB::table('customers_account_transactions')
                ->where('reference_id', $id)
                ->where('transaction_type', 'promissory_note')
                ->delete();

            // Delete promissory note record
            DB::table('promissory_notes')->where('id', $id)->delete();

            DB::commit();

            return redirect()->route('promissory_notes.index')->with('success', 'Promissory note deleted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('promissory_notes.index')->with('error', 'Failed to delete promissory note: ' . $e->getMessage());
        }
    }
}
