<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class OptimizeApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:optimize {--clear : Clear all caches first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize application performance by warming caches and clearing unnecessary data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 Starting application optimization...');

        if ($this->option('clear')) {
            $this->clearCaches();
        }

        $this->warmCaches();
        $this->optimizeDatabase();
        $this->clearLogs();

        $this->info('✅ Application optimization completed successfully!');
        
        return 0;
    }

    /**
     * Clear all caches
     */
    private function clearCaches()
    {
        $this->info('🧹 Clearing caches...');
        
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        $this->info('✅ Caches cleared');
    }

    /**
     * Warm frequently used caches
     */
    private function warmCaches()
    {
        $this->info('🔥 Warming caches...');

        // Warm settings cache
        Cache::remember('settings', 3600, function () {
            return DB::table('settings')->find(1);
        });

        // Warm categories cache
        Cache::remember('categories', 3600, function () {
            return DB::table('categories')->select('id', 'name')->get();
        });

        // Warm tables cache
        Cache::remember('tables', 3600, function () {
            return DB::table('tables')->get();
        });

        // Warm payment methods cache
        Cache::remember('payment_methods', 3600, function () {
            return DB::table('payment_methods')->get();
        });

        // Warm package features cache
        Cache::remember('package_features', 3600, function () {
            return DB::table('package_features')->first();
        });

        $this->info('✅ Caches warmed');
    }

    /**
     * Optimize database
     */
    private function optimizeDatabase()
    {
        $this->info('🗄️ Optimizing database...');

        try {
            // Get table sizes
            $tables = ['orders', 'order_items', 'menu_items', 'categories', 'tables'];
            
            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::statement("OPTIMIZE TABLE `{$table}`");
                }
            }

            $this->info('✅ Database optimized');
        } catch (\Exception $e) {
            $this->warn('⚠️ Database optimization failed: ' . $e->getMessage());
        }
    }

    /**
     * Clear old logs
     */
    private function clearLogs()
    {
        $this->info('📝 Clearing old logs...');

        try {
            $logPath = storage_path('logs/laravel.log');
            if (file_exists($logPath) && filesize($logPath) > 10 * 1024 * 1024) { // 10MB
                file_put_contents($logPath, '');
                $this->info('✅ Logs cleared');
            } else {
                $this->info('ℹ️ Logs are within acceptable size');
            }
        } catch (\Exception $e) {
            $this->warn('⚠️ Log clearing failed: ' . $e->getMessage());
        }
    }
}

