<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define authorization gates
        $this->defineGates();
    }

    /**
     * Define authorization gates for restaurant management
     */
    private function defineGates(): void
    {
        // Admin gates - full access to everything
        Gate::define('admin', function ($user) {
            return $this->hasRole($user, ['admin', 'manager']);
        });

        // Menu management
        Gate::define('manage-menu', function ($user) {
            return $this->hasRole($user, ['admin', 'manager', 'chef']);
        });

        // Table management
        Gate::define('manage-tables', function ($user) {
            return $this->hasRole($user, ['admin', 'manager', 'waiter']);
        });

        // Order management
        Gate::define('manage-orders', function ($user) {
            return $this->hasRole($user, ['admin', 'manager', 'waiter', 'cashier']);
        });

        // Employee management
        Gate::define('manage-employees', function ($user) {
            return $this->hasRole($user, ['admin', 'manager']);
        });

        // Financial management
        Gate::define('manage-finance', function ($user) {
            return $this->hasRole($user, ['admin', 'manager', 'accountant']);
        });

        // Settings management
        Gate::define('manage-settings', function ($user) {
            return $this->hasRole($user, ['admin', 'manager']);
        });

        // Reports access
        Gate::define('view-reports', function ($user) {
            return $this->hasRole($user, ['admin', 'manager']);
        });

        // POS access
        Gate::define('access-pos', function ($user) {
            return $this->hasRole($user, ['admin', 'manager', 'waiter', 'cashier']);
        });

        // Kitchen access
        Gate::define('access-kitchen', function ($user) {
            return $this->hasRole($user, ['admin', 'manager', 'chef', 'cook']);
        });
    }

    /**
     * Check if user has any of the specified roles
     */
    private function hasRole($user, array $roles): bool
    {
        // For now, implement basic role checking based on email
        // In production, this should check the permissions table
        foreach ($roles as $role) {
            if (strpos($user->email, $role) !== false) {
                return true;
            }
        }

        // Check if user has admin email pattern
        if (strpos($user->email, 'admin') !== false) {
            return true;
        }

        return false;
    }
}