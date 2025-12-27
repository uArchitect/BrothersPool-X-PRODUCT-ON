<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestFinancialModules extends Command
{
    protected $signature = 'test:financial {module=all}';
    protected $description = 'Test financial modules (incomes, checks, promissory_notes)';

    public function handle()
    {
        $module = $this->argument('module');
        
        $this->info('🧪 Starting Financial Modules Test...');
        $this->newLine();

        try {
            // Test Income Module
            if ($module === 'all' || $module === 'income') {
                $this->testIncomeModule();
            }

            // Test Check Module
            if ($module === 'all' || $module === 'check') {
                $this->testCheckModule();
            }

            // Test Promissory Note Module
            if ($module === 'all' || $module === 'promissory') {
                $this->testPromissoryNoteModule();
            }

            $this->newLine();
            $this->info('✅ All tests passed!');
            
        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            Log::error('[Test Failed]', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return 1;
        }

        return 0;
    }

    private function testIncomeModule()
    {
        $this->info('📊 Testing Income Module...');
        
        try {
            // Test 1: Check if income_categories exist
            $categoriesCount = DB::table('income_categories')->count();
            $this->line("  ✓ Income categories exist: {$categoriesCount}");
            
            if ($categoriesCount === 0) {
                throw new \Exception('No income categories found');
            }

            // Test 2: Check if accounts exist
            $accountsCount = DB::table('accounts')->count();
            $this->line("  ✓ Accounts exist: {$accountsCount}");
            
            if ($accountsCount === 0) {
                throw new \Exception('No accounts found');
            }

            // Test 3: Check if customers exist
            $customersCount = DB::table('customers')->count();
            $this->line("  ✓ Customers exist: {$customersCount}");
            
            if ($customersCount === 0) {
                throw new \Exception('No customers found');
            }

            // Test 4: Check status column type
            $statusColumn = DB::select("SHOW COLUMNS FROM incomes WHERE Field = 'status'");
            $type = $statusColumn[0]->Type;
            $this->line("  ✓ Income status column type: {$type}");
            
            if (str_contains($type, 'enum')) {
                throw new \Exception('Income status column is still ENUM, should be VARCHAR');
            }

            // Test 5: Test INSERT operation
            $this->line("  ↳ Testing INSERT operation...");
            $accountId = DB::table('accounts')->first()->id;
            $customerId = DB::table('customers')->first()->id;
            $testIncomeId = DB::table('incomes')->insertGetId([
                'income_number' => 'TEST-' . time(),
                'income_category_id' => 1,
                'account_id' => $accountId,
                'customer_id' => $customerId,
                'amount' => 100.00,
                'date' => now()->toDateString(),
                'description' => 'Test income',
                'payment_method' => 'cash',
                'status' => 'TAMAMLANDI',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->line("    ✓ INSERT successful: ID {$testIncomeId}");

            // Test 6: Test UPDATE operation
            $this->line("  ↳ Testing UPDATE operation...");
            DB::table('incomes')->where('id', $testIncomeId)->update([
                'amount' => 200.00,
                'updated_at' => now()
            ]);
            $updated = DB::table('incomes')->where('id', $testIncomeId)->first();
            if ($updated->amount != 200.00) {
                throw new \Exception('UPDATE failed: amount not updated');
            }
            $this->line("    ✓ UPDATE successful");

            // Test 7: Test SELECT operation
            $this->line("  ↳ Testing SELECT operation...");
            $income = DB::table('incomes')->where('id', $testIncomeId)->first();
            if (!$income) {
                throw new \Exception('SELECT failed: income not found');
            }
            $this->line("    ✓ SELECT successful");

            // Test 8: Test DELETE operation
            $this->line("  ↳ Testing DELETE operation...");
            DB::table('incomes')->where('id', $testIncomeId)->delete();
            $deleted = DB::table('incomes')->where('id', $testIncomeId)->first();
            if ($deleted) {
                throw new \Exception('DELETE failed: income still exists');
            }
            $this->line("    ✓ DELETE successful");

            // Test 9: Test customer account transactions
            $this->line("  ↳ Testing customer account transactions...");
            $catCount = DB::table('customers_account_transactions')->count();
            $this->line("    ✓ Customer transactions: {$catCount}");

            $this->info('  ✅ Income Module OK');
            
        } catch (\Exception $e) {
            $this->error('  ❌ Income Module Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function testCheckModule()
    {
        $this->info('🔴 Testing Check Module...');
        
        try {
            // Test 1: Check if checks table exists
            $checksCount = DB::table('checks')->count();
            $this->line("  ✓ Checks in database: {$checksCount}");

            // Test 2: Check status column type
            $statusColumn = DB::select("SHOW COLUMNS FROM checks WHERE Field = 'status'");
            $type = $statusColumn[0]->Type;
            $this->line("  ✓ Check status column type: {$type}");
            
            if (str_contains($type, 'enum')) {
                throw new \Exception('Check status column is still ENUM, should be VARCHAR');
            }

            // Test 3: Check transaction_type column type
            $typeColumn = DB::select("SHOW COLUMNS FROM check_transactions WHERE Field = 'transaction_type'");
            $transactionType = $typeColumn[0]->Type;
            $this->line("  ✓ Check transaction_type column type: {$transactionType}");
            
            if (str_contains($transactionType, 'enum')) {
                throw new \Exception('Check transaction_type column is still ENUM, should be VARCHAR');
            }

            // Test 4: Test INSERT operation
            $this->line("  ↳ Testing INSERT operation...");
            $customerId = DB::table('customers')->first()->id;
            $testCheckId = DB::table('checks')->insertGetId([
                'check_number' => 'TEST-CHK-' . time(),
                'customer_id' => $customerId,
                'bank_name' => 'Test Bank',
                'bank_branch' => 'Test Branch',
                'account_number' => 'TEST-123',
                'amount' => 1000.00,
                'issue_date' => now()->toDateString(),
                'maturity_date' => now()->addDays(30)->toDateString(),
                'status' => 'BEKLEMEDE',
                'description' => 'Test check',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->line("    ✓ INSERT successful: ID {$testCheckId}");

            // Test 5: Test UPDATE operation
            $this->line("  ↳ Testing UPDATE operation...");
            DB::table('checks')->where('id', $testCheckId)->update([
                'status' => 'TAHSIL_EDILDI',
                'updated_at' => now()
            ]);
            $updated = DB::table('checks')->where('id', $testCheckId)->first();
            if ($updated->status !== 'TAHSIL_EDILDI') {
                throw new \Exception('UPDATE failed: status not updated');
            }
            $this->line("    ✓ UPDATE successful");

            // Test 6: Test DELETE operation
            $this->line("  ↳ Testing DELETE operation...");
            DB::table('checks')->where('id', $testCheckId)->delete();
            $deleted = DB::table('checks')->where('id', $testCheckId)->first();
            if ($deleted) {
                throw new \Exception('DELETE failed: check still exists');
            }
            $this->line("    ✓ DELETE successful");

            $this->info('  ✅ Check Module OK');
            
        } catch (\Exception $e) {
            $this->error('  ❌ Check Module Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function testPromissoryNoteModule()
    {
        $this->info('📝 Testing Promissory Note Module...');
        
        try {
            // Test 1: Check if promissory_notes table exists
            $notesCount = DB::table('promissory_notes')->count();
            $this->line("  ✓ Promissory notes in database: {$notesCount}");

            // Test 2: Check status column type
            $statusColumn = DB::select("SHOW COLUMNS FROM promissory_notes WHERE Field = 'status'");
            $type = $statusColumn[0]->Type;
            $this->line("  ✓ Promissory note status column type: {$type}");
            
            if (str_contains($type, 'enum')) {
                throw new \Exception('Promissory note status column is still ENUM, should be VARCHAR');
            }

            // Test 3: Check transaction_type column type
            $typeColumn = DB::select("SHOW COLUMNS FROM promissory_note_transactions WHERE Field = 'transaction_type'");
            $transactionType = $typeColumn[0]->Type;
            $this->line("  ✓ Promissory note transaction_type column type: {$transactionType}");
            
            if (str_contains($transactionType, 'enum')) {
                throw new \Exception('Promissory note transaction_type column is still ENUM, should be VARCHAR');
            }

            // Test 4: Test INSERT operation
            $this->line("  ↳ Testing INSERT operation...");
            $customerId = DB::table('customers')->first()->id;
            $testNoteId = DB::table('promissory_notes')->insertGetId([
                'note_number' => 'TEST-NOTE-' . time(),
                'customer_id' => $customerId,
                'amount' => 5000.00,
                'issue_date' => now()->toDateString(),
                'maturity_date' => now()->addDays(90)->toDateString(),
                'status' => 'AKTIF',
                'description' => 'Test promissory note',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->line("    ✓ INSERT successful: ID {$testNoteId}");

            // Test 5: Test UPDATE operation
            $this->line("  ↳ Testing UPDATE operation...");
            DB::table('promissory_notes')->where('id', $testNoteId)->update([
                'status' => 'ODENDI',
                'updated_at' => now()
            ]);
            $updated = DB::table('promissory_notes')->where('id', $testNoteId)->first();
            if ($updated->status !== 'ODENDI') {
                throw new \Exception('UPDATE failed: status not updated');
            }
            $this->line("    ✓ UPDATE successful");

            // Test 6: Test DELETE operation
            $this->line("  ↳ Testing DELETE operation...");
            DB::table('promissory_notes')->where('id', $testNoteId)->delete();
            $deleted = DB::table('promissory_notes')->where('id', $testNoteId)->first();
            if ($deleted) {
                throw new \Exception('DELETE failed: promissory note still exists');
            }
            $this->line("    ✓ DELETE successful");

            $this->info('  ✅ Promissory Note Module OK');
            
        } catch (\Exception $e) {
            $this->error('  ❌ Promissory Note Module Error: ' . $e->getMessage());
            throw $e;
        }
    }
}

