<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Önce foreign key constraint'i kaldır
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // account_id kolonunu nullable yap
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable()->change();
        });
        
        // Foreign key constraint'i tekrar ekle (onDelete set null olarak)
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
        });
        
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('set null');
        });
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
        });
        
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable(false)->change();
        });
        
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};

