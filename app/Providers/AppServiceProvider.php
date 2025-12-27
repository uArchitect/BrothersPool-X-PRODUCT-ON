<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Category;
use App\Models\Reservation;
use App\Observers\ModelAuditObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register audit observers for key models
        Table::observe(ModelAuditObserver::class);
        MenuItem::observe(ModelAuditObserver::class);
        Order::observe(ModelAuditObserver::class);
        Customer::observe(ModelAuditObserver::class);
        Employee::observe(ModelAuditObserver::class);
        Category::observe(ModelAuditObserver::class);
        Reservation::observe(ModelAuditObserver::class);
    }
}
