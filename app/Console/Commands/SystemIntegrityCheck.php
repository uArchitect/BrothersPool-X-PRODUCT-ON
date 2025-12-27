<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SystemIntegrityService;

class SystemIntegrityCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:check 
                            {--fix : Otomatik düzeltmeleri uygula}
                            {--checks : Sadece çek kontrollerini yap}
                            {--notes : Sadece senet kontrollerini yap}
                            {--accounts : Sadece kasa bakiye kontrollerini yap}
                            {--customers : Sadece müşteri bakiye kontrollerini yap}
                            {--orphaned : Sadece sahipsiz kayıt kontrollerini yap}
                            {--account= : Belirli bir hesabı detaylı kontrol et (ID veya isim)}
                            {--customer= : Belirli bir müşteriyi detaylı kontrol et (ID veya isim)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sistem veri tutarlılığını kontrol eder ve hataları düzeltir';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new SystemIntegrityService();
        $autoFix = $this->option('fix');

        $this->info('🔍 Sistem bütünlük kontrolü başlatılıyor...');
        $this->newLine();

        if ($this->option('account')) {
            $this->checkSpecificAccount($service, $this->option('account'), $autoFix);
        } elseif ($this->option('customer')) {
            $this->checkSpecificCustomer($service, $this->option('customer'), $autoFix);
        } elseif ($this->option('checks')) {
            $this->checkChecks($service, $autoFix);
        } elseif ($this->option('notes')) {
            $this->checkNotes($service, $autoFix);
        } elseif ($this->option('accounts')) {
            $this->checkAccounts($service, $autoFix);
        } elseif ($this->option('customers')) {
            $this->checkCustomers($service, $autoFix);
        } elseif ($this->option('orphaned')) {
            $this->checkOrphaned($service, $autoFix);
        } else {
            // Tüm kontrolleri çalıştır
            $this->runAllChecks($service, $autoFix);
        }

        return 0;
    }

    /**
     * Tüm kontrolleri çalıştır
     */
    protected function runAllChecks(SystemIntegrityService $service, $autoFix)
    {
        $this->info('📋 Tüm sistem kontrolleri çalıştırılıyor...');
        $this->newLine();

        $results = $service->runAllChecks($autoFix);

        $this->displayResults($results, $autoFix);
    }

    /**
     * Çek kontrollerini çalıştır
     */
    protected function checkChecks(SystemIntegrityService $service, $autoFix)
    {
        $this->info('🔍 Çek transaction kayıtları kontrol ediliyor...');
        $count = $service->checkChecksTransactions($autoFix);
        $this->info("✅ Kontrol tamamlandı. {$count} sorun bulundu.");
    }

    /**
     * Senet kontrollerini çalıştır
     */
    protected function checkNotes(SystemIntegrityService $service, $autoFix)
    {
        $this->info('🔍 Senet transaction kayıtları kontrol ediliyor...');
        $count = $service->checkPromissoryNotesTransactions($autoFix);
        $this->info("✅ Kontrol tamamlandı. {$count} sorun bulundu.");
    }

    /**
     * Kasa kontrollerini çalıştır
     */
    protected function checkAccounts(SystemIntegrityService $service, $autoFix)
    {
        $this->info('🔍 Kasa bakiyeleri kontrol ediliyor...');
        $count = $service->checkAccountBalances($autoFix);
        $this->info("✅ Kontrol tamamlandı. {$count} sorun bulundu.");
    }

    /**
     * Müşteri kontrollerini çalıştır
     */
    protected function checkCustomers(SystemIntegrityService $service, $autoFix)
    {
        $this->info('🔍 Müşteri bakiyeleri kontrol ediliyor...');
        $count = $service->checkCustomerBalances($autoFix);
        $this->info("✅ Kontrol tamamlandı. {$count} sorun bulundu.");
    }

    /**
     * Sahipsiz kayıt kontrollerini çalıştır
     */
    protected function checkOrphaned(SystemIntegrityService $service, $autoFix)
    {
        $this->info('🔍 Sahipsiz transaction kayıtları kontrol ediliyor...');
        $count = $service->checkOrphanedTransactions($autoFix);
        $this->info("✅ Kontrol tamamlandı. {$count} sorun bulundu.");
    }

    /**
     * Belirli bir hesabı detaylı kontrol et
     */
    protected function checkSpecificAccount(SystemIntegrityService $service, $accountIdentifier, $autoFix)
    {
        $this->info("🔍 Hesap kontrol ediliyor: {$accountIdentifier}");
        $this->newLine();

        $result = $service->checkAccountDetails($accountIdentifier, $autoFix);

        if (isset($result['error'])) {
            $this->error("❌ " . $result['error']);
            return;
        }

        if (!isset($result['account']) || !$result['account']) {
            $this->error("❌ Hesap bulunamadı");
            return;
        }

        $account = $result['account'];
        $accountName = isset($account->name) ? $account->name : 'N/A';
        $accountId = isset($account->id) ? $account->id : 'N/A';
        $accountType = isset($account->type) ? $account->type : 'N/A';

        $this->info("═══════════════════════════════════════════════════════");
        $this->info("📊 HESAP DETAY RAPORU");
        $this->info("═══════════════════════════════════════════════════════");
        $this->newLine();

        $this->info("Hesap Adı: {$accountName}");
        $this->info("Hesap ID: {$accountId}");
        $this->info("Hesap Tipi: {$accountType}");
        $this->newLine();

        $this->info("💰 BAKİYE BİLGİLERİ");
        $this->info("───────────────────────────────────────────────────────");
        $this->line("Mevcut Bakiye: " . number_format($result['current_balance'], 2) . " TL");
        $this->line("Hesaplanan Bakiye: " . number_format($result['calculated_balance'], 2) . " TL");
        
        if ($result['is_balanced']) {
            $this->info("✅ Bakiye tutarlı!");
        } else {
            $this->error("❌ Bakiye tutarsız! Fark: " . number_format($result['difference'], 2) . " TL");
        }
        $this->newLine();

        $this->info("📈 İSTATİSTİKLER");
        $this->info("───────────────────────────────────────────────────────");
        $this->line("Toplam Gelir: " . number_format($result['stats']['total_income'], 2) . " TL");
        $this->line("Toplam Gider: " . number_format($result['stats']['total_expense'], 2) . " TL");
        $this->line("Transaction Sayısı: " . $result['stats']['transaction_count']);
        $this->line("Çek Sayısı: " . $result['stats']['check_count']);
        $this->line("Senet Sayısı: " . $result['stats']['note_count']);
        $this->newLine();

        if (count($result['check_issues']) > 0) {
            $this->warn("⚠️  ÇEK SORUNLARI: " . count($result['check_issues']));
            foreach ($result['check_issues'] as $issue) {
                $this->line("  ✗ Çek #{$issue['check_id']} ({$issue['check_number']}) - Transaction kaydı eksik");
                $this->line("    Tutar: " . number_format($issue['amount'], 2) . " TL, Tip: {$issue['type']}");
            }
            $this->newLine();
        }

        if (count($result['note_issues']) > 0) {
            $this->warn("⚠️  SENET SORUNLARI: " . count($result['note_issues']));
            foreach ($result['note_issues'] as $issue) {
                $this->line("  ✗ Senet #{$issue['note_id']} ({$issue['note_number']}) - Transaction kaydı eksik");
                $this->line("    Tutar: " . number_format($issue['amount'], 2) . " TL, Tip: {$issue['type']}");
            }
            $this->newLine();
        }

        if ($autoFix) {
            if (isset($result['fixed'])) {
                $this->info("✅ " . $result['fix_message']);
                $this->newLine();
            }

            if (isset($result['check_fixes'])) {
                $this->info("✅ Çek Düzeltmeleri:");
                foreach ($result['check_fixes'] as $fix) {
                    $this->line("  ✓ " . $fix);
                }
                $this->newLine();
            }

            if (isset($result['note_fixes'])) {
                $this->info("✅ Senet Düzeltmeleri:");
                foreach ($result['note_fixes'] as $fix) {
                    $this->line("  ✓ " . $fix);
                }
                $this->newLine();
            }
        }

        $this->info("═══════════════════════════════════════════════════════");
    }

    /**
     * Belirli bir müşteriyi detaylı kontrol et
     */
    protected function checkSpecificCustomer(SystemIntegrityService $service, $customerIdentifier, $autoFix)
    {
        $this->info("🔍 Müşteri kontrol ediliyor: {$customerIdentifier}");
        $this->newLine();

        $result = $service->checkCustomerDetails($customerIdentifier, $autoFix);

        if (isset($result['error']) || !isset($result['customer']) || !$result['customer']) {
            $errorMsg = $result['error'] ?? 'Müşteri bulunamadı';
            $this->error("❌ " . $errorMsg);
            return;
        }

        $customer = $result['customer'];
        $customerName = isset($customer->title) ? $customer->title : 'N/A';
        $customerId = isset($customer->id) ? $customer->id : 'N/A';

        $this->info("═══════════════════════════════════════════════════════");
        $this->info("📊 MÜŞTERİ DETAY RAPORU");
        $this->info("═══════════════════════════════════════════════════════");
        $this->newLine();

        $this->info("Müşteri: {$customerName} (ID: {$customerId})");
        $this->newLine();

        $this->info("💰 BAKİYE BİLGİLERİ");
        $this->info("───────────────────────────────────────────────────────");
        $this->line("Mevcut Bakiye: " . number_format($result['current_balance'], 2) . " TL");
        $this->line("Hesaplanan Bakiye: " . number_format($result['calculated_balance'], 2) . " TL");
        
        if ($result['is_balanced']) {
            $this->info("✅ Bakiye tutarlı!");
        } else {
            $this->error("❌ Bakiye tutarsız! Fark: " . number_format($result['difference'], 2) . " TL");
        }
        $this->newLine();

        $this->info("📈 İSTATİSTİKLER");
        $this->info("───────────────────────────────────────────────────────");
        $this->line("Toplam Borçlar (Expenses): " . number_format($result['stats']['total_expenses'], 2) . " TL");
        $this->line("Toplam Borç Ödemeleri: " . number_format($result['stats']['total_debt_payments'], 2) . " TL");
        $this->line("Transaction Sayısı: " . $result['stats']['transaction_count']);
        $this->line("Expense Sayısı: " . $result['stats']['expense_count']);
        $this->line("Borç Ödeme Sayısı: " . $result['stats']['debt_payment_count']);
        $this->newLine();

        if ($autoFix) {
            if (isset($result['fixed'])) {
                $this->info("✅ " . $result['fix_message']);
                $this->newLine();
            }

            if (isset($result['fix_error'])) {
                $this->error("❌ Düzeltme hatası: " . $result['fix_error']);
                $this->newLine();
            }
        }

        $this->info("═══════════════════════════════════════════════════════");
    }

    /**
     * Sonuçları göster
     */
    protected function displayResults($results, $autoFix)
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('📊 SİSTEM KONTROL SONUÇLARI');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        if ($autoFix) {
            $this->info("✅ Otomatik düzeltme: AÇIK");
        } else {
            $this->warn("⚠️  Otomatik düzeltme: KAPALI (--fix parametresi ile açabilirsiniz)");
        }

        $this->newLine();

        // Düzeltmeler
        if (count($results['fixes']) > 0) {
            $this->info("✅ Düzeltilen Sorunlar: " . count($results['fixes']));
            $this->newLine();
            
            foreach ($results['fixes'] as $fix) {
                $this->line("  ✓ " . $fix['message']);
            }
            $this->newLine();
        } else {
            $this->info("✅ Düzeltilen sorun yok");
            $this->newLine();
        }

        // Hatalar
        if (count($results['errors']) > 0) {
            $this->error("❌ Bulunan Hatalar: " . count($results['errors']));
            $this->newLine();
            
            foreach ($results['errors'] as $error) {
                $this->line("  ✗ " . $error['message']);
            }
            $this->newLine();
        } else {
            $this->info("✅ Hata bulunamadı");
            $this->newLine();
        }

        $this->info('═══════════════════════════════════════════════════════');
        $this->info("📈 Toplam Düzeltme: {$results['total_fixes']}");
        $this->info("⚠️  Toplam Hata: {$results['total_errors']}");
        $this->info('═══════════════════════════════════════════════════════');
    }
}

