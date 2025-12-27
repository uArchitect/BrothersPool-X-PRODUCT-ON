<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SystemIntegrityService
{
    protected $fixes = [];
    protected $errors = [];

    /**
     * Tüm sistem kontrollerini çalıştır
     */
    public function runAllChecks($autoFix = true)
    {
        $this->fixes = [];
        $this->errors = [];

        $this->checkChecksTransactions($autoFix);
        $this->checkPromissoryNotesTransactions($autoFix);
        $this->checkAccountBalances($autoFix);
        $this->checkCustomerBalances($autoFix);
        $this->checkOrphanedTransactions($autoFix);

        return [
            'fixes' => $this->fixes,
            'errors' => $this->errors,
            'total_fixes' => count($this->fixes),
            'total_errors' => count($this->errors)
        ];
    }

    /**
     * Çekler için eksik transaction kayıtlarını kontrol et ve düzelt
     */
    public function checkChecksTransactions($autoFix = true)
    {
        // Account_id'si olan ama transaction kaydı olmayan çekleri bul
        $checksWithoutTransactions = DB::table('checks')
            ->leftJoin('transactions', function($join) {
                $join->on('transactions.reference_id', '=', 'checks.id')
                     ->where('transactions.reference_type', '=', 'check');
            })
            ->whereNotNull('checks.account_id')
            ->whereNull('transactions.id')
            ->select('checks.*')
            ->get();

        foreach ($checksWithoutTransactions as $check) {
            if ($autoFix) {
                try {
                    $this->createCheckTransaction($check);
                    $this->fixes[] = [
                        'type' => 'check_transaction',
                        'message' => "Çek #{$check->id} için transaction kaydı oluşturuldu",
                        'check_id' => $check->id
                    ];
                } catch (\Exception $e) {
                    $this->errors[] = [
                        'type' => 'check_transaction',
                        'message' => "Çek #{$check->id} için transaction oluşturulamadı: " . $e->getMessage(),
                        'check_id' => $check->id
                    ];
                    Log::error("System Integrity: Check transaction creation failed", [
                        'check_id' => $check->id,
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                $this->errors[] = [
                    'type' => 'check_transaction',
                    'message' => "Çek #{$check->id} için transaction kaydı eksik",
                    'check_id' => $check->id
                ];
            }
        }

        return $checksWithoutTransactions->count();
    }

    /**
     * Senetler için eksik transaction kayıtlarını kontrol et ve düzelt
     */
    public function checkPromissoryNotesTransactions($autoFix = true)
    {
        // Account_id'si olan ama transaction kaydı olmayan senetleri bul
        $notesWithoutTransactions = DB::table('promissory_notes')
            ->leftJoin('transactions', function($join) {
                $join->on('transactions.reference_id', '=', 'promissory_notes.id')
                     ->where('transactions.reference_type', '=', 'promissory_note');
            })
            ->whereNotNull('promissory_notes.account_id')
            ->whereNull('transactions.id')
            ->select('promissory_notes.*')
            ->get();

        foreach ($notesWithoutTransactions as $note) {
            if ($autoFix) {
                try {
                    $this->createPromissoryNoteTransaction($note);
                    $this->fixes[] = [
                        'type' => 'promissory_note_transaction',
                        'message' => "Senet #{$note->id} için transaction kaydı oluşturuldu",
                        'note_id' => $note->id
                    ];
                } catch (\Exception $e) {
                    $this->errors[] = [
                        'type' => 'promissory_note_transaction',
                        'message' => "Senet #{$note->id} için transaction oluşturulamadı: " . $e->getMessage(),
                        'note_id' => $note->id
                    ];
                    Log::error("System Integrity: Promissory note transaction creation failed", [
                        'note_id' => $note->id,
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                $this->errors[] = [
                    'type' => 'promissory_note_transaction',
                    'message' => "Senet #{$note->id} için transaction kaydı eksik",
                    'note_id' => $note->id
                ];
            }
        }

        return $notesWithoutTransactions->count();
    }

    /**
     * Kasa bakiyelerini kontrol et ve düzelt
     */
    public function checkAccountBalances($autoFix = true)
    {
        $accounts = DB::table('accounts')->get();
        $issues = [];

        foreach ($accounts as $account) {
            // Gerçek bakiyeyi hesapla
            $calculatedBalance = $this->calculateAccountBalance($account->id);

            // Fark varsa
            if (abs($account->balance - $calculatedBalance) > 0.01) {
                $difference = $calculatedBalance - $account->balance;
                
                if ($autoFix) {
                    try {
                        DB::table('accounts')
                            ->where('id', $account->id)
                            ->update(['balance' => $calculatedBalance]);

                        $this->fixes[] = [
                            'type' => 'account_balance',
                            'message' => "Kasa '{$account->name}' bakiyesi düzeltildi. Fark: " . number_format($difference, 2),
                            'account_id' => $account->id,
                            'old_balance' => $account->balance,
                            'new_balance' => $calculatedBalance,
                            'difference' => $difference
                        ];
                    } catch (\Exception $e) {
                        $this->errors[] = [
                            'type' => 'account_balance',
                            'message' => "Kasa '{$account->name}' bakiyesi düzeltilemedi: " . $e->getMessage(),
                            'account_id' => $account->id
                        ];
                    }
                } else {
                    $this->errors[] = [
                        'type' => 'account_balance',
                        'message' => "Kasa '{$account->name}' bakiyesi hatalı. Mevcut: " . number_format($account->balance, 2) . ", Olması gereken: " . number_format($calculatedBalance, 2),
                        'account_id' => $account->id,
                        'current_balance' => $account->balance,
                        'calculated_balance' => $calculatedBalance,
                        'difference' => $difference
                    ];
                }
            }
        }

        return count($issues);
    }

    /**
     * Müşteri bakiyelerini kontrol et ve düzelt
     */
    public function checkCustomerBalances($autoFix = true)
    {
        $customers = DB::table('customers')->get();
        $issues = [];

        foreach ($customers as $customer) {
            // Gerçek bakiyeyi hesapla
            $calculatedBalance = $this->calculateCustomerBalance($customer->id);

            // Fark varsa
            if (abs($customer->current_balance - $calculatedBalance) > 0.01) {
                $difference = $calculatedBalance - $customer->current_balance;
                
                if ($autoFix) {
                    try {
                        // CustomerBalanceService kullanarak güncelle
                        $balanceService = app(\App\Services\CustomerBalanceService::class);
                        $balanceService->recalculateAndUpdate($customer->id);
                        $calculatedBalance = $balanceService->calculateBalance($customer->id);

                        $this->fixes[] = [
                            'type' => 'customer_balance',
                            'message' => "Müşteri '{$customer->title}' bakiyesi düzeltildi. Fark: " . number_format($difference, 2),
                            'customer_id' => $customer->id,
                            'old_balance' => $customer->current_balance,
                            'new_balance' => $calculatedBalance,
                            'difference' => $difference
                        ];
                    } catch (\Exception $e) {
                        $this->errors[] = [
                            'type' => 'customer_balance',
                            'message' => "Müşteri '{$customer->title}' bakiyesi düzeltilemedi: " . $e->getMessage(),
                            'customer_id' => $customer->id
                        ];
                    }
                } else {
                    $this->errors[] = [
                        'type' => 'customer_balance',
                        'message' => "Müşteri '{$customer->title}' bakiyesi hatalı. Mevcut: " . number_format($customer->current_balance, 2) . ", Olması gereken: " . number_format($calculatedBalance, 2),
                        'customer_id' => $customer->id,
                        'current_balance' => $customer->current_balance,
                        'calculated_balance' => $calculatedBalance,
                        'difference' => $difference
                    ];
                }
            }
        }

        return count($issues);
    }

    /**
     * Orphaned (sahipsiz) transaction kayıtlarını kontrol et
     */
    public function checkOrphanedTransactions($autoFix = true)
    {
        $orphanedTransactions = [];

        // Check transactions
        $checkTransactions = DB::table('transactions')
            ->where('reference_type', 'check')
            ->leftJoin('checks', 'transactions.reference_id', '=', 'checks.id')
            ->whereNull('checks.id')
            ->select('transactions.*')
            ->get();

        foreach ($checkTransactions as $transaction) {
            if ($autoFix) {
                try {
                    DB::table('transactions')->where('id', $transaction->id)->delete();
                    $this->fixes[] = [
                        'type' => 'orphaned_transaction',
                        'message' => "Sahipsiz transaction kaydı silindi (Check #{$transaction->reference_id})",
                        'transaction_id' => $transaction->id
                    ];
                } catch (\Exception $e) {
                    $this->errors[] = [
                        'type' => 'orphaned_transaction',
                        'message' => "Sahipsiz transaction silinemedi: " . $e->getMessage(),
                        'transaction_id' => $transaction->id
                    ];
                }
            } else {
                $this->errors[] = [
                    'type' => 'orphaned_transaction',
                    'message' => "Sahipsiz transaction kaydı bulundu (Check #{$transaction->reference_id})",
                    'transaction_id' => $transaction->id
                ];
            }
        }

        // Promissory note transactions
        $noteTransactions = DB::table('transactions')
            ->where('reference_type', 'promissory_note')
            ->leftJoin('promissory_notes', 'transactions.reference_id', '=', 'promissory_notes.id')
            ->whereNull('promissory_notes.id')
            ->select('transactions.*')
            ->get();

        foreach ($noteTransactions as $transaction) {
            if ($autoFix) {
                try {
                    DB::table('transactions')->where('id', $transaction->id)->delete();
                    $this->fixes[] = [
                        'type' => 'orphaned_transaction',
                        'message' => "Sahipsiz transaction kaydı silindi (Promissory Note #{$transaction->reference_id})",
                        'transaction_id' => $transaction->id
                    ];
                } catch (\Exception $e) {
                    $this->errors[] = [
                        'type' => 'orphaned_transaction',
                        'message' => "Sahipsiz transaction silinemedi: " . $e->getMessage(),
                        'transaction_id' => $transaction->id
                    ];
                }
            } else {
                $this->errors[] = [
                    'type' => 'orphaned_transaction',
                    'message' => "Sahipsiz transaction kaydı bulundu (Promissory Note #{$transaction->reference_id})",
                    'transaction_id' => $transaction->id
                ];
            }
        }

        return count($checkTransactions) + count($noteTransactions);
    }

    /**
     * Çek için transaction kaydı oluştur
     */
    protected function createCheckTransaction($check)
    {
        $transactionNumber = 'TXN-' . str_pad((DB::table('transactions')->max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);
        $transactionType = $check->type === 'verilen' ? 'expense' : 'income';

        DB::table('transactions')->insert([
            'transaction_number' => $transactionNumber,
            'type' => $transactionType,
            'account_id' => $check->account_id,
            'reference_id' => $check->id,
            'reference_type' => 'check',
            'amount' => $check->amount,
            'description' => 'Çek #' . ($check->check_number ?? $check->id) . ' - ' . ($check->type === 'verilen' ? 'Verilen Çek' : 'Alınan Çek'),
            'date' => $check->issue_date,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Senet için transaction kaydı oluştur
     */
    protected function createPromissoryNoteTransaction($note)
    {
        $transactionNumber = 'TXN-' . str_pad((DB::table('transactions')->max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);
        $transactionType = $note->type === 'verilen' ? 'expense' : 'income';

        DB::table('transactions')->insert([
            'transaction_number' => $transactionNumber,
            'type' => $transactionType,
            'account_id' => $note->account_id,
            'reference_id' => $note->id,
            'reference_type' => 'promissory_note',
            'amount' => $note->amount,
            'description' => 'Senet #' . ($note->note_number ?? $note->id) . ' - ' . ($note->type === 'verilen' ? 'Verilen Senet' : 'Alınan Senet'),
            'date' => $note->issue_date,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Hesap bakiyesini hesapla
     */
    protected function calculateAccountBalance($accountId)
    {
        $income = DB::table('transactions')
            ->where('account_id', $accountId)
            ->where('type', 'income')
            ->sum('amount') ?? 0;

        $expense = DB::table('transactions')
            ->where('account_id', $accountId)
            ->where('type', 'expense')
            ->sum('amount') ?? 0;

        // Transfer işlemleri için (gelen transferler +, giden transferler -)
        // Bu kısım transfer mantığına göre düzenlenebilir
        $transferIn = DB::table('transactions')
            ->where('account_id', $accountId)
            ->where('type', 'transfer')
            ->where('amount', '>', 0)
            ->sum('amount') ?? 0;

        $transferOut = abs(DB::table('transactions')
            ->where('account_id', $accountId)
            ->where('type', 'transfer')
            ->where('amount', '<', 0)
            ->sum('amount') ?? 0);

        return $income - $expense + $transferIn - $transferOut;
    }

    /**
     * Müşteri bakiyesini hesapla
     */
    protected function calculateCustomerBalance($customerId)
    {
        // CustomerBalanceService kullanarak hesapla
        $balanceService = app(\App\Services\CustomerBalanceService::class);
        return $balanceService->calculateBalance($customerId);
    }

    /**
     * Belirli bir hesabı detaylı kontrol et
     */
    public function checkAccountDetails($accountIdOrName, $autoFix = false)
    {
        // Hesabı bul
        $account = is_numeric($accountIdOrName) 
            ? DB::table('accounts')->where('id', $accountIdOrName)->first()
            : DB::table('accounts')->where('name', 'like', '%' . $accountIdOrName . '%')->first();

        if (!$account) {
            return [
                'error' => 'Hesap bulunamadı',
                'account' => null
            ];
        }

        $calculatedBalance = $this->calculateAccountBalance($account->id);
        $difference = $calculatedBalance - $account->balance;

        // Tüm transaction'ları getir
        $transactions = DB::table('transactions')
            ->where('account_id', $account->id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Çek işlemlerini kontrol et
        $checks = DB::table('checks')
            ->where('account_id', $account->id)
            ->get();

        $checkIssues = [];
        foreach ($checks as $check) {
            $hasTransaction = DB::table('transactions')
                ->where('reference_id', $check->id)
                ->where('reference_type', 'check')
                ->exists();

            if (!$hasTransaction) {
                $checkIssues[] = [
                    'check_id' => $check->id,
                    'check_number' => $check->check_number,
                    'amount' => $check->amount,
                    'type' => $check->type,
                    'issue_date' => $check->issue_date,
                    'status' => 'missing_transaction'
                ];
            }
        }

        // Senet işlemlerini kontrol et
        $notes = DB::table('promissory_notes')
            ->where('account_id', $account->id)
            ->get();

        $noteIssues = [];
        foreach ($notes as $note) {
            $hasTransaction = DB::table('transactions')
                ->where('reference_id', $note->id)
                ->where('reference_type', 'promissory_note')
                ->exists();

            if (!$hasTransaction) {
                $noteIssues[] = [
                    'note_id' => $note->id,
                    'note_number' => $note->note_number,
                    'amount' => $note->amount,
                    'type' => $note->type,
                    'issue_date' => $note->issue_date,
                    'status' => 'missing_transaction'
                ];
            }
        }

        // İstatistikler
        $stats = [
            'total_income' => DB::table('transactions')
                ->where('account_id', $account->id)
                ->where('type', 'income')
                ->sum('amount') ?? 0,
            'total_expense' => DB::table('transactions')
                ->where('account_id', $account->id)
                ->where('type', 'expense')
                ->sum('amount') ?? 0,
            'transaction_count' => $transactions->count(),
            'check_count' => $checks->count(),
            'note_count' => $notes->count(),
            'check_issues_count' => count($checkIssues),
            'note_issues_count' => count($noteIssues)
        ];

        $result = [
            'account' => $account,
            'current_balance' => $account->balance,
            'calculated_balance' => $calculatedBalance,
            'difference' => $difference,
            'is_balanced' => abs($difference) <= 0.01,
            'transactions' => $transactions,
            'checks' => $checks,
            'notes' => $notes,
            'check_issues' => $checkIssues,
            'note_issues' => $noteIssues,
            'stats' => $stats
        ];

        // Otomatik düzeltme
        if ($autoFix && abs($difference) > 0.01) {
            try {
                DB::table('accounts')
                    ->where('id', $account->id)
                    ->update(['balance' => $calculatedBalance]);
                $result['fixed'] = true;
                $result['fix_message'] = "Bakiye düzeltildi: " . number_format($account->balance, 2) . " → " . number_format($calculatedBalance, 2);
            } catch (\Exception $e) {
                $result['fix_error'] = $e->getMessage();
            }
        }

        // Eksik transaction kayıtlarını oluştur
        if ($autoFix) {
            foreach ($checkIssues as $checkIssue) {
                $check = DB::table('checks')->where('id', $checkIssue['check_id'])->first();
                if ($check) {
                    try {
                        $this->createCheckTransaction($check);
                        $result['check_fixes'][] = "Çek #{$check->id} için transaction oluşturuldu";
                    } catch (\Exception $e) {
                        $result['check_fix_errors'][] = "Çek #{$check->id}: " . $e->getMessage();
                    }
                }
            }

            foreach ($noteIssues as $noteIssue) {
                $note = DB::table('promissory_notes')->where('id', $noteIssue['note_id'])->first();
                if ($note) {
                    try {
                        $this->createPromissoryNoteTransaction($note);
                        $result['note_fixes'][] = "Senet #{$note->id} için transaction oluşturuldu";
                    } catch (\Exception $e) {
                        $result['note_fix_errors'][] = "Senet #{$note->id}: " . $e->getMessage();
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Belirli bir müşteriyi detaylı kontrol et
     */
    public function checkCustomerDetails($customerIdOrName, $autoFix = false)
    {
        // Müşteriyi bul
        $customer = is_numeric($customerIdOrName) 
            ? DB::table('customers')->where('id', $customerIdOrName)->first()
            : DB::table('customers')->where('title', 'like', '%' . $customerIdOrName . '%')->first();

        if (!$customer) {
            return [
                'error' => 'Müşteri bulunamadı',
                'customer' => null
            ];
        }

        $calculatedBalance = $this->calculateCustomerBalance($customer->id);
        $difference = $calculatedBalance - $customer->current_balance;

        // Tüm transaction'ları getir
        $transactions = DB::table('customers_account_transactions')
            ->where('customer_id', $customer->id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Expenses
        $expenses = DB::table('expenses')
            ->where('customer_id', $customer->id)
            ->get();

        // Borç ödemeleri
        $debtPayments = DB::table('customer_debt_payments')
            ->where('customer_id', $customer->id)
            ->get();

        // İstatistikler
        $stats = [
            'total_expenses' => $expenses->sum('amount'),
            'total_debt_payments' => $debtPayments->sum('paid_amount'),
            'transaction_count' => $transactions->count(),
            'expense_count' => $expenses->count(),
            'debt_payment_count' => $debtPayments->count()
        ];

        $result = [
            'customer' => $customer,
            'current_balance' => $customer->current_balance,
            'calculated_balance' => $calculatedBalance,
            'difference' => $difference,
            'is_balanced' => abs($difference) <= 0.01,
            'transactions' => $transactions,
            'expenses' => $expenses,
            'debt_payments' => $debtPayments,
            'stats' => $stats
        ];

        // Otomatik düzeltme
        if ($autoFix && abs($difference) > 0.01) {
            try {
                DB::table('customers')
                    ->where('id', $customer->id)
                    ->update(['current_balance' => $calculatedBalance]);
                $result['fixed'] = true;
                $result['fix_message'] = "Bakiye düzeltildi: " . number_format($customer->current_balance, 2) . " → " . number_format($calculatedBalance, 2);
            } catch (\Exception $e) {
                $result['fix_error'] = $e->getMessage();
            }
        }

        return $result;
    }
}

