<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExpenseModuleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $expenseType;
    protected $account;
    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create();
        
        // Create test data
        $this->expenseType = DB::table('expense_types')->insertGetId([
            'name' => 'Test Expense Type',
            'description' => 'Test Description',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->account = DB::table('accounts')->insertGetId([
            'name' => 'Test Account',
            'account_type' => 'bank',
            'balance' => 10000.00,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->employee = DB::table('employees')->insertGetId([
            'name' => 'Test Employee',
            'email' => 'test@employee.com',
            'phone' => '1234567890',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /** @test */
    public function it_can_display_expense_index_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('expenses.index'));

        $response->assertStatus(200);
        $response->assertViewIs('financial.expenses.index');
    }

    /** @test */
    public function it_can_display_expense_create_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('expenses.create'));

        $response->assertStatus(200);
        $response->assertViewIs('financial.expenses.create');
        $response->assertViewHas(['expenseTypes', 'accounts', 'employees', 'expenseId']);
    }

    /** @test */
    public function it_can_store_expense_with_valid_data()
    {
        $expenseData = [
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'employee_id' => $this->employee,
            'description' => 'Test Expense',
            'item_name' => ['Test Item'],
            'unit_price' => [100.00],
            'quantity' => [2],
            'item_description' => ['Test Description']
        ];

        $response = $this->actingAs($this->user)
            ->post(route('expenses.store'), $expenseData);

        $response->assertRedirect(route('expenses.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('expenses', [
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'amount' => 200.00
        ]);

        $this->assertDatabaseHas('expense_items', [
            'item_name' => 'Test Item',
            'unit_price' => 100.00,
            'quantity' => 2,
            'amount' => 200.00
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing_expense()
    {
        $response = $this->actingAs($this->user)
            ->post(route('expenses.store'), []);

        $response->assertSessionHasErrors([
            'expense_type_id',
            'account_id',
            'date',
            'payment_method',
            'item_name',
            'unit_price',
            'quantity'
        ]);
    }

    /** @test */
    public function it_validates_business_rules_when_storing_expense()
    {
        // Test with insufficient account balance
        DB::table('accounts')->where('id', $this->account)->update(['balance' => 50.00]);

        $expenseData = [
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'item_name' => ['Test Item'],
            'unit_price' => [100.00],
            'quantity' => [1]
        ];

        $response = $this->actingAs($this->user)
            ->post(route('expenses.store'), $expenseData);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function it_can_display_expense_show_page()
    {
        $expense = DB::table('expenses')->insertGetId([
            'expense_number' => 'GID-000001',
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'amount' => 100.00,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'status' => 'TAMAMLANDI',
            'created_by' => $this->user->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('expenses.show', $expense));

        $response->assertStatus(200);
        $response->assertViewIs('financial.expenses.show');
        $response->assertViewHas('expense');
    }

    /** @test */
    public function it_can_display_expense_edit_page()
    {
        $expense = DB::table('expenses')->insertGetId([
            'expense_number' => 'GID-000001',
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'amount' => 100.00,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'status' => 'TAMAMLANDI',
            'created_by' => $this->user->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('expenses.edit', $expense));

        $response->assertStatus(200);
        $response->assertViewIs('financial.expenses.edit');
        $response->assertViewHas(['expense', 'expenseTypes', 'accounts', 'employees']);
    }

    /** @test */
    public function it_can_update_expense_with_valid_data()
    {
        $expense = DB::table('expenses')->insertGetId([
            'expense_number' => 'GID-000001',
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'amount' => 100.00,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'status' => 'TAMAMLANDI',
            'created_by' => $this->user->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $updateData = [
            'expense_number' => 'GID-000001',
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'card',
            'description' => 'Updated Expense',
            'item_name' => ['Updated Item'],
            'unit_price' => [150.00],
            'quantity' => [2],
            'item_description' => ['Updated Description']
        ];

        $response = $this->actingAs($this->user)
            ->put(route('expenses.update', $expense), $updateData);

        $response->assertRedirect(route('expenses.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('expenses', [
            'id' => $expense,
            'payment_method' => 'card',
            'amount' => 300.00
        ]);
    }

    /** @test */
    public function it_can_delete_expense()
    {
        $expense = DB::table('expenses')->insertGetId([
            'expense_number' => 'GID-000001',
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'amount' => 100.00,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'status' => 'TAMAMLANDI',
            'created_by' => $this->user->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('expenses.destroy', $expense));

        $response->assertRedirect(route('expenses.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('expenses', ['id' => $expense]);
    }

    /** @test */
    public function it_validates_deletion_rules()
    {
        // Create expense older than 30 days
        $expense = DB::table('expenses')->insertGetId([
            'expense_number' => 'GID-000001',
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'amount' => 100.00,
            'date' => now()->subDays(35)->format('Y-m-d'),
            'payment_method' => 'cash',
            'status' => 'TAMAMLANDI',
            'created_by' => $this->user->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('expenses.destroy', $expense));

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function it_calculates_total_amount_correctly()
    {
        $expenseData = [
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'item_name' => ['Item 1', 'Item 2'],
            'unit_price' => [100.00, 50.00],
            'quantity' => [2, 3],
            'item_description' => ['Desc 1', 'Desc 2']
        ];

        $response = $this->actingAs($this->user)
            ->post(route('expenses.store'), $expenseData);

        $response->assertRedirect(route('expenses.index'));

        // Total should be (100 * 2) + (50 * 3) = 200 + 150 = 350
        $this->assertDatabaseHas('expenses', [
            'amount' => 350.00
        ]);
    }

    /** @test */
    public function it_updates_account_balance_correctly()
    {
        $initialBalance = DB::table('accounts')->where('id', $this->account)->value('balance');
        
        $expenseData = [
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'item_name' => ['Test Item'],
            'unit_price' => [100.00],
            'quantity' => [1]
        ];

        $this->actingAs($this->user)
            ->post(route('expenses.store'), $expenseData);

        $newBalance = DB::table('accounts')->where('id', $this->account)->value('balance');
        
        $this->assertEquals($initialBalance - 100.00, $newBalance);
    }

    /** @test */
    public function it_creates_employee_transaction_when_employee_selected()
    {
        $expenseData = [
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'employee_id' => $this->employee,
            'item_name' => ['Test Item'],
            'unit_price' => [100.00],
            'quantity' => [1]
        ];

        $this->actingAs($this->user)
            ->post(route('expenses.store'), $expenseData);

        $this->assertDatabaseHas('employee_transactions', [
            'employee_id' => $this->employee,
            'type' => 'expense',
            'amount' => 100.00
        ]);
    }

    /** @test */
    public function it_generates_unique_expense_numbers()
    {
        $expenseData = [
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'item_name' => ['Test Item'],
            'unit_price' => [100.00],
            'quantity' => [1]
        ];

        // Create first expense
        $this->actingAs($this->user)
            ->post(route('expenses.store'), $expenseData);

        // Create second expense
        $this->actingAs($this->user)
            ->post(route('expenses.store'), $expenseData);

        $expenses = DB::table('expenses')->get();
        
        $this->assertCount(2, $expenses);
        $this->assertNotEquals($expenses[0]->expense_number, $expenses[1]->expense_number);
    }

    /** @test */
    public function it_handles_file_upload_correctly()
    {
        $expenseData = [
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'item_name' => ['Test Item'],
            'unit_price' => [100.00],
            'quantity' => [1],
            'invoice_photo' => \Illuminate\Http\UploadedFile::fake()->image('invoice.jpg')
        ];

        $response = $this->actingAs($this->user)
            ->post(route('expenses.store'), $expenseData);

        $response->assertRedirect(route('expenses.index'));

        $expense = DB::table('expenses')->first();
        $this->assertNotNull($expense->invoice_photo);
    }

    /** @test */
    public function it_validates_file_upload_constraints()
    {
        $expenseData = [
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'item_name' => ['Test Item'],
            'unit_price' => [100.00],
            'quantity' => [1],
            'invoice_photo' => \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 3000) // Too large
        ];

        $response = $this->actingAs($this->user)
            ->post(route('expenses.store'), $expenseData);

        $response->assertSessionHasErrors(['invoice_photo']);
    }

    /** @test */
    public function it_can_get_expense_data_for_ajax()
    {
        $expense = DB::table('expenses')->insertGetId([
            'expense_number' => 'GID-000001',
            'expense_type_id' => $this->expenseType,
            'account_id' => $this->account,
            'amount' => 100.00,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'status' => 'TAMAMLANDI',
            'created_by' => $this->user->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('expenses.get', $expense));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'expense_number',
            'expense_type_id',
            'account_id',
            'amount',
            'date',
            'payment_method'
        ]);
    }
}