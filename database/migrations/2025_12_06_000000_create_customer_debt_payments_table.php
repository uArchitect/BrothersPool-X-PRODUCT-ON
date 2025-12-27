<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Bu tablo, müşterilere atanan giderlerden (expenses) kaynaklanan borçların
     * kasa üzerinden gerçek zamanlı ödenmesini sağlar.
     * 
     * Mevcut yapıda:
     * - expenses tablosunda customer_id ile giderler müşteriye atanabiliyor
     * - customers_account_transactions ile müşteri hesap hareketleri tutuluyor
     * - Ancak bu borçların kasa (cash) üzerinden ödenmesi için özel mekanizma yok
     * - Sadece check ve promissory_note ile ödeme yapılabiliyor
     * 
     * Bu tablo ile:
     * - Kasa üzerinden gerçek zamanlı borç ödemesi yapılabilir
     * - Borç takibi ve ödeme geçmişi tutulur
     * - Kısmi ödemeler desteklenir
     */
    public function up(): void
    {
        // Tablo zaten varsa oluşturma
        if (Schema::hasTable('customer_debt_payments')) {
            return;
        }

        Schema::create('customer_debt_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('payment_number')->unique();
            
            // İlişkiler
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('account_id'); // Kasa hesabı (accounts tablosundan)
            $table->unsignedBigInteger('reference_id')->nullable(); // İlişkili expense, income vb.
            $table->string('reference_type')->nullable(); // 'expense', 'income', 'manual' vb.
            
            // Borç bilgileri
            $table->decimal('debt_amount', 15, 2); // Toplam borç tutarı (expense'ten gelen)
            $table->decimal('paid_amount', 15, 2); // Bu ödemede ödenen tutar
            $table->decimal('remaining_amount', 15, 2)->default(0); // Kalan borç (hesaplanan)
            
            // Ödeme bilgileri
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'check', 'promissory_note'])->default('cash');
            $table->date('payment_date');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            
            // Durum
            $table->enum('status', ['pending', 'completed', 'cancelled', 'partial'])->default('completed');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes - performans için
            $table->index(['customer_id', 'status']);
            $table->index(['payment_date']);
            $table->index(['reference_type', 'reference_id'], 'cdp_ref_type_ref_idx');
            $table->index('status');
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_debt_payments');
    }
};

