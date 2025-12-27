<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // System Tables Seeder - Tüm sistem tabloları ve verileri
        $this->call([
            SystemTablesSeeder::class,
        ]);

        $this->command->info('✅ Seeding completed successfully!');
        $this->command->info('📊 All system tables and data have been seeded from db.sql');
    }
}
