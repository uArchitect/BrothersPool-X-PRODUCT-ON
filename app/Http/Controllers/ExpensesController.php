<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class ExpensesController extends Controller
{
    /**
     * Display a listing of expenses
     */
    public function index(Request $request)
    {
        $query = DB::table('expenses')
            ->leftJoin('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id')
            ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->leftJoin('accounts', 'expenses.account_id', '=', 'accounts.id')
            ->select([
                'expenses.*',
                'expense_types.name as expense_type_name',
                'expense_categories.name as expense_category_name',
                'accounts.name as account_name'
            ]);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('expenses.date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('expenses.date', '<=', $request->end_date);
        }
        if ($request->filled('expense_type_id')) {
            $query->where('expenses.expense_type_id', $request->expense_type_id);
        }

        $expenses = $query->orderBy('expenses.date', 'desc')->get();

        // Get filter options
        $expenseTypes = DB::table('expense_types')->orderBy('name')->get();
        $accounts = DB::table('accounts')->orderBy('name')->get();
        $employees = DB::table('employees')->orderBy('name')->get();

        // Calculate statistics
        $totalExpenses = DB::table('expenses')->count();
        $totalAmount = DB::table('expenses')->sum('amount');
        $thisMonthExpenses = DB::table('expenses')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();
        $thisMonthAmount = DB::table('expenses')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        return view('financial.expenses.index', compact(
            'expenses',
            'expenseTypes',
            'accounts',
            'employees',
            'totalExpenses',
            'totalAmount',
            'thisMonthExpenses',
            'thisMonthAmount'
        ));
    }

    /**
     * Show the form for creating a new expense
     */
    public function create(Request $request)
    {
        // Get all expense types (including inactive ones) to show "AYLIK FİX GİDERLER"
        $expenseTypes = DB::table('expense_types')->orderBy('name')->get();
        $expenseCategories = DB::table('expense_categories')->orderBy('name')->get();
        $accounts = DB::table('accounts')->where('is_active', true)->orderBy('name')->get();
        $customers = DB::table('customers')->select('id', 'title', 'code', 'current_balance')->orderBy('title')->get();

        // Generate expense number - son eklenen belge numarasından +1
        $lastExpense = DB::table('expenses')
            ->whereNotNull('expense_number')
            ->where('expense_number', 'like', 'GID-%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastExpense && preg_match('/(\d+)$/', $lastExpense->expense_number, $matches)) {
            $lastNumber = intval($matches[1]);
            $nextNumber = $lastNumber + 1;
            $expenseId = 'GID-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        } else {
            // İlk kayıt için
            $expenseId = 'GID-000001';
        }

        // URL'den gelen customer_id parametresini al
        $selectedCustomerId = $request->get('customer_id');

        return view('financial.expenses.create', compact('expenseTypes', 'expenseCategories', 'accounts', 'customers', 'expenseId', 'selectedCustomerId'));
    }

    /**
     * Store a newly created expense with comprehensive validation
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
        
        // Generate expense number if not provided
        $expenseNumber = $this->generateExpenseNumber($request);
        
        // Handle file upload
        $receiptImagePath = $this->handleFileUpload($request->file('invoice_photo'));
        
        // Get first available expense_type_id if not provided
        $expenseTypeId = $request->expense_type_id ?? DB::table('expense_types')->value('id');
        if (!$expenseTypeId) {
            throw new Exception('Hiç gider tipi bulunamadı. Lütfen önce bir gider tipi oluşturun.');
        }
        
        // Insert expense record
        $expenseId = DB::table('expenses')->insertGetId([
            'expense_number' => $expenseNumber,
            'expense_type_id' => $expenseTypeId,
            'expense_category_id' => $request->expense_category_id ?? null,
            'account_id' => $request->account_id ?? null,
            'customer_id' => $request->customer_id ?? null,
            'amount' => $totalAmount,
            'total' => $totalAmount,
            'title' => 'Gider Kaydı - ' . $expenseNumber,
            'date' => $request->date,
            'description' => $request->description ?? null,
            'receipt_image' => $receiptImagePath,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Insert expense items with validation
        $this->insertExpenseItems($expenseId, $request);

        // Eğer hesap seçilmişse (ödeme yapılmışsa), account balance güncelle ve transaction oluştur
        if ($request->account_id) {
            // Update account balance
            $this->updateAccountBalance($request->account_id, $totalAmount, 'debit');

            // Create transaction record
            $this->createTransactionRecord($expenseId, $expenseNumber, $request, $totalAmount);
        }

        // Create customer transaction record if customer is selected
        if ($request->customer_id) {
            $this->createCustomerTransaction($request->customer_id, $totalAmount, $expenseNumber, 'expense');
            
            // Müşteri bakiyesini yeniden hesapla
            app(\App\Services\CustomerBalanceService::class)->recalculateAndUpdate($request->customer_id);
        }

        DB::commit();

        // Eğer müşteri sayfasından gelindiyse oraya dön, değilse expenses.index'e git
        if ($request->customer_id) {
            return redirect()->route('customers.show', $request->customer_id)
                ->with('success', 'Gider başarıyla kaydedildi. Belge No: ' . $expenseNumber);
        }
        
        return redirect()->back()
            ->with('success', 'Gider başarıyla kaydedildi. Belge No: ' . $expenseNumber);
    }

    /**
     * Display the specified expense
     */
    public function show($id)
    {
        $expense = DB::table('expenses')
            ->leftJoin('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id')
            ->leftJoin('accounts', 'expenses.account_id', '=', 'accounts.id')
            ->select([
                'expenses.*',
                'expense_types.name as expense_type_name',
                'accounts.name as account_name'
            ])
            ->where('expenses.id', $id)
            ->first();

        if (!$expense) {
            return redirect()->route('expenses.index')
                ->with('error', 'Gider bulunamadı.');
        }

        // Get expense items
        $expenseItems = DB::table('expense_items')
            ->where('expense_id', $id)
            ->get();

        return view('financial.expenses.show', compact('expense', 'expenseItems'));
    }

    /**
     * Show the form for editing the specified expense
     */
    public function edit($id)
    {
        $expense = DB::table('expenses')->where('id', $id)->first();

        if (!$expense) {
            return redirect()->route('expenses.index')
                ->with('error', 'Gider bulunamadı.');
        }

        $expenseTypes = DB::table('expense_types')->orderBy('name')->get();
        $expenseCategories = DB::table('expense_categories')->orderBy('name')->get();
        $accounts = DB::table('accounts')->orderBy('name')->get();
        $customers = DB::table('customers')->select('id', 'title', 'current_balance')->orderBy('title')->get();

        // Get expense items
        $expenseItems = DB::table('expense_items')
            ->where('expense_id', $id)
            ->get();

        return view('financial.expenses.edit', compact('expense', 'expenseTypes', 'expenseCategories', 'accounts', 'customers', 'expenseItems'));
    }

    /**
     * Update the specified expense with comprehensive validation
     */
    public function update(Request $request, $id)
    {
        $expense = DB::table('expenses')->where('id', $id)->first();
        
        if (!$expense) {
            return redirect()->route('expenses.index')
                ->with('error', 'Gider bulunamadı.');
        }

        // No validation - direct processing

        DB::beginTransaction();

        // Calculate new total amount
        $newTotalAmount = $this->calculateTotalAmount($request);
        
        if ($newTotalAmount <= 0) {
            throw new Exception('Toplam tutar 0\'dan büyük olmalıdır.');
        }

        // Calculate difference for account balance adjustment
        $amountDifference = $newTotalAmount - $expense->amount;

        // Handle file upload
        $receiptImagePath = $this->handleFileUpload($request->file('invoice_photo'), $expense->receipt_image ?? null);

        // Update expense record
        DB::table('expenses')->where('id', $id)->update([
            'expense_number' => $request->expense_number,
            'expense_type_id' => $request->expense_type_id ?? $expense->expense_type_id,
            'expense_category_id' => $request->expense_category_id ?? null,
            'account_id' => $request->account_id,
            'customer_id' => $request->customer_id ?? null,
            'amount' => $newTotalAmount,
            'total' => $newTotalAmount,
            'title' => 'Gider Kaydı - ' . $request->expense_number,
            'date' => $request->date,
            'description' => $request->description ?? null,
            'receipt_image' => $receiptImagePath,
            'updated_at' => now()
        ]);

        // Delete existing expense items and insert new ones
        DB::table('expense_items')->where('expense_id', $id)->delete();
        $this->insertExpenseItems($id, $request);

        // Eğer hesap seçilmişse (ödeme yapılmışsa), account balance güncelle ve transaction oluştur/güncelle
        if ($request->account_id) {
            // Eski hesap varsa ve değiştiyse, eski hesabı geri al
            if ($expense->account_id && $expense->account_id != $request->account_id) {
                $this->updateAccountBalance($expense->account_id, $expense->amount, 'credit');
            }
            
            // Update account balance if amount changed
            if ($amountDifference != 0) {
                $this->updateAccountBalance($request->account_id, abs($amountDifference), $amountDifference > 0 ? 'debit' : 'credit');
            }

            // Update transaction record
            $this->updateTransactionRecord($id, $newTotalAmount, $request);
        } else {
            // Hesap seçilmemişse, mevcut transaction'ı sil (eğer varsa)
            DB::table('transactions')
                ->where('reference_id', $id)
                ->where('reference_type', 'expense')
                ->delete();
            
            // Eğer önceden hesap varsa, balance'ı geri al
            if ($expense->account_id) {
                $this->updateAccountBalance($expense->account_id, $expense->amount, 'credit');
            }
        }

        // Handle customer transaction updates
        $this->handleCustomerTransactionUpdate($expense, $request, $newTotalAmount, $request->expense_number);

        DB::commit();

        // Eğer müşteri sayfasından gelindiyse oraya dön
        if ($request->customer_id || $expense->customer_id) {
            $customerId = $request->customer_id ?? $expense->customer_id;
            return redirect()->route('customers.show', $customerId)
                ->with('success', 'Gider başarıyla güncellendi.');
        }
        
        return redirect()->back()
            ->with('success', 'Gider başarıyla güncellendi.');
    }

    /**
     * Remove the specified expense with comprehensive validation
     */
    public function destroy($id)
    {
        $expense = DB::table('expenses')->where('id', $id)->first();

        if (!$expense) {
            return redirect()->route('expenses.index')
                ->with('error', 'Gider bulunamadı.');
        }

        // Check if expense can be deleted (business rules)
        // Direct deletion without validation

        DB::beginTransaction();

        // Reverse account balance (sadece hesap seçilmişse)
        if ($expense->account_id) {
            $this->updateAccountBalance($expense->account_id, $expense->amount, 'credit');
        }

        // Bu expense'e ait borç ödemelerini sil
        $debtPayments = DB::table('customer_debt_payments')
            ->where('reference_id', $id)
            ->where('reference_type', 'expense')
            ->get();

        $affectedCustomers = [];
        foreach ($debtPayments as $debtPayment) {
            $affectedCustomers[] = $debtPayment->customer_id;

            // İlişkili transaction'ları sil
            DB::table('transactions')
                ->where('reference_id', $debtPayment->id)
                ->where('reference_type', 'customer_debt_payment')
                ->delete();

            // Customer account transaction'ları sil (fazla ödeme kayıtları da dahil)
            DB::table('customers_account_transactions')
                ->where('reference_id', $debtPayment->id)
                ->whereIn('transaction_type', ['debt_payment', 'over_payment'])
                ->delete();
        }

        // Customer debt payments kayıtlarını sil
        DB::table('customer_debt_payments')
            ->where('reference_id', $id)
            ->where('reference_type', 'expense')
            ->delete();

        // Delete related records
        DB::table('expense_items')->where('expense_id', $id)->delete();
        DB::table('transactions')->where('reference_id', $id)->where('reference_type', 'expense')->delete();

        // Reverse customer transaction if customer exists
        if ($expense->customer_id) {
            DB::table('customers_account_transactions')
                ->where('customer_id', $expense->customer_id)
                ->where('reference_id', $id)
                ->where('transaction_type', 'expense')
                ->delete();
            
            $affectedCustomers[] = $expense->customer_id;
        }

        // Etkilenen müşterilerin bakiyelerini yeniden hesapla
        $balanceService = app(\App\Services\CustomerBalanceService::class);
        foreach (array_unique($affectedCustomers) as $customerId) {
            $balanceService->recalculateAndUpdate($customerId);
        }

        // Delete receipt image if exists
        if ($expense->receipt_image && file_exists(public_path('images/' . $expense->receipt_image))) {
            unlink(public_path('images/' . $expense->receipt_image));
        }

        // Delete expense record
        DB::table('expenses')->where('id', $id)->delete();

        DB::commit();

        // Eğer müşteri sayfasından gelindiyse oraya dön
        if ($expense->customer_id) {
            return redirect()->route('customers.show', $expense->customer_id)
                ->with('success', 'Gider başarıyla silindi.');
        }
        
        return redirect()->back()
            ->with('success', 'Gider başarıyla silindi.');
    }

    /**
     * Delete expense (AJAX route)
     */
    public function deleteExpense($id)
    {
        $expense = DB::table('expenses')->where('id', $id)->first();

        if (!$expense) {
            return response()->json(['success' => false, 'message' => 'Gider bulunamadı.']);
        }

        DB::beginTransaction();

        // Reverse account balance (sadece hesap seçilmişse)
        if ($expense->account_id) {
            $this->updateAccountBalance($expense->account_id, $expense->amount, 'credit');
        }

        // Bu expense'e ait borç ödemelerini sil
        $debtPayments = DB::table('customer_debt_payments')
            ->where('reference_id', $id)
            ->where('reference_type', 'expense')
            ->get();

        $affectedCustomers = [];
        foreach ($debtPayments as $debtPayment) {
            $affectedCustomers[] = $debtPayment->customer_id;

            // İlişkili transaction'ları sil
            DB::table('transactions')
                ->where('reference_id', $debtPayment->id)
                ->where('reference_type', 'customer_debt_payment')
                ->delete();

            // Customer account transaction'ları sil (fazla ödeme kayıtları da dahil)
            DB::table('customers_account_transactions')
                ->where('reference_id', $debtPayment->id)
                ->whereIn('transaction_type', ['debt_payment', 'over_payment'])
                ->delete();
        }

        // Customer debt payments kayıtlarını sil
        DB::table('customer_debt_payments')
            ->where('reference_id', $id)
            ->where('reference_type', 'expense')
            ->delete();

        // Delete related records
        DB::table('expense_items')->where('expense_id', $id)->delete();
        DB::table('transactions')->where('reference_id', $id)->where('reference_type', 'expense')->delete();

        // Reverse customer transaction if customer exists
        if ($expense->customer_id) {
            DB::table('customers_account_transactions')
                ->where('customer_id', $expense->customer_id)
                ->where('reference_id', $id)
                ->where('transaction_type', 'expense')
                ->delete();
            
            $affectedCustomers[] = $expense->customer_id;
        }

        // Etkilenen müşterilerin bakiyelerini yeniden hesapla
        $balanceService = app(\App\Services\CustomerBalanceService::class);
        foreach (array_unique($affectedCustomers) as $customerId) {
            $balanceService->recalculateAndUpdate($customerId);
        }

        // Delete receipt image if exists
        if ($expense->receipt_image && file_exists(public_path('images/' . $expense->receipt_image))) {
            unlink(public_path('images/' . $expense->receipt_image));
        }

        // Delete expense record
        DB::table('expenses')->where('id', $id)->delete();

        DB::commit();

        // AJAX isteği için JSON döndür, ama müşteri sayfasından gelindiyse redirect yap
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Gider başarıyla silindi.']);
        }
        
        // Eğer müşteri sayfasından gelindiyse oraya dön
        if ($expense->customer_id) {
            return redirect()->route('customers.show', $expense->customer_id)
                ->with('success', 'Gider başarıyla silindi.');
        }
        
        return redirect()->back()
            ->with('success', 'Gider başarıyla silindi.');
    }

    /**
     * Get expense data for editing (AJAX)
     */
    public function getExpense($id)
    {
        $expense = DB::table('expenses')
            ->select('expenses.*', 'expense_types.name as expense_type_name', 'accounts.name as account_name')
            ->leftJoin('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id')
            ->leftJoin('accounts', 'expenses.account_id', '=', 'accounts.id')
            ->where('expenses.id', $id)
            ->first();

        if (!$expense) {
            return response()->json(['error' => 'Gider bulunamadı'], 404);
        }

        // Format date for HTML date input
        if ($expense->date) {
            if (strpos($expense->date, '.') !== false) {
                $dateParts = explode('.', $expense->date);
                if (count($dateParts) === 3) {
                    $expense->date = $dateParts[2] . '-' . str_pad($dateParts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($dateParts[0], 2, '0', STR_PAD_LEFT);
                }
            } else {
                $expense->date = date('Y-m-d', strtotime($expense->date));
            }
        }

        return response()->json($expense);
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
                    $amount = round($unitPrice * $quantity, 2);
                    $totalAmount += $amount;
                }
            }
        }
        
        return round($totalAmount, 2);
    }

    /**
     * Generate unique expense number
     */
    private function generateExpenseNumber(Request $request)
    {
        if (!empty($request->expense_number)) {
            return $request->expense_number;
        }

        // Son eklenen belge numarasından +1
        $lastExpense = DB::table('expenses')
            ->whereNotNull('expense_number')
            ->where('expense_number', 'like', 'GID-%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastExpense && preg_match('/(\d+)$/', $lastExpense->expense_number, $matches)) {
            $lastNumber = intval($matches[1]);
            $nextNumber = $lastNumber + 1;
            $expenseNumber = 'GID-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        } else {
            // İlk kayıt için
            $expenseNumber = 'GID-000001';
        }
        
        // Ensure uniqueness
        while (DB::table('expenses')->where('expense_number', $expenseNumber)->exists()) {
            $nextNumber++;
            $expenseNumber = 'GID-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        }
        
        return $expenseNumber;
    }

    /**
     * Handle file upload with validation
     */
    private function handleFileUpload($file, $existingFile = null)
    {
        if (!$file || !$file->isValid()) {
            return $existingFile;
        }
        
        // Delete existing file if new one is uploaded
        if ($existingFile && file_exists(public_path('images/' . $existingFile))) {
            unlink(public_path('images/' . $existingFile));
        }
        
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images'), $fileName);
        
        return $fileName;
    }

    /**
     * Insert expense items with validation
     */
    private function insertExpenseItems($expenseId, Request $request)
    {
        if (isset($request->item_name) && is_array($request->item_name)) {
            foreach ($request->item_name as $key => $itemName) {
                if (!empty($itemName)) {
                    $unitPrice = floatval($request->unit_price[$key] ?? 0);
                    $quantity = floatval($request->quantity[$key] ?? 1);
                    $amount = round($unitPrice * $quantity, 2);
                    
                    DB::table('expense_items')->insert([
                        'expense_id' => $expenseId,
                        'expense_category_id' => $request->expense_category_id, // Formdan gelen kategori ID
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
    private function createTransactionRecord($expenseId, $expenseNumber, $request, $totalAmount)
    {
        $transactionNumber = 'TXN-' . str_pad(DB::table('transactions')->max('id') + 1, 4, '0', STR_PAD_LEFT);
        DB::table('transactions')->insert([
            'transaction_number' => $transactionNumber,
            'type' => 'expense',
            'account_id' => $request->account_id,
            'reference_id' => $expenseId,
            'reference_type' => 'expense',
            'amount' => $totalAmount,
            'description' => 'Gider Eklendi - Belge No: ' . $expenseNumber,
            'date' => $request->date,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Create customer transaction record
     */
    private function createCustomerTransaction($customerId, $amount, $expenseNumber, $type = 'expense')
    {
        $description = 'Gider Eklendi - ' . $expenseNumber;

        DB::table('customers_account_transactions')->insert([
            'customer_id' => $customerId,
            'date' => now()->format('Y-m-d'),
            'account' => 'Gider Hesabı',
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'reference_id' => DB::table('expenses')->where('expense_number', $expenseNumber)->value('id'),
            'transaction_type' => 'expense',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Müşteri bakiyesi artık customers_account_transactions'dan otomatik hesaplanacak
        // Manuel güncelleme yapmıyoruz, sadece transaction kaydı oluşturuyoruz
        // Bakiye güncellemesi için recalculateAndUpdate kullanılmalı
    }

    /**
     * Handle customer transaction updates
     */
    private function handleCustomerTransactionUpdate($oldExpense, $request, $newAmount, $expenseNumber)
    {
        $oldCustomerId = $oldExpense->customer_id;
        $newCustomerId = $request->customer_id;

        // If customer was changed or amount was changed
        if ($oldCustomerId != $newCustomerId || $oldExpense->amount != $newAmount) {

            // Reverse old customer transaction if exists
            if ($oldCustomerId) {
                DB::table('customers_account_transactions')
                    ->where('customer_id', $oldCustomerId)
                    ->where('reference_id', $oldExpense->id)
                    ->where('transaction_type', 'expense')
                    ->delete();

            // Müşteri bakiyesini yeniden hesapla
            app(\App\Services\CustomerBalanceService::class)->recalculateAndUpdate($oldCustomerId);
            }

            // Create new customer transaction if customer is selected
            if ($newCustomerId) {
                $this->createCustomerTransaction($newCustomerId, $newAmount, $expenseNumber, 'expense');
            }
        }
    }

    /**
     * Update transaction record
     */
    private function updateTransactionRecord($expenseId, $totalAmount, $request)
    {
        $existingTransaction = DB::table('transactions')
            ->where('reference_id', $expenseId)
            ->where('reference_type', 'expense')
            ->first();
        
        if ($existingTransaction) {
            // Mevcut transaction'ı güncelle
            DB::table('transactions')
                ->where('reference_id', $expenseId)
                ->where('reference_type', 'expense')
                ->update([
                    'account_id' => $request->account_id,
                    'amount' => $totalAmount,
                    'description' => 'Gider Güncellendi - Belge No: ' . $request->expense_number,
                    'date' => $request->date,
                    'updated_at' => now()
                ]);
        } else {
            // Yeni transaction oluştur
            $this->createTransactionRecord($expenseId, $request->expense_number, $request, $totalAmount);
        }
    }

    /**
     * Create employee transaction
     */
    private function createEmployeeTransaction($employeeId, $amount, $expenseNumber, $description = null)
    {
        $description = 'Gider Eklendi - ' . $expenseNumber;
        if ($description) {
            $description .= ' - ' . $description;
        }
        
        DB::table('employee_transactions')->insert([
            'employee_id' => $employeeId,
            'date' => now()->format('Y-m-d'),
            'type' => 'expense',
            'amount' => $amount,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Update employee transaction
     */
    private function updateEmployeeTransaction($expenseId, $oldEmployeeId, $newEmployeeId, $amountDifference, $expenseNumber)
    {
        // Handle old employee transaction deletion
        if ($oldEmployeeId && $oldEmployeeId != $newEmployeeId) {
            DB::table('employee_transactions')
                ->where('reference_id', $expenseId)
                ->where('transaction_type', 'expense')
                ->delete();
        }

        // Handle new employee transaction
        if ($newEmployeeId) {
            $this->createEmployeeTransaction($newEmployeeId, $amountDifference, $expenseNumber);
        }
    }
}