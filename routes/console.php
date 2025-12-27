<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('reset:app', function () {
    $this->info("🔄 Uygulama önbelleği ve geçici dosyalar temizleniyor...");

    // Cache temizleme
    Artisan::call('cache:clear');
    $this->line('✔️ Cache temizlendi.');

    // Config cache temizleme
    Artisan::call('config:clear');
    $this->line('✔️ Config cache temizlendi.');

    // Route cache temizleme
    Artisan::call('route:clear');
    $this->line('✔️ Route cache temizlendi.');

    // View cache temizleme
    Artisan::call('view:clear');
    $this->line('✔️ View cache temizlendi.');

    // Event cache temizleme (Laravel 8+)
    try {
        Artisan::call('event:clear');
        $this->line('✔️ Event cache temizlendi.');
    } catch (\Exception $e) {
        // event:clear komutu mevcut değilse sessizce geç
    }

    $this->info("\n🎉 Tüm önbellekler başarıyla temizlendi!");
    $this->line("\n──────────────────────────────────────────────");
    $this->line("   👨‍💻  Developer: Uğurcan Yaş");
    $this->line("──────────────────────────────────────────────\n");
    $this->comment("Her şey tertemiz! İyi çalışmalar dilerim. ✨");
})->purpose('Uygulama önbelleklerini ve geçici dosyaları temizler (Kurumsal İmza ile)');
