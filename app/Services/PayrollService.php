<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollService {
    /**
     * Periyot bazlı ücret hesapla
     */
    public function calculatePeriodWage($employeeId, $startDate, $endDate) {
        $employee = DB::table('employees')->where('id', $employeeId)->first();
        
        if (!$employee) {
            throw new \Exception('Çalışan bulunamadı!');
        }
        
        // İşe giriş tarihi kontrolü
        $hireDate = Carbon::parse($employee->hire_date);
        $startDateCarbon = Carbon::parse($startDate);
        
        if ($startDateCarbon->lt($hireDate)) {
            throw new \Exception('Bordro başlangıç tarihi, çalışanın işe giriş tarihinden (' . $hireDate->format('d.m.Y') . ') önce olamaz!');
        }
        
        // staff_shifts'ten çalışılan günleri al
        $workingDays = DB::table('staff_shifts')
            ->where('employee_id', $employeeId)
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();
        
        // Eğer shift kaydı yoksa, tarih aralığındaki iş günlerini say (hafta sonu hariç)
        if ($workingDays == 0) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $workingDays = 0;
            
            while ($start->lte($end)) {
                // Hafta sonu değilse ve tatil değilse
                if (!$start->isWeekend() && !$this->isHoliday($start->format('Y-m-d'))) {
                    $workingDays++;
                }
                $start->addDay();
            }
        }
        
        // Günlük ücret hesapla
        $dailyWage = $this->calculateDailyWage($employee);
        
        if ($dailyWage <= 0) {
            throw new \Exception('Çalışanın günlük ücreti hesaplanamadı! Lütfen çalışan bilgilerini kontrol edin.');
        }
        
        // Toplam ücret
        $totalWage = round($dailyWage * $workingDays, 2);
        
        return [
            'working_days' => $workingDays,
            'daily_wage' => $dailyWage,
            'total_wage' => $totalWage,
        ];
    }
    
    /**
     * Günlük ücret hesapla
     */
    public function calculateDailyWage($employee) {
        switch($employee->payment_frequency) {
            case 'daily':
                return $employee->daily_wage ?? 0;
            case 'weekly':
                // Haftalık ücret genelde 5-6 iş günü için
                if ($employee->weekly_wage && $employee->working_days_per_month) {
                    // Haftalık iş günü ortalama (aylık gün / 4.33 hafta)
                    $weeklyWorkingDays = $employee->working_days_per_month / 4.33;
                    return $employee->weekly_wage / max(1, $weeklyWorkingDays);
                }
                return 0;
            case 'monthly':
                return $employee->monthly_salary ? 
                    ($employee->monthly_salary / max(1, $employee->working_days_per_month)) : 0;
            case 'hourly':
                return $employee->hourly_wage ? ($employee->hourly_wage * 8) : 0;
            default:
                return 0;
        }
    }
    
    /**
     * Base salary hesapla (çalışanın ödeme periyoduna göre)
     */
    private function getBaseSalary($employee) {
        switch($employee->payment_frequency) {
            case 'monthly':
                return $employee->monthly_salary;
            case 'weekly':
                return $employee->weekly_wage;
            case 'daily':
                return $employee->daily_wage;
            case 'hourly':
                return $employee->hourly_wage;
            default:
                return $employee->monthly_salary ?? $employee->daily_wage ?? $employee->hourly_wage ?? 0;
        }
    }
    
    /**
     * Bordro oluştur
     */
    public function createPayroll($data) {
        DB::beginTransaction();
        try {
            $employee = DB::table('employees')->where('id', $data['employee_id'])->first();
            
            if (!$employee) {
                throw new \Exception('Çalışan bulunamadı!');
            }
            
            // Aynı personel, periyot tipi ve başlangıç tarihi için bordro kontrolü
            $existingPayroll = DB::table('payrolls')
                ->where('employee_id', $data['employee_id'])
                ->where('period_type', $data['period_type'])
                ->where('period_start_date', $data['period_start_date'])
                ->first();
            
            if ($existingPayroll) {
                throw new \Exception('Bu personel için seçilen periyot tipi ve başlangıç tarihinde zaten bir bordro mevcut! Lütfen mevcut bordroyu kullanın veya farklı bir tarih seçin.');
            }
            
            // Periyot hesaplama
            $calculation = $this->calculatePeriodWage(
                $employee->id,
                $data['period_start_date'],
                $data['period_end_date']
            );
            
            // Kesintileri hesapla (SGK, vergi vb.)
            $deductions = $this->calculateDeductions($calculation['total_wage'], $employee);
            $grossSalary = round($calculation['total_wage'], 2);
            $totalDeductions = round($deductions['total'], 2);
            $netSalary = round($grossSalary - $totalDeductions, 2);
            
            // Bordro oluştur
            $payrollId = DB::table('payrolls')->insertGetId([
                'employee_id' => $employee->id,
                'payroll_period' => $data['payroll_period'],
                'period_type' => $data['period_type'],
                'period_start_date' => $data['period_start_date'],
                'period_end_date' => $data['period_end_date'],
                'working_days' => $calculation['working_days'],
                'daily_wage' => round($calculation['daily_wage'], 2),
                'calculated_amount' => round($calculation['total_wage'], 2),
                'gross_salary' => $grossSalary,
                'deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'remaining_amount' => $netSalary,
                'base_salary' => $this->getBaseSalary($employee),
                'base_salary_date' => now(),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Kesintileri kaydet
            if ($totalDeductions > 0) {
                $this->saveDeductions($payrollId, $deductions);
            }
            
            // employee_daily_wages kayıtlarını oluştur
            $this->createDailyWageRecords($employee, $payrollId, $data['period_start_date'], $data['period_end_date'], $calculation);
            
            DB::commit();
            
            // Oluşturulan bordroyu döndür
            return DB::table('payrolls')->where('id', $payrollId)->first();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Kısmi ödeme ekle
     */
    public function addPartialPayment($payrollId, $paymentData) {
        DB::beginTransaction();
        try {
            $payroll = DB::table('payrolls')->where('id', $payrollId)->first();
            
            if (!$payroll) {
                throw new \Exception('Bordro bulunamadı!');
            }
            
            // Ödeme tutar kontrolü (remaining_amount ile karşılaştır)
            $remainingAmount = round($payroll->remaining_amount, 2);
            $paymentAmount = round($paymentData['amount'], 2);
            
            if ($paymentAmount <= 0) {
                throw new \Exception('Ödeme tutarı 0\'dan büyük olmalıdır!');
            }
            
            if ($paymentAmount > $remainingAmount) {
                throw new \Exception('Ödeme tutarı kalan tutardan (' . number_format($remainingAmount, 2) . ' TL) fazla olamaz!');
            }
            
            // Ödeme ekle
            $paymentId = DB::table('payroll_payments')->insertGetId([
                'payroll_id' => $payrollId,
                'payment_date' => $paymentData['payment_date'],
                'amount' => round($paymentData['amount'], 2),
                'payment_method' => $paymentData['payment_method'],
                'bank_name' => $paymentData['bank_name'] ?? null,
                'account_number' => $paymentData['account_number'] ?? null,
                'reference_number' => $paymentData['reference_number'] ?? null,
                'description' => $paymentData['description'] ?? null,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Bordro durumunu güncelle
            $this->updatePayrollStatus($payrollId);
            
            DB::commit();
            
            return DB::table('payroll_payments')->where('id', $paymentId)->first();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Bordro durumunu güncelle
     */
    public function updatePayrollStatus($payrollId) {
        $payroll = DB::table('payrolls')->where('id', $payrollId)->first();
        
        if (!$payroll) {
            throw new \Exception('Bordro bulunamadı!');
        }
        
        $totalPaid = round(DB::table('payroll_payments')
            ->where('payroll_id', $payrollId)
            ->where('status', 'completed')
            ->sum('amount'), 2);
        
        $remaining = round($payroll->net_salary - $totalPaid, 2);
        
        $status = $remaining <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'pending');
        
        DB::table('payrolls')
            ->where('id', $payrollId)
            ->update([
                'total_paid' => $totalPaid,
                'remaining_amount' => $remaining,
                'status' => $status,
                'updated_at' => now(),
            ]);
    }
    
    /**
     * Günlük ücret kayıtlarını oluştur
     */
    private function createDailyWageRecords($employee, $payrollId, $startDate, $endDate, array $calculation) {
        // staff_shifts'ten her gün için kayıt oluştur
        $shifts = DB::table('staff_shifts')
            ->where('employee_id', $employee->id)
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();
        
        $records = [];
        
        foreach ($shifts as $shift) {
            $isHoliday = $this->isHoliday($shift->shift_date);
            $workedHours = $this->calculateWorkedHours($shift);
            $isOvertime = $workedHours > 8; // Normal çalışma saati 8 saat
            $multiplier = $this->calculateMultiplier($isHoliday, $isOvertime);
            
            $records[] = [
                'employee_id' => $employee->id,
                'staff_shift_id' => $shift->id,
                'payroll_id' => $payrollId,
                'calculation_date' => now(),
                'work_date' => $shift->shift_date,
                'daily_wage' => $calculation['daily_wage'],
                'worked_hours' => $workedHours,
                'is_holiday' => $isHoliday,
                'is_overtime' => $isOvertime,
                'multiplier' => $multiplier,
                'calculated_amount' => round($calculation['daily_wage'] * $multiplier, 2),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if (!empty($records)) {
            DB::table('employee_daily_wages')->insert($records);
        }
    }
    
    /**
     * Tatil günü kontrolü
     */
    private function isHoliday($date) {
        $holidays = [
            // Yılbaşı
            Carbon::parse($date)->format('Y') . '-01-01',
            // Ulusal Egemenlik ve Çocuk Bayramı
            Carbon::parse($date)->format('Y') . '-04-23',
            // İşçi Bayramı
            Carbon::parse($date)->format('Y') . '-05-01',
            // Atatürk'ü Anma, Gençlik ve Spor Bayramı
            Carbon::parse($date)->format('Y') . '-05-19',
            // Zafer Bayramı
            Carbon::parse($date)->format('Y') . '-08-30',
            // Cumhuriyet Bayramı
            Carbon::parse($date)->format('Y') . '-10-29',
        ];
        
        return in_array(Carbon::parse($date)->format('Y-m-d'), $holidays);
    }
    
    /**
     * Çalışılan saat hesapla
     */
    private function calculateWorkedHours($shift) {
        if (!isset($shift->actual_start_time) || !isset($shift->actual_end_time) || 
            !$shift->actual_start_time || !$shift->actual_end_time) {
            // Eğer gerçek saatler yoksa, planlanan saatleri kullan
            if (!isset($shift->start_time) || !isset($shift->end_time) || 
                !$shift->start_time || !$shift->end_time) {
                return 0;
            }
            $start = Carbon::parse($shift->shift_date . ' ' . $shift->start_time);
            $end = Carbon::parse($shift->shift_date . ' ' . $shift->end_time);
        } else {
            $start = Carbon::parse($shift->shift_date . ' ' . $shift->actual_start_time);
            $end = Carbon::parse($shift->shift_date . ' ' . $shift->actual_end_time);
        }
        
        $hours = $end->diffInHours($start);
        $breakDuration = $shift->break_duration ?? 0;
        $hours -= ($breakDuration / 60); // Mola süresini çıkar (dakika -> saat)
        
        return max(0, round($hours, 2));
    }
    
    /**
     * Çarpan hesapla (tatil ve fazla mesai için)
     */
    private function calculateMultiplier($isHoliday, $isOvertime) {
        $multiplier = 1.0;
        
        if ($isHoliday) {
            $multiplier = 1.5; // Tatil çarpanı
        }
        
        if ($isOvertime) {
            $multiplier = max($multiplier, 1.5); // Fazla mesai çarpanı (tatil varsa daha yüksek olanı al)
        }
        
        return $multiplier;
    }
    
    /**
     * Kesintileri hesapla (SGK, vergi vb.)
     */
    private function calculateDeductions($grossSalary, $employee) {
        $deductions = [
            'sgk_employee' => 0,
            'sgk_employer' => 0,
            'tax' => 0,
            'stamp_duty' => 0,
            'total' => 0
        ];
        
        // SGK İşçi Payı: %14 (brüt maaşın üzerinden)
        $deductions['sgk_employee'] = round($grossSalary * 0.14, 2);
        
        // SGK İşveren Payı: %20.5 (brüt maaşın üzerinden) - Bu bordroya dahil değil ama kayıt için
        $deductions['sgk_employer'] = round($grossSalary * 0.205, 2);
        
        // Gelir Vergisi: %15 (SGK kesintisi sonrası kalan tutarın üzerinden)
        $taxableAmount = $grossSalary - $deductions['sgk_employee'];
        $deductions['tax'] = round($taxableAmount * 0.15, 2);
        
        // Damga Vergisi: %0.759 (brüt maaşın üzerinden)
        $deductions['stamp_duty'] = round($grossSalary * 0.00759, 2);
        
        // Toplam kesinti
        $deductions['total'] = round(
            $deductions['sgk_employee'] + 
            $deductions['tax'] + 
            $deductions['stamp_duty'], 
            2
        );
        
        return $deductions;
    }
    
    /**
     * Kesintileri veritabanına kaydet
     */
    private function saveDeductions($payrollId, $deductions) {
        $deductionRecords = [];
        
        if ($deductions['sgk_employee'] > 0) {
            $deductionRecords[] = [
                'payroll_id' => $payrollId,
                'deduction_type' => 'sgk_employee',
                'amount' => $deductions['sgk_employee'],
                'rate' => 14.0,
                'description' => 'SGK İşçi Payı',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if ($deductions['sgk_employer'] > 0) {
            $deductionRecords[] = [
                'payroll_id' => $payrollId,
                'deduction_type' => 'sgk_employer',
                'amount' => $deductions['sgk_employer'],
                'rate' => 20.5,
                'description' => 'SGK İşveren Payı',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if ($deductions['tax'] > 0) {
            $deductionRecords[] = [
                'payroll_id' => $payrollId,
                'deduction_type' => 'tax',
                'amount' => $deductions['tax'],
                'rate' => 15.0,
                'description' => 'Gelir Vergisi',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if ($deductions['stamp_duty'] > 0) {
            $deductionRecords[] = [
                'payroll_id' => $payrollId,
                'deduction_type' => 'stamp_duty',
                'amount' => $deductions['stamp_duty'],
                'rate' => 0.759,
                'description' => 'Damga Vergisi',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if (!empty($deductionRecords)) {
            DB::table('payroll_deductions')->insert($deductionRecords);
        }
    }
}

