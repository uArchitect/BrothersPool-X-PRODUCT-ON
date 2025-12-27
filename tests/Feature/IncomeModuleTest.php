<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class IncomeModuleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $incomeCategory;
    protected $account;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create();
        
        // Create test data
        $this->incomeCategory = DB::table('income_categories')->insertGetId([
            'name' => 'Test Income Category',
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

        $this->customer = DB::table('customers')->insertGetId([
            'title' => 'Test Customer',
            'code' => 'CUST001',
            'current_balance' => 0.00,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /** @test */
    public function it_can_display_income_index_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('incomes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('financial.incomes.index');
    }

    /** @test */
    public function it_can_display_income_create_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('incomes.create'));

        $response->assertStatus(200);
        $response->assertViewIs('financial.incomes.create');
        $response->assertViewHas(['incomeCategories', 'accounts', 'customers', 'incomeId']);
    }

    /** @test */
    public function it_can_store_income_with_valid_data()
    {
        $incomeData = [
            'income_category_id' => $this->incomeCategory,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'customer_id' => $this->customer,
            'description' => 'Test Income',
            'item_name' => ['Test Item'],
            'unit_price' => [100.00],
            'quantity' => [2],
            'item_description' => ['Test Description']
        ];

        $response = $this->actingAs($this->user)
            ->post(route('incomes.store'), $incomeData);

        $response->assertRedirect(route('incomes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('incomes', [
            'income_category_id' => $this->incomeCategory,
            'account_id' => $this->account,
            'amount' => 200.00
        ]);

        $this->assertDatabaseHas('income_items', [
            'item_name' => 'Test Item',
            'unit_price' => 100.00,
            'quantity' => 2,
            'amount' => 200.00
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing_income()
    {
        $response = $this->actingAs($this->user)
            ->post(route('incomes.store'), []);

        $response->assertSessionHasErrors([
            'income_category_id',
            'account_id',
            'date',
            'payment_method',
            'item_name',
            'unit_price',
            'quantity'
        ]);
    }

    /** @test */
    public function it_validates_business_rules_when_storing_income()
    {
        // Test with inactive account
        DB::table('accounts')->where('id', $this->account)->update(['is_active' => false]);

        $incomeData = [
            'income_category_id' => $this->incomeCategory,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'item_name' => ['Test Item'],
            'unit_price' => [100.00],
            'quantity' => [1]
        ];

        $response = $this->actingAs($this->user)
            ->post(route('incomes.store'), $incomeData);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function it_can_display_income_show_page()
    {
        $income = DB::table('incomes')->insertGetId([
            'income_number' => 'GEL-000001',
            'income_category_id' => $this->incomeCategory,
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
            ->get(route('incomes.show', $income));

        $response->assertStatus(200);
        $response->assertViewIs('financial.incomes.show');
        $response->assertViewHas('income');
    }

    /** @test */
    public function it_can_display_income_edit_page()
    {
        $income = DB::table('incomes')->insertGetId([
            'income_number' => 'GEL-000001',
            'income_category_id' => $this->incomeCategory,
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
            ->get(route('incomes.edit', $income));

        $response->assertStatus(200);
        $response->assertViewIs('financial.incomes.edit');
        $response->assertViewHas(['income', 'incomeCategories', 'accounts', 'customers']);
    }

    /** @test */
    public function it_can_update_income_with_valid_data()
    {
        $income = DB::table('incomes')->insertGetId([
            'income_number' => 'GEL-000001',
            'income_category_id' => $this->incomeCategory,
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
            'income_number' => 'GEL-000001',
            'income_category_id' => $this->incomeCategory,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'card',
            'description' => 'Updated Income',
            'item_name' => ['Updated Item'],
            'unit_price' => [150.00],
            'quantity' => [2],
            'item_description' => ['Updated Description']
        ];

        $response = $this->actingAs($this->user)
            ->put(route('incomes.update', $income), $updateData);

        $response->assertRedirect(route('incomes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('incomes', [
            'id' => $income,
            'payment_method' => 'card',
            'amount' => 300.00
        ]);
    }

    /** @test */
    public function it_can_delete_income()
    {
        $income = DB::table('incomes')->insertGetId([
            'income_number' => 'GEL-000001',
            'income_category_id' => $this->incomeCategory,
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
            ->delete(route('incomes.destroy', $income));

        $response->assertRedirect(route('incomes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('incomes', ['id' => $income]);
    }

    /** @test */
    public function it_validates_deletion_rules()
    {
        // Create income older than 30 days
        $income = DB::table('incomes')->insertGetId([
            'income_number' => 'GEL-000001',
            'income_category_id' => $this->incomeCategory,
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
            ->delete(route('incomes.destroy', $income));

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function it_calculates_total_amount_correctly()
    {
        $incomeData = [
            'income_category_id' => $this->incomeCategory,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'item_name' => ['Item 1', 'Item 2'],
            'unit_price' => [100.00, 50.00],
            'quantity' => [2, 3],
            'item_description' => ['Desc 1', 'Desc 2']
        ];

        $response = $this->actingAs($this->user)
            ->post(route('incomes.store'), $incomeData);

        $response->assertRedirect(route('incomes.index'));

        // Total should be (100 * 2) + (50 * 3) = 200 + 150 = 350
        $this->assertDatabaseHas('incomes', [
            'amount' => 350.00
        ]);
    }

    /** @test */
    public function it_updates_account_balance_correctly()
    {
        $initialBalance = DB::table('accounts')->where('id', $this->account)->value('balance');
        
        $incomeData = [
            'income_category_id' => $this->incomeCategory,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'item_name' => ['Test Item'],
            'unit_price' => [100.00],
            'quantity' => [1]
        ];

        $this->actingAs($this->user)
            ->post(route('incomes.store'), $incomeData);

        $newBalance = DB::table('accounts')->where('id', $this->account)->value('balance');
        
        $this->assertEquals($initialBalance + 100.00, $newBalance);
    }

    /** @test */
    public function it_creates_customer_transaction_when_customer_selected()
    {
        $incomeData = [
            'income_category_id' => $this->incomeCategory,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'customer_id' => $this->customer,
            'item_name' => ['Test Item'],
            'unit_price' => [100.00],
            'quantity' => [1]
        ];

        $this->actingAs($this->user)
            ->post(route('incomes.store'), $incomeData);

        $this->assertDatabaseHas('customers_account_transactions', [
            'customer_id' => $this->customer,
            'type' => 'Gelir',
            'amount' => 100.00
        ]);
    }

    /** @test */
    public function it_generates_unique_income_numbers()
    {
        $incomeData = [
            'income_category_id' => $this->incomeCategory,
            'account_id' => $this->account,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'item_name' => ['Test Item'],
            'unit_price' => [100.00],
            'quantity' => [1]
        ];

        // Create first income
        $this->actingAs($this->user)
            ->post(route('incomes.store'), $incomeData);

        // Create second income
        $this->actingAs($this->user)
            ->post(route('incomes.store'), $incomeData);

        $incomes = DB::table('incomes')->get();
        
        $this->assertCount(2, $incomes);
        $this->assertNotEquals($incomes[0]->income_number, $incomes[1]->income_number);
    }
}