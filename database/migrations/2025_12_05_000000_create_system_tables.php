<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set default charset and collation for Turkish character support
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;');
        DB::statement('SET CHARACTER SET utf8mb4;');
        
        // 1. Users table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable()->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 2. Employees table (with all fields from db.sql)
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('tc_no', 11)->nullable();
                $table->string('sgk_no', 20)->nullable();
                $table->unsignedBigInteger('group_id')->nullable();
                $table->unsignedBigInteger('position_id')->nullable();
                $table->string('iban', 255)->nullable();
                $table->string('bank_name')->nullable();
                $table->string('position')->nullable();
                $table->decimal('salary', 10, 2)->nullable();
                $table->date('hire_date')->nullable();
                $table->string('avatar')->nullable();
                $table->string('role')->nullable();
                $table->decimal('hourly_wage', 10, 2)->nullable();
                $table->decimal('monthly_salary', 10, 2)->nullable();
                $table->enum('payment_frequency', ['daily', 'weekly', 'monthly', 'hourly'])->default('monthly');
                $table->decimal('daily_wage', 15, 2)->nullable();
                $table->decimal('weekly_wage', 15, 2)->nullable();
                $table->integer('working_days_per_month')->default(30);
                $table->text('address')->nullable();
                $table->integer('experience_years')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. Employee Groups table
        if (!Schema::hasTable('employee_groups')) {
            Schema::create('employee_groups', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 4. Employee Positions table
        if (!Schema::hasTable('employee_positions')) {
            Schema::create('employee_positions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('group_id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->timestamps();
                
                $table->unique(['group_id', 'name']);
            });
        }

        // 5. Employee Daily Wages table
        if (!Schema::hasTable('employee_daily_wages')) {
            Schema::create('employee_daily_wages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('staff_shift_id')->nullable();
                $table->unsignedBigInteger('payroll_id')->nullable();
                $table->date('calculation_date');
                $table->date('work_date');
                $table->decimal('daily_wage', 15, 2);
                $table->decimal('worked_hours', 5, 2)->nullable();
                $table->boolean('is_holiday')->default(false);
                $table->boolean('is_overtime')->default(false);
                $table->decimal('multiplier', 4, 2)->default(1.0);
                $table->decimal('calculated_amount', 15, 2);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 6. Payrolls table
        if (!Schema::hasTable('payrolls')) {
            Schema::create('payrolls', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('employee_id');
                $table->string('payroll_period', 20);
                $table->enum('period_type', ['daily', 'weekly', 'monthly', 'hourly']);
                $table->date('period_start_date');
                $table->date('period_end_date');
                $table->integer('working_days')->default(0);
                $table->decimal('daily_wage', 15, 2)->nullable();
                $table->decimal('calculated_amount', 15, 2)->default(0);
                $table->decimal('gross_salary', 15, 2)->default(0);
                $table->decimal('deductions', 15, 2)->default(0);
                $table->decimal('net_salary', 15, 2)->default(0);
                $table->decimal('total_paid', 15, 2)->default(0);
                $table->decimal('remaining_amount', 15, 2)->default(0);
                $table->enum('status', ['pending', 'partial', 'paid', 'cancelled'])->default('pending');
                $table->text('notes')->nullable();
                $table->decimal('base_salary', 15, 2)->nullable();
                $table->date('base_salary_date')->nullable();
                $table->timestamps();
                
                $table->unique(['employee_id', 'period_type', 'period_start_date'], 'unique_employee_period');
            });
        }

        // 7. Payroll Deductions table
        if (!Schema::hasTable('payroll_deductions')) {
            Schema::create('payroll_deductions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('payroll_id');
                $table->enum('deduction_type', ['sgk_employee', 'sgk_employer', 'tax', 'stamp_duty', 'other']);
                $table->decimal('amount', 15, 2);
                $table->decimal('rate', 5, 2)->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 8. Payroll Payments table
        if (!Schema::hasTable('payroll_payments')) {
            Schema::create('payroll_payments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('payroll_id');
                $table->date('payment_date');
                $table->decimal('amount', 15, 2);
                $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'other']);
                $table->string('bank_name')->nullable();
                $table->string('account_number', 50)->nullable();
                $table->string('reference_number', 100)->nullable();
                $table->text('description')->nullable();
                $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
                $table->timestamps();
            });
        }

        // 9. Customers table
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('code')->nullable();
                $table->string('title')->nullable();
                $table->string('account_type')->nullable();
                $table->string('tax_office')->nullable();
                $table->string('tax_number')->nullable();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('name')->nullable();
                $table->string('phone')->nullable()->unique();
                $table->string('email')->nullable()->unique();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('postal_code')->nullable();
                $table->date('birth_date')->nullable();
                $table->enum('gender', ['male', 'female', 'other'])->nullable();
                $table->json('preferences')->nullable();
                $table->integer('loyalty_points')->default(0);
                $table->decimal('total_spent', 10, 2)->default(0.00);
                $table->integer('order_count')->default(0);
                $table->boolean('is_verified')->default(false);
                $table->boolean('is_blocked')->default(false);
                $table->boolean('is_vip')->default(false);
                $table->boolean('allergy')->default(false);
                $table->timestamp('last_order_at')->nullable();
                $table->string('authorized_person')->nullable();
                $table->decimal('credit_limit', 15, 2)->nullable()->default(0);
                $table->text('current_balance')->nullable();
                $table->timestamps();

                $table->index(['phone', 'email']);
                $table->index('loyalty_points');
                $table->index('code');
                $table->index('account_type');
                $table->index('tax_number');
            });
        }

        // 10. Categories table
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('slug')->nullable();
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->integer('stock_type')->default(0);
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->integer('level')->default(0);
                $table->string('path')->nullable();
                $table->timestamps();

                $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
                $table->index(['parent_id', 'is_active']);
                $table->index(['level', 'sort_order']);
                $table->index('path');
            });
        }

        // 11. Menu Items table
        if (!Schema::hasTable('menu_items')) {
            Schema::create('menu_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('code')->nullable();
                $table->text('description')->nullable();
                $table->text('ingredients')->nullable();
                $table->decimal('price', 10, 2);
                $table->string('image')->nullable();
                $table->integer('prep_time')->default(15);
                $table->boolean('is_available')->default(true);
                $table->text('allergens')->nullable();
                $table->json('nutrition_info')->nullable();
                $table->boolean('is_stock')->default(false);
                $table->integer('stock_quantity')->default(0);
                $table->integer('min_stock_level')->default(0);
                $table->unsignedBigInteger('category_id');
                $table->timestamps();

                $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
                $table->index(['is_available', 'category_id']);
            });
        }

        // 12. Menu Item Variants table
        if (!Schema::hasTable('menu_item_variants')) {
            Schema::create('menu_item_variants', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('menu_item_id');
                $table->string('name');
                $table->decimal('price_adjustment', 10, 2)->default(0.00);
                $table->boolean('is_default')->default(false);
                $table->boolean('is_available')->default(true);
                $table->timestamps();

                $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            });
        }

        // 13. Menu Item Addons table
        if (!Schema::hasTable('menu_item_addons')) {
            Schema::create('menu_item_addons', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('menu_item_id');
                $table->string('name');
                $table->decimal('price', 10, 2);
                $table->boolean('is_required')->default(false);
                $table->boolean('is_available')->default(true);
                $table->timestamps();

                $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            });
        }

        // 14. Tables table
        if (!Schema::hasTable('tables')) {
            Schema::create('tables', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('table_number')->unique();
                $table->string('table_type')->default('dine_in');
                $table->boolean('is_reservable')->default(true);
                $table->boolean('is_smoking_allowed')->default(false);
                $table->json('features')->nullable();
                $table->decimal('location_x', 10, 2)->nullable();
                $table->decimal('location_y', 10, 2)->nullable();
                $table->integer('capacity');
                $table->string('location')->nullable();
                $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance'])->default('available');
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
                $table->index(['status', 'is_active']);
                $table->index(['table_type', 'is_reservable']);
            });
        }

        // 15. Laravel system tables
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        }

        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration');
            });
        }

        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->string('email')->index();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        // 16. Settings table
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('restaurant_name')->nullable();
                $table->string('salon_name')->nullable();
                $table->string('restaurant_type')->default('restaurant');
                $table->text('phone_number')->nullable();
                $table->text('email')->nullable();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('country')->default('Turkey');
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->string('business_license')->nullable();
                $table->string('tax_office')->nullable();
                $table->string('tax_number')->nullable();
                $table->string('vat_number')->nullable();
                $table->string('company_logo')->nullable();
                $table->string('restaurant_logo')->nullable();
                $table->string('favicon')->nullable();
                $table->time('opening_time')->default('09:00');
                $table->time('closing_time')->default('22:00');
                $table->json('weekly_schedule')->nullable();
                $table->json('holiday_schedule')->nullable();
                $table->boolean('is_24_hours')->default(false);
                $table->string('currency', 3)->default('TRY');
                $table->string('currency_symbol', 5)->default('₺');
                $table->decimal('default_tax_rate', 5, 2)->default(18.00);
                $table->boolean('tax_inclusive_pricing')->default(true);
                $table->boolean('service_charge_enabled')->default(false);
                $table->decimal('service_charge_rate', 5, 2)->default(10.00);
                $table->boolean('delivery_enabled')->default(false);
                $table->boolean('takeaway_enabled')->default(true);
                $table->boolean('dine_in_enabled')->default(true);
                $table->boolean('reservation_enabled')->default(true);
                $table->boolean('online_ordering_enabled')->default(false);
                $table->integer('max_table_capacity')->default(4);
                $table->integer('reservation_advance_days')->default(30);
                $table->decimal('staff_commission_rate', 5, 2)->default(5.00);
                $table->boolean('auto_assign_waiter')->default(false);
                $table->integer('max_orders_per_waiter')->default(10);
                $table->timestamps();
            });
        }

        // 17. Order system tables
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('order_number')->unique();
                $table->enum('order_source', ['dine_in', 'takeaway', 'delivery', 'online', 'phone', 'walk_in', 'reservation'])->default('dine_in');
                $table->unsignedBigInteger('table_id')->nullable();
                $table->unsignedBigInteger('waiter_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->string('customer_name')->nullable();
                $table->string('customer_phone')->nullable();
                $table->string('customer_email')->nullable();
                $table->string('customer_address')->nullable();
                $table->string('customer_city')->nullable();
                $table->string('customer_postal_code')->nullable();
                $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready', 'served', 'cancelled'])->default('pending');
                $table->timestamp('order_time')->useCurrent();
                $table->timestamp('served_at')->nullable();
                $table->decimal('subtotal', 10, 2)->default(0.00);
                $table->decimal('tax_amount', 10, 2)->default(0.00);
                $table->decimal('service_charge', 10, 2)->default(0.00);
                $table->decimal('delivery_fee', 10, 2)->default(0.00);
                $table->decimal('discount_amount', 10, 2)->default(0.00);
                $table->decimal('total_amount', 10, 2)->default(0.00);
                $table->timestamp('delivery_time')->nullable();
                $table->text('delivery_notes')->nullable();
                $table->enum('payment_method', ['cash', 'card', 'online', 'wallet', 'points'])->nullable();
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
                $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
                $table->boolean('is_urgent')->default(false);
                $table->boolean('is_group_order')->default(false);
                $table->string('tracking_number')->nullable();
                $table->json('status_history')->nullable();
                $table->string('external_order_id')->nullable();
                $table->string('external_platform')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('table_id')->references('id')->on('tables')->onDelete('set null');
                $table->foreign('waiter_id')->references('id')->on('employees')->onDelete('set null');
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('menu_item_id');
                $table->integer('quantity');
                $table->decimal('unit_price', 10, 2);
                $table->decimal('total_price', 10, 2);
                $table->text('special_instructions')->nullable();
                $table->enum('status', ['pending', 'preparing', 'ready', 'served'])->default('pending');
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            });
        }

        // 18. Sales tables
        if (!Schema::hasTable('sales')) {
            Schema::create('sales', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('sale_number')->unique();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->unsignedBigInteger('seller_id')->nullable();
                $table->decimal('subtotal', 10, 2)->default(0.00);
                $table->decimal('tax_amount', 10, 2)->default(0.00);
                $table->decimal('discount_amount', 10, 2)->default(0.00);
                $table->decimal('total', 10, 2)->default(0.00);
                $table->string('date')->nullable();
                $table->decimal('total_discount', 10, 2)->default(0);
                $table->decimal('total_tax', 10, 2)->default(0);
                $table->decimal('grand_total', 10, 2)->default(0);
                $table->decimal('paid', 10, 2)->default(0);
                $table->enum('payment_method', ['cash', 'card', 'online', 'wallet', 'points'])->nullable();
                $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
                $table->foreign('seller_id')->references('id')->on('employees')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('sale_items')) {
            Schema::create('sale_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('sale_id');
                $table->unsignedBigInteger('menu_item_id');
                $table->integer('quantity');
                $table->decimal('unit_price', 10, 2);
                $table->decimal('total_price', 10, 2);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
                $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            });
        }

        // 19. Inventory & Stock tables
        if (!Schema::hasTable('payment_methods')) {
            Schema::create('payment_methods', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('type');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('units')) {
            Schema::create('units', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('short_name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tax_rates')) {
            Schema::create('tax_rates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->decimal('rate', 5, 2);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('warehouses')) {
            Schema::create('warehouses', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('code')->nullable();
                $table->text('address')->nullable();
                $table->unsignedBigInteger('manager')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('manager')->references('id')->on('employees')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('menu_item_id');
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->enum('movement_type', ['in', 'out', 'transfer', 'adjustment']);
                $table->integer('quantity');
                $table->decimal('unit_cost', 10, 2)->nullable();
                $table->decimal('total_cost', 10, 2)->nullable();
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
                $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->index(['menu_item_id', 'movement_type']);
                $table->index(['reference_type', 'reference_id'], 'sm_ref_type_ref_idx');
            });
        }

        // 20. Expense & Financial tables
        if (!Schema::hasTable('expense_types')) {
            Schema::create('expense_types', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('expense_categories')) {
            Schema::create('expense_categories', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('accounts')) {
            Schema::create('accounts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('type');
                $table->text('description')->nullable();
                $table->decimal('balance', 10, 2)->default(0.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('expense_number')->unique();
                $table->unsignedBigInteger('expense_type_id');
                $table->unsignedBigInteger('expense_category_id')->nullable();
                $table->unsignedBigInteger('account_id')->nullable();
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->string('title');
                $table->text('description')->nullable();
                $table->decimal('amount', 10, 2);
                $table->decimal('total', 10, 2);
                $table->date('date');
                $table->string('receipt_image')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('expense_type_id')->references('id')->on('expense_types')->onDelete('cascade');
                $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('set null');
                $table->foreign('account_id')->references('id')->on('accounts')->onDelete('set null');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
                $table->index(['date', 'expense_type_id']);
            });
        }

        if (!Schema::hasTable('expense_items')) {
            Schema::create('expense_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('expense_id');
                $table->unsignedBigInteger('expense_category_id');
                $table->string('item_name');
                $table->text('description')->nullable();
                $table->decimal('amount', 10, 2);
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 10, 2)->nullable();
                $table->timestamps();

                $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
                $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('transaction_number')->unique();
                $table->enum('type', ['income', 'expense', 'transfer']);
                $table->unsignedBigInteger('account_id');
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('reference_type')->nullable();
                $table->decimal('amount', 10, 2);
                $table->text('description')->nullable();
                $table->date('date');
                $table->timestamps();

                $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
                $table->index(['type', 'date']);
                $table->index(['reference_type', 'reference_id'], 'trans_ref_type_ref_idx');
            });
        }

        // 21. Income management tables
        if (!Schema::hasTable('income_categories')) {
            Schema::create('income_categories', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('incomes')) {
            Schema::create('incomes', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('income_number')->unique();
                $table->unsignedBigInteger('income_category_id');
                $table->unsignedBigInteger('account_id');
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->decimal('amount', 15, 2);
                $table->date('date');
                $table->text('description')->nullable();
                $table->string('payment_method')->default('cash');
                $table->string('reference_number')->nullable();
                $table->string('status')->default('TAMAMLANDI');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('income_category_id')->references('id')->on('income_categories')->onDelete('cascade');
                $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->index(['date', 'status']);
                $table->index(['income_category_id', 'date']);
                $table->index(['account_id', 'date']);
                $table->index(['customer_id', 'date']);
                $table->index('income_number');
                $table->index('status');
            });
        }

        if (!Schema::hasTable('income_items')) {
            Schema::create('income_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('income_id');
                $table->string('item_name');
                $table->text('description')->nullable();
                $table->decimal('unit_price', 15, 2);
                $table->decimal('quantity', 10, 2)->default(1);
                $table->decimal('amount', 15, 2);
                $table->timestamps();

                $table->foreign('income_id')->references('id')->on('incomes')->onDelete('cascade');
                $table->index('income_id');
            });
        }

        // 22. Income types table
        if (!Schema::hasTable('income_types')) {
            Schema::create('income_types', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['is_active']);
                $table->index(['sort_order']);
            });
        }

        // 23. Customer account transactions table
        if (!Schema::hasTable('customers_account_transactions')) {
            Schema::create('customers_account_transactions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('customer_id');
                $table->date('date');
                $table->string('account');
                $table->string('type');
                $table->decimal('amount', 15, 2);
                $table->text('description')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('transaction_type')->nullable();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
                $table->index(['customer_id', 'date']);
                $table->index(['customer_id', 'type']);
                $table->index(['transaction_type', 'reference_id'], 'cat_trans_type_ref_idx');
                $table->index('date');
                $table->index('type');
            });
        }

        // 24. Checks table
        if (!Schema::hasTable('checks')) {
            Schema::create('checks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->enum('type', ['verilen', 'alınan'])->default('alınan');
                $table->unsignedBigInteger('account_id')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('branch_name')->nullable();
                $table->string('check_number');
                $table->decimal('amount', 15, 2);
                $table->date('issue_date');
                $table->date('maturity_date');
                $table->enum('status', ['PENDING', 'CLEARED', 'BOUNCED', 'CANCELLED'])->default('PENDING');
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['customer_id', 'status']);
                $table->index(['maturity_date']);
                $table->index('status');
            });
        }

        // 25. Promissory Notes table
        if (!Schema::hasTable('promissory_notes')) {
            Schema::create('promissory_notes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->enum('type', ['verilen', 'alınan'])->default('alınan');
                $table->unsignedBigInteger('account_id')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('branch_name')->nullable();
                $table->string('note_number');
                $table->decimal('amount', 15, 2);
                $table->date('issue_date');
                $table->date('maturity_date');
                $table->enum('status', ['ACTIVE', 'PAID', 'OVERDUE', 'CANCELLED'])->default('ACTIVE');
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['customer_id', 'status']);
                $table->index(['maturity_date']);
                $table->index('status');
            });
        }

        // 26. Reservation tables
        if (!Schema::hasTable('reservations')) {
            Schema::create('reservations', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('reservation_number')->unique();
                $table->unsignedBigInteger('table_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->string('customer_name')->nullable();
                $table->string('customer_phone')->nullable();
                $table->string('customer_email')->nullable();
                $table->date('start_date');
                $table->time('start_time');
                $table->time('end_time')->nullable();
                $table->integer('party_size')->default(1);
                $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])->default('pending');
                $table->text('special_requests')->nullable();
                $table->text('notes')->nullable();
                $table->decimal('total_price', 10, 2)->default(0);
                $table->string('discount_type')->nullable();
                $table->decimal('discount_percent', 5, 2)->default(0);
                $table->decimal('discount', 10, 2)->default(0);
                $table->string('color')->nullable();
                $table->timestamps();

                $table->foreign('table_id')->references('id')->on('tables')->onDelete('set null');
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
                $table->index(['start_date', 'status']);
            });
        }

        if (!Schema::hasTable('reservations_items')) {
            Schema::create('reservations_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('reservation_id');
                $table->unsignedBigInteger('menu_item_id');
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 10, 2);
                $table->decimal('total_price', 10, 2);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
                $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            });
        }

        // 27. Additional system tables
        if (!Schema::hasTable('order_status_history')) {
            Schema::create('order_status_history', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('order_id');
                $table->string('status');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('changed_by')->nullable();
                $table->timestamp('changed_at')->useCurrent();
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
                $table->index(['order_id', 'changed_at']);
            });
        }

        if (!Schema::hasTable('employee_commissions')) {
            Schema::create('employee_commissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('menu_item_id');
                $table->decimal('commission_rate', 5, 2);
                $table->decimal('commission_amount', 10, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('employee_service_commissions')) {
            Schema::create('employee_service_commissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('menu_item_id');
                $table->decimal('commission_rate', 5, 2);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            });
        }

        // 28. Payments table
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('payment_number')->unique();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->unsignedBigInteger('reservation_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->decimal('payment_amount', 10, 2);
                $table->string('payment_method');
                $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
                $table->enum('invoice_status', ['pending', 'generated', 'sent', 'paid'])->default('pending');
                $table->text('payment_note')->nullable();
                $table->string('transaction_id')->nullable();
                $table->json('payment_details')->nullable();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
                $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('set null');
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
                $table->index(['payment_status', 'created_at']);
                $table->index(['customer_id', 'created_at']);
            });
        }

        // 29. Permission system tables
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_permissions')) {
            Schema::create('user_permissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('permission_id');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
                $table->unique(['user_id', 'permission_id']);
            });
        }

        // 30. Package features table
        if (!Schema::hasTable('package_features')) {
            Schema::create('package_features', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('package_name');
                $table->integer('max_employees')->default(0);
                $table->integer('max_tables')->default(0);
                $table->integer('max_menu_items')->default(0);
                $table->boolean('delivery_enabled')->default(false);
                $table->boolean('online_ordering_enabled')->default(false);
                $table->boolean('reservation_enabled')->default(false);
                $table->boolean('inventory_enabled')->default(false);
                $table->boolean('reports_enabled')->default(false);
                $table->timestamps();
            });
        }

        // 31. Loyalty system tables
        if (!Schema::hasTable('loyalty_transactions')) {
            Schema::create('loyalty_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->constrained()->onDelete('cascade');
                $table->enum('transaction_type', ['earned', 'redeemed', 'expired', 'bonus']);
                $table->integer('points');
                $table->string('reason', 255);
                $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();

                $table->index(['customer_id', 'created_at']);
                $table->index('transaction_type');
            });
        }

        if (!Schema::hasTable('loyalty_settings')) {
            Schema::create('loyalty_settings', function (Blueprint $table) {
                $table->id();
                $table->decimal('points_per_tl', 8, 2)->default(1.00);
                $table->decimal('tl_per_point', 8, 4)->default(0.01);
                $table->integer('min_redemption')->default(100);
                $table->integer('max_redemption_percent')->default(50);
                $table->integer('birthday_bonus')->default(50);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 32. Staff shifts table
        if (!Schema::hasTable('staff_shifts')) {
            Schema::create('staff_shifts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->onDelete('cascade');
                $table->date('shift_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->time('actual_start_time')->nullable();
                $table->time('actual_end_time')->nullable();
                $table->integer('break_duration')->default(0);
                $table->text('notes')->nullable();
                $table->enum('status', ['scheduled', 'active', 'completed', 'cancelled'])->default('scheduled');
                $table->timestamps();

                $table->index(['employee_id', 'shift_date']);
                $table->index('status');
            });
        }

        // 33. Notifications tables
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->text('message');
                $table->enum('type', ['info', 'warning', 'error', 'success']);
                $table->enum('target_type', ['all', 'role', 'user']);
                $table->unsignedBigInteger('target_id')->nullable();
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['target_type', 'target_id']);
                $table->index('is_read');
                $table->index('created_at');
            });
        }

        if (!Schema::hasTable('user_notifications')) {
            Schema::create('user_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('notification_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->unique(['notification_id', 'user_id']);
                $table->index(['user_id', 'is_read']);
            });
        }

        // 34. Audit logs table
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->string('action');
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->index(['model_type', 'model_id']);
                $table->index(['action', 'created_at']);
                $table->index('user_id');
            });
        }
        
        // AUTO_INCREMENT değerlerini ayarla (db.sql'deki değerlere göre)
        // Bu, manuel id'lerle veri eklerken çakışmaları önler
        DB::statement('ALTER TABLE `accounts` AUTO_INCREMENT = 15;');
        DB::statement('ALTER TABLE `customers_account_transactions` AUTO_INCREMENT = 17;');
        DB::statement('ALTER TABLE `employees` AUTO_INCREMENT = 16;');
        DB::statement('ALTER TABLE `employee_groups` AUTO_INCREMENT = 5;');
        DB::statement('ALTER TABLE `employee_positions` AUTO_INCREMENT = 25;');
        DB::statement('ALTER TABLE `expenses` AUTO_INCREMENT = 53;');
        DB::statement('ALTER TABLE `expense_categories` AUTO_INCREMENT = 7;');
        DB::statement('ALTER TABLE `expense_items` AUTO_INCREMENT = 131;');
        DB::statement('ALTER TABLE `expense_types` AUTO_INCREMENT = 7;');
        DB::statement('ALTER TABLE `incomes` AUTO_INCREMENT = 29;');
        DB::statement('ALTER TABLE `income_categories` AUTO_INCREMENT = 6;');
        DB::statement('ALTER TABLE `income_items` AUTO_INCREMENT = 54;');
        DB::statement('ALTER TABLE `income_types` AUTO_INCREMENT = 7;');
        DB::statement('ALTER TABLE `payment_methods` AUTO_INCREMENT = 4;');
        DB::statement('ALTER TABLE `transactions` AUTO_INCREMENT = 77;');
        DB::statement('ALTER TABLE `payrolls` AUTO_INCREMENT = 9;');
        DB::statement('ALTER TABLE `payroll_deductions` AUTO_INCREMENT = 17;');
        DB::statement('ALTER TABLE `payroll_payments` AUTO_INCREMENT = 3;');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all tables in reverse order
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('staff_shifts');
        Schema::dropIfExists('loyalty_settings');
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('package_features');
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('employee_service_commissions');
        Schema::dropIfExists('employee_commissions');
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('reservations_items');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('promissory_notes');
        Schema::dropIfExists('checks');
        Schema::dropIfExists('customers_account_transactions');
        Schema::dropIfExists('income_items');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('income_categories');
        Schema::dropIfExists('income_types');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('expense_items');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('expense_types');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('units');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('tables');
        Schema::dropIfExists('menu_item_addons');
        Schema::dropIfExists('menu_item_variants');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('payroll_payments');
        Schema::dropIfExists('payroll_deductions');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('employee_daily_wages');
        Schema::dropIfExists('employee_positions');
        Schema::dropIfExists('employee_groups');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('users');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};

