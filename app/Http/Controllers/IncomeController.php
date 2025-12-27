<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('incomes')
            ->leftJoin('income_categories', 'incomes.income_category_id', '=', 'income_categories.id')
            ->leftJoin('accounts', 'incomes.account_id', '=', 'accounts.id')
            ->leftJoin('customers', 'incomes.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'incomes.created_by', '=', 'users.id')
            ->select([
                'incomes.*',
                'income_categories.name as category_name',
                'accounts.name as account_name',
                'customers.title as customer_name',
                'users.name as created_by_name'
            ]);

        // Filtreleme
        if ($request->filled('start_date')) {
            $query->where('incomes.date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('incomes.date', '<=', $request->end_date);
        }
        if ($request->filled('income_category_id')) {
            $query->where('incomes.income_category_id', $request->income_category_id);
        }
        if ($request->filled('status')) {
            $query->where('incomes.status', $request->status);
        }

        $incomes = $query->orderBy('incomes.date', 'desc')->get();

        // Filtre seçenekleri
        $incomeCategories = DB::table('income_categories')->orderBy('name')->get();
        $accounts = DB::table('accounts')->orderBy('name')->get();
        $customers = DB::table('customers')->orderBy('title')->get();

        // İstatistikler
        $totalIncomes = DB::table('incomes')->count();
        $totalAmount = DB::table('incomes')->sum('amount');
        $thisMonthIncomes = DB::table('incomes')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();
        $thisMonthAmount = DB::table('incomes')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');


        return view('financial.incomes.index', compact(
            'incomes',
            'incomeCategories',
            'accounts',
            'customers',
            'totalIncomes',
            'totalAmount',
            'thisMonthIncomes',
            'thisMonthAmount'
        ));
    }

    public function create()
    {
        // Get all records (including inactive ones)
        $incomeCategories = DB::table('income_categories')->orderBy('name')->get();
        $incomeTypes = DB::table('income_types')->orderBy('name')->get();
        $accounts = DB::table('accounts')->where('is_active', true)->orderBy('name')->get();
        $customers = DB::table('customers')->orderBy('title')->get();

        // Generate income number - son eklenen belge numarasından +1
        $lastIncome = DB::table('incomes')
            ->whereNotNull('income_number')
            ->where('income_number', 'like', 'GEL-%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastIncome && preg_match('/(\d+)$/', $lastIncome->income_number, $matches)) {
            $lastNumber = intval($matches[1]);
            $nextNumber = $lastNumber + 1;
            $incomeId = 'GEL-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        } else {
            // İlk kayıt için
            $incomeId = 'GEL-000001';
        }

        // Get tables for employee_add_modal
        $tables = DB::table('tables')->where('is_active', true)->orderBy('table_number')->get();

        return view('financial.incomes.create', compact('incomeCategories', 'accounts', 'customers', 'incomeId', 'tables', 'incomeTypes'));
    }

    /**
     * Store a newly created income with comprehensive validation
     */
    public function store(Request $request)
    {
        // No validation - direct processing
        DB::beginTransaction();

        // Calculate total from line items with precision
        $totalAmount = $this->calculateTotalAmount($request);

        if ($totalAmount <= 0) {
            throw new Exception('Toplam tutar 0\'dan büyük olmalıdır.');
        }

        // Generate income number if not provided
        $incomeNumber = $this->generateIncomeNumber($request);

        // Insert income record
        $incomeId = DB::table('incomes')->insertGetId([
            'income_number' => $incomeNumber,
            'income_category_id' => $request->income_category_id,
            'account_id' => $request->account_id,
            'customer_id' => $request->customer_id ?? null,
            'amount' => $totalAmount,
            'date' => $request->date,
            'description' => $request->description ?? null,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number ?? null,
            'status' => 'TAMAMLANDI',
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Insert income items with validation
        $this->insertIncomeItems($incomeId, $request);

        // Eğer hesap seçilmişse, account balance güncelle ve transaction oluştur
        if ($request->account_id) {
            // Update account balance
            $this->updateAccountBalance($request->account_id, $totalAmount, 'credit');

            // Create transaction record
            $this->createTransactionRecord($incomeId, $incomeNumber, $request, $totalAmount);
        }

        // Handle customer transaction if applicable
        if ($request->customer_id) {
            $this->createCustomerTransaction($request->customer_id, $totalAmount, $incomeNumber, $request->description ?? null);
        }

        DB::commit();

        return redirect()->route('incomes.index')
            ->with('success', 'Gelir başarıyla kaydedildi. Belge No: ' . $incomeNumber);
    }

    /**
     * Display the specified income
     */
    public function show($id)
    {
        $income = DB::table('incomes')
            ->leftJoin('income_categories', 'incomes.income_category_id', '=', 'income_categories.id')
            ->leftJoin('accounts', 'incomes.account_id', '=', 'accounts.id')
            ->leftJoin('customers', 'incomes.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'incomes.created_by', '=', 'users.id')
            ->select([
                'incomes.*',
                'income_categories.name as category_name',
                'accounts.name as account_name',
                'customers.title as customer_name',
                'users.name as created_by_name'
            ])
            ->where('incomes.id', $id)
            ->first();

        if (!$income) {
            return redirect()->route('incomes.index')
                ->with('error', 'Gelir bulunamadı.');
        }

        // Get income items
        $incomeItems = DB::table('income_items')
            ->where('income_id', $id)
            ->get();

        return view('financial.incomes.show', compact('income', 'incomeItems'));
    }

    /**
     * Show the form for editing the specified income
     */
    public function edit($id)
    {
        $income = DB::table('incomes')->where('id', $id)->first();

        if (!$income) {
            return redirect()->route('incomes.index')
                ->with('error', 'Gelir bulunamadı.');
        }

        $incomeCategories = DB::table('income_categories')->orderBy('name')->get();
        $incomeTypes = DB::table('income_types')->orderBy('name')->get();
        $accounts = DB::table('accounts')->orderBy('name')->get();
        $customers = DB::table('customers')->select('id', 'title', 'current_balance')->orderBy('title')->get();

        // Get income items
        $incomeItems = DB::table('income_items')
            ->where('income_id', $id)
            ->get();

        return view('financial.incomes.edit', compact('income', 'incomeCategories', 'incomeTypes', 'accounts', 'customers', 'incomeItems'));
    }

    /**
     * Update the specified income with comprehensive validation
     */
    public function update(Request $request, $id)
    {
        $income = DB::table('incomes')->where('id', $id)->first();

        if (!$income) {
            return redirect()->route('incomes.index')
                ->with('error', 'Gelir bulunamadı.');
        }

        DB::beginTransaction();

        // Calculate new total amount
        $newTotalAmount = $this->calculateTotalAmount($request);

        if ($newTotalAmount <= 0) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Toplam tutar 0\'dan büyük olmalıdır.')
                ->withInput();
        }

        // Calculate difference for account balance adjustment
        $amountDifference = $newTotalAmount - $income->amount;

        // Update income record
        DB::table('incomes')->where('id', $id)->update([
            'income_category_id' => $request->income_category_id,
            'account_id' => $request->account_id,
            'customer_id' => $request->customer_id ?? null,
            'amount' => $newTotalAmount,
            'date' => $request->date,
            'description' => $request->description ?? null,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number ?? null,
            'updated_at' => now()
        ]);

        // Delete existing income items and insert new ones
        DB::table('income_items')->where('income_id', $id)->delete();
        $this->insertIncomeItems($id, $request);

        // Eğer hesap seçilmişse, account balance güncelle ve transaction oluştur/güncelle
        if ($request->account_id) {
            // Eski hesap varsa ve değiştiyse, eski hesabı geri al
            if ($income->account_id && $income->account_id != $request->account_id) {
                $this->updateAccountBalance($income->account_id, $income->amount, 'debit');
            }
            
            // Update account balance if amount changed
            if ($amountDifference != 0) {
                $this->updateAccountBalance($request->account_id, abs($amountDifference), $amountDifference > 0 ? 'credit' : 'debit');
            }

            // Update transaction record
            $this->updateTransactionRecord($id, $newTotalAmount, $request);
        } else {
            // Hesap seçilmemişse, mevcut transaction'ı sil (eğer varsa)
            DB::table('transactions')
                ->where('reference_id', $id)
                ->where('reference_type', 'income')
                ->delete();
            
            // Eğer önceden hesap varsa, balance'ı geri al
            if ($income->account_id) {
                $this->updateAccountBalance($income->account_id, $income->amount, 'debit');
            }
        }

        // Handle customer transaction updates
        if ($request->customer_id || $income->customer_id) {
            $this->updateCustomerTransaction($id, $income->customer_id, $request->customer_id, $amountDifference, $income->income_number);
        }

        DB::commit();

        return redirect()->route('incomes.index')
            ->with('success', 'Gelir başarıyla güncellendi.');
    }

    /**
     * Remove the specified income with comprehensive validation
     */
    public function destroy($id)
    {
        $income = DB::table('incomes')->where('id', $id)->first();

        if (!$income) {
            return redirect()->route('incomes.index')
                ->with('error', 'Gelir bulunamadı.');
        }

        DB::beginTransaction();

        // Reverse account balance (sadece hesap seçilmişse)
        if ($income->account_id) {
            $this->updateAccountBalance($income->account_id, $income->amount, 'debit');
        }

        // Delete related records
        DB::table('income_items')->where('income_id', $id)->delete();
        DB::table('transactions')->where('reference_id', $id)->where('reference_type', 'income')->delete();

        if ($income->customer_id) {
            DB::table('customers_account_transactions')
                ->where('customer_id', $income->customer_id)
                ->where('type', 'income')
                ->delete();

            // Reverse customer balance
            DB::table('customers')
                ->where('id', $income->customer_id)
                ->decrement('current_balance', $income->amount);
        }

        // Delete income record
        DB::table('incomes')->where('id', $id)->delete();

        DB::commit();

        return redirect()->route('incomes.index')
            ->with('success', 'Gelir başarıyla silindi.');
    }

    /**
     * Delete income (AJAX route)
     */
    public function deleteIncome($id)
    {
        $income = DB::table('incomes')->where('id', $id)->first();

        if (!$income) {
            return response()->json(['success' => false, 'message' => 'Gelir bulunamadı.']);
        }

        DB::beginTransaction();

        // Reverse account balance (sadece hesap seçilmişse)
        if ($income->account_id) {
            $this->updateAccountBalance($income->account_id, $income->amount, 'debit');
        }

        // Delete related records
        DB::table('income_items')->where('income_id', $id)->delete();
        DB::table('transactions')->where('reference_id', $id)->where('reference_type', 'income')->delete();

        if ($income->customer_id) {
            DB::table('customers_account_transactions')
                ->where('customer_id', $income->customer_id)
                ->where('type', 'income')
                ->delete();

            // Reverse customer balance
            DB::table('customers')
                ->where('id', $income->customer_id)
                ->decrement('current_balance', $income->amount);
        }

        // Delete income record
        DB::table('incomes')->where('id', $id)->delete();

        DB::commit();

        return response()->json(['success' => true, 'message' => 'Gelir başarıyla silindi.']);
    }

    /**
     * Calculate total amount from line items with precision
     */
    private function calculateTotalAmount(Request $request)
    {
        $totalAmount = 0;

        if (isset($request->item_name) && is_array($request->item_name)) {
            foreach ($request->item_name as $key => $itemName) {
                if (!empty($itemName)) {
                    $unitPrice = floatval($request->unit_price[$key] ?? 0);
                    $quantity = floatval($request->quantity[$key] ?? 1);
                    $totalAmount += round($unitPrice * $quantity, 2);
                }
            }
        }

        return round($totalAmount, 2);
    }

    /**
     * Generate unique income number
     */
    private function generateIncomeNumber(Request $request)
    {
        if (!empty($request->income_number)) {
            return $request->income_number;
        }

        $maxId = DB::table('incomes')->max('id') ?? 0;
        $incomeNumber = 'GEL-' . str_pad($maxId + 1, 6, '0', STR_PAD_LEFT);

        // Ensure uniqueness
        while (DB::table('incomes')->where('income_number', $incomeNumber)->exists()) {
            $maxId++;
            $incomeNumber = 'GEL-' . str_pad($maxId + 1, 6, '0', STR_PAD_LEFT);
        }

        return $incomeNumber;
    }

    /**
     * Insert income items with validation
     */
    private function insertIncomeItems($incomeId, Request $request)
    {
        if (isset($request->item_name) && is_array($request->item_name)) {
            foreach ($request->item_name as $key => $itemName) {
                if (!empty($itemName)) {
                    $unitPrice = floatval($request->unit_price[$key] ?? 0);
                    $quantity = floatval($request->quantity[$key] ?? 1);
                    $amount = round($unitPrice * $quantity, 2);

                    DB::table('income_items')->insert([
                        'income_id' => $incomeId,
                        'item_name' => $itemName,
                        'description' => $request->item_description[$key] ?? null,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'amount' => $amount,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }

    /**
     * Update account balance with transaction safety
     */
    private function updateAccountBalance($accountId, $amount, $type)
    {
        if ($type === 'credit') {
            DB::table('accounts')->where('id', $accountId)->increment('balance', $amount);
        } else {
            DB::table('accounts')->where('id', $accountId)->decrement('balance', $amount);
        }
    }

    /**
     * Create transaction record
     */
    private function createTransactionRecord($incomeId, $incomeNumber, $request, $totalAmount)
    {
        $transactionNumber = 'TXN-' . str_pad(DB::table('transactions')->max('id') + 1, 4, '0', STR_PAD_LEFT);
        DB::table('transactions')->insert([
            'transaction_number' => $transactionNumber,
            'type' => 'income',
            'account_id' => $request->account_id,
            'reference_id' => $incomeId,
            'reference_type' => 'income',
            'amount' => $totalAmount,
            'description' => 'Gelir Eklendi - Belge No: ' . $incomeNumber,
            'date' => $request->date,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Update transaction record
     */
    private function updateTransactionRecord($incomeId, $totalAmount, $request)
    {
        DB::table('transactions')
            ->where('reference_id', $incomeId)
            ->where('reference_type', 'income')
            ->update([
                'account_id' => $request->account_id,
                'amount' => $totalAmount,
                'description' => 'Gelir Güncellendi - Belge No: ' . $request->income_number,
                'date' => $request->date,
                'updated_at' => now()
            ]);
    }

    /**
     * Create customer transaction
     */
    private function createCustomerTransaction($customerId, $amount, $incomeNumber, $description = null)
    {
        $description = 'Gelir Eklendi - ' . $incomeNumber;
        if ($description) {
            $description .= ' - ' . $description;
        }

        DB::table('customers_account_transactions')->insert([
            'customer_id' => $customerId,
            'date' => now()->format('Y-m-d'),
            'account' => 'Gelir Hesabı',
            'type' => 'income',
            'amount' => $amount,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Update customer balance
        DB::table('customers')
            ->where('id', $customerId)
            ->increment('current_balance', $amount);
    }

    /**
     * Update customer transaction
     */
    private function updateCustomerTransaction($incomeId, $oldCustomerId, $newCustomerId, $amountDifference, $incomeNumber)
    {
        // Handle old customer balance reversal
        if ($oldCustomerId && $oldCustomerId != $newCustomerId) {
            DB::table('customers')
                ->where('id', $oldCustomerId)
                ->decrement('current_balance', $amountDifference);

            DB::table('customers_account_transactions')
                ->where('customer_id', $oldCustomerId)
                ->where('type', 'income')
                ->delete();
        }

        // Handle new customer transaction
        if ($newCustomerId) {
            $this->createCustomerTransaction($newCustomerId, $amountDifference, $incomeNumber);
        }
    }
}
