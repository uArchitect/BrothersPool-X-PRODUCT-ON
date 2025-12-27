<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OrderService;
use App\Services\StockService;
use App\Services\PaymentService;
use App\Services\ReservationService;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(OrderService::class, function ($app) {
            return new OrderService();
        });

        $this->app->singleton(StockService::class, function ($app) {
            return new StockService();
        });

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService();
        });

        $this->app->singleton(ReservationService::class, function ($app) {
            return new ReservationService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}