<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

abstract class BaseService
{
    /**
     * Execute a database transaction
     */
    protected function transaction(callable $callback)
    {
        return DB::transaction($callback);
    }

    /**
     * Log an action with context
     */
    protected function log(string $action, array $context = []): void
    {
        Log::info($action, array_merge($context, [
            'user_id' => Auth::id(),
            'timestamp' => now(),
        ]));
    }

    /**
     * Log an error with context
     */
    protected function logError(string $action, \Exception $e, array $context = []): void
    {
        Log::error($action . ': ' . $e->getMessage(), array_merge($context, [
            'user_id' => Auth::id(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]));
    }

    /**
     * Generate a unique number with prefix
     */
    protected function generateUniqueNumber(string $table, string $column, string $prefix, int $padding = 6): string
    {
        $maxId = DB::table($table)->max('id') ?? 0;
        return $prefix . str_pad($maxId + 1, $padding, '0', STR_PAD_LEFT);
    }

    /**
     * Validate required fields
     */
    protected function validateRequired(array $data, array $requiredFields): void
    {
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("Field '{$field}' is required");
            }
        }
    }

    /**
     * Sanitize input data
     */
    protected function sanitizeData(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            // Remove null bytes and trim strings
            if (is_string($value)) {
                $sanitized[$key] = trim(str_replace("\0", '', $value));
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Format currency amount
     */
    protected function formatCurrency(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    /**
     * Calculate percentage
     */
    protected function calculatePercentage(float $amount, float $percentage): float
    {
        return ($amount * $percentage) / 100;
    }

    /**
     * Get current user ID or null if not authenticated
     */
    protected function getCurrentUserId(): ?int
    {
        return Auth::id();
    }

    /**
     * Check if user has permission (basic implementation)
     */
    protected function hasPermission(string $permission): bool
    {
        // Basic permission check - can be enhanced with proper role system
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin users have all permissions
        if (strpos($user->email, 'admin') !== false) {
            return true;
        }

        // Check specific permissions based on email patterns
        $permissionMap = [
            'manage_menu' => ['chef', 'manager'],
            'manage_orders' => ['waiter', 'manager', 'cashier'],
            'manage_tables' => ['waiter', 'manager'],
            'manage_employees' => ['manager'],
            'manage_finance' => ['manager', 'accountant'],
        ];

        $allowedRoles = $permissionMap[$permission] ?? [];

        foreach ($allowedRoles as $role) {
            if (strpos($user->email, $role) !== false) {
                return true;
            }
        }

        return false;
    }
}
