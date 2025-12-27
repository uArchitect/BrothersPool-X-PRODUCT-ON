<?php

namespace App\Http\Controllers;

use App\Services\PayrollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollController extends Controller
{
    protected $payrollService;
    
    public function __construct(PayrollService $payrollService) {
        $this->payrollService = $payrollService;
    }
    
    public function index() {
        $payrolls = DB::table('payrolls')
            ->join('employees', 'payrolls.employee_id', '=', 'employees.id')
            ->select('payrolls.*', 'employees.name as employee_name')
            ->orderBy('payrolls.created_at', 'desc')
            ->get();
        
        return view('payrolls.index', compact('payrolls'));
    }
    
    public function create() {
        // Tüm personelleri getir (aktif olmayanlar da dahil, ama uyarı ile)
        $employees = DB::table('employees')
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();
        return view('payrolls.create', compact('employees'));
    }
    
    public function store(Request $request) {
        // Validasyon
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period_type' => 'required|in:daily,weekly,monthly,hourly',
            'period_start_date' => 'required|date',
            'period_end_date' => 'required|date|after_or_equal:period_start_date',
        ]);
        
        // İşe giriş tarihi kontrolü
        $employee = DB::table('employees')->where('id', $request->employee_id)->first();
        if ($employee && $employee->hire_date) {
            $hireDate = Carbon::parse($employee->hire_date);
            $startDate = Carbon::parse($request->period_start_date);
            
            if ($startDate->lt($hireDate)) {
                return back()->withInput()->with('error', 'Bordro başlangıç tarihi, çalışanın işe giriş tarihinden (' . $hireDate->format('d.m.Y') . ') önce olamaz!');
            }
        }
        
        // Payroll period string oluştur
        $period = $this->generatePeriodString(
            $request->period_type,
            $request->period_start_date
        );
        
        $data = $request->all();
        $data['payroll_period'] = $period;
        
        try {
            $payroll = $this->payrollService->createPayroll($data);
            return redirect()->route('payrolls.show', $payroll->id)
                ->with('success', 'Bordro başarıyla oluşturuldu');
        } catch (\Exception $e) {
            // Eğer duplicate entry hatası ise, mevcut bordroyu bul ve yönlendir
            if (strpos($e->getMessage(), 'Duplicate entry') !== false || strpos($e->getMessage(), 'zaten bir bordro mevcut') !== false) {
                $existingPayroll = DB::table('payrolls')
                    ->where('employee_id', $request->employee_id)
                    ->where('period_type', $request->period_type)
                    ->where('period_start_date', $request->period_start_date)
                    ->first();
                
                if ($existingPayroll) {
                    return redirect()->route('payrolls.show', $existingPayroll->id)
                        ->with('info', 'Bu personel için seçilen periyot tipi ve başlangıç tarihinde zaten bir bordro mevcut. Mevcut bordro gösteriliyor.');
                }
            }
            
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
    
    public function show($id) {
        $payroll = DB::table('payrolls')
            ->join('employees', 'payrolls.employee_id', '=', 'employees.id')
            ->where('payrolls.id', $id)
            ->select('payrolls.*', 'employees.name as employee_name', 'employees.iban', 'employees.bank_name')
            ->first();
        
        if (!$payroll) {
            return redirect()->route('payrolls.index')->with('error', 'Bordro bulunamadı!');
        }
        
        // Ödemeler
        $payments = DB::table('payroll_payments')
            ->where('payroll_id', $id)
            ->orderBy('payment_date', 'desc')
            ->get();
        
        // Günlük ücret kayıtları (employee_daily_wages)
        $dailyWages = DB::table('employee_daily_wages')
            ->where('payroll_id', $id)
            ->orderBy('work_date', 'asc')
            ->get();
        
        // Kesintiler (payroll_deductions)
        $deductions = DB::table('payroll_deductions')
            ->where('payroll_id', $id)
            ->get();
        
        return view('payrolls.show', compact('payroll', 'payments', 'dailyWages', 'deductions'));
    }
    
    public function addPayment(Request $request, $id) {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check,other',
            'bank_name' => 'nullable|string|max:255',
        ]);
        
        try {
            $payment = $this->payrollService->addPartialPayment($id, $request->all());
            return back()->with('success', 'Ödeme başarıyla eklendi');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
    
    public function cancel($id) {
        try {
            $payroll = DB::table('payrolls')->where('id', $id)->first();
            
            if (!$payroll) {
                return redirect()->route('payrolls.index')->with('error', 'Bordro bulunamadı!');
            }
            
            if ($payroll->status == 'cancelled') {
                return back()->with('error', 'Bu bordro zaten iptal edilmiş!');
            }
            
            // Ödeme varsa iptal edilemez
            $hasPayments = DB::table('payroll_payments')
                ->where('payroll_id', $id)
                ->where('status', 'completed')
                ->exists();
            
            if ($hasPayments) {
                return back()->with('error', 'Ödeme yapılmış bordrolar iptal edilemez!');
            }
            
            DB::table('payrolls')
                ->where('id', $id)
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now(),
                ]);
            
            return back()->with('success', 'Bordro başarıyla iptal edildi');
        } catch (\Exception $e) {
            return back()->with('error', 'Bordro iptal edilirken hata oluştu: ' . $e->getMessage());
        }
    }
    
    public function destroy($id) {
        try {
            $payroll = DB::table('payrolls')->where('id', $id)->first();
            
            if (!$payroll) {
                return redirect()->route('payrolls.index')->with('error', 'Bordro bulunamadı!');
            }
            
            // Ödeme varsa silinemez
            $hasPayments = DB::table('payroll_payments')
                ->where('payroll_id', $id)
                ->where('status', 'completed')
                ->exists();
            
            if ($hasPayments) {
                return back()->with('error', 'Ödeme yapılmış bordrolar silinemez! Önce ödemeleri silin.');
            }
            
            DB::beginTransaction();
            
            // İlişkili kayıtları sil
            DB::table('employee_daily_wages')->where('payroll_id', $id)->delete();
            DB::table('payroll_deductions')->where('payroll_id', $id)->delete();
            DB::table('payroll_payments')->where('payroll_id', $id)->delete();
            DB::table('payrolls')->where('id', $id)->delete();
            
            DB::commit();
            
            return redirect()->route('payrolls.index')->with('success', 'Bordro başarıyla silindi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bordro silinirken hata oluştu: ' . $e->getMessage());
        }
    }
    
    public function deletePayment($id, $paymentId) {
        try {
            $payment = DB::table('payroll_payments')
                ->where('id', $paymentId)
                ->where('payroll_id', $id)
                ->first();
            
            if (!$payment) {
                return back()->with('error', 'Ödeme bulunamadı!');
            }
            
            if ($payment->status != 'completed') {
                return back()->with('error', 'Sadece tamamlanmış ödemeler silinebilir!');
            }
            
            DB::beginTransaction();
            
            // Ödemeyi sil
            DB::table('payroll_payments')->where('id', $paymentId)->delete();
            
            // Bordro durumunu güncelle
            $this->payrollService->updatePayrollStatus($id);
            
            DB::commit();
            
            return back()->with('success', 'Ödeme başarıyla silindi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ödeme silinirken hata oluştu: ' . $e->getMessage());
        }
    }
    
    public function addDeduction(Request $request, $id) {
        $request->validate([
            'deduction_type' => 'required|in:sgk_employee,sgk_employer,tax,stamp_duty,other',
            'amount' => 'required|numeric|min:0.01',
            'rate' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string|max:500',
        ]);
        
        try {
            DB::beginTransaction();
            
            $deductionId = DB::table('payroll_deductions')->insertGetId([
                'payroll_id' => $id,
                'deduction_type' => $request->deduction_type,
                'amount' => round($request->amount, 2),
                'rate' => $request->rate ? round($request->rate, 2) : null,
                'description' => $request->description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Bordro kesintilerini güncelle
            $totalDeductions = round(DB::table('payroll_deductions')
                ->where('payroll_id', $id)
                ->sum('amount'), 2);
            
            $payroll = DB::table('payrolls')->where('id', $id)->first();
            $newNetSalary = round($payroll->gross_salary - $totalDeductions, 2);
            $newRemaining = round($newNetSalary - $payroll->total_paid, 2);
            
            DB::table('payrolls')
                ->where('id', $id)
                ->update([
                    'deductions' => $totalDeductions,
                    'net_salary' => $newNetSalary,
                    'remaining_amount' => $newRemaining,
                    'updated_at' => now(),
                ]);
            
            DB::commit();
            
            return back()->with('success', 'Kesinti başarıyla eklendi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Kesinti eklenirken hata oluştu: ' . $e->getMessage());
        }
    }
    
    public function deleteDeduction($id, $deductionId) {
        try {
            DB::beginTransaction();
            
            DB::table('payroll_deductions')->where('id', $deductionId)->where('payroll_id', $id)->delete();
            
            // Bordro kesintilerini güncelle
            $totalDeductions = round(DB::table('payroll_deductions')
                ->where('payroll_id', $id)
                ->sum('amount'), 2);
            
            $payroll = DB::table('payrolls')->where('id', $id)->first();
            $newNetSalary = round($payroll->gross_salary - $totalDeductions, 2);
            $newRemaining = round($newNetSalary - $payroll->total_paid, 2);
            
            DB::table('payrolls')
                ->where('id', $id)
                ->update([
                    'deductions' => $totalDeductions,
                    'net_salary' => $newNetSalary,
                    'remaining_amount' => $newRemaining,
                    'updated_at' => now(),
                ]);
            
            DB::commit();
            
            return back()->with('success', 'Kesinti başarıyla silindi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Kesinti silinirken hata oluştu: ' . $e->getMessage());
        }
    }
    
    public function adjustAmount(Request $request, $id) {
        $request->validate([
            'adjustment_type' => 'required|in:add,subtract',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:500',
        ]);
        
        try {
            DB::beginTransaction();
            
            $payroll = DB::table('payrolls')->where('id', $id)->first();
            
            if (!$payroll) {
                return back()->with('error', 'Bordro bulunamadı!');
            }
            
            $adjustmentAmount = round($request->amount, 2);
            
            if ($request->adjustment_type == 'add') {
                // Eksik para ekleme - net maaşa ekle
                $newNetSalary = round($payroll->net_salary + $adjustmentAmount, 2);
                $newRemaining = round($payroll->remaining_amount + $adjustmentAmount, 2);
            } else {
                // Eksik para çıkarma
                if ($adjustmentAmount > $payroll->remaining_amount) {
                    return back()->with('error', 'Çıkarılacak tutar kalan tutardan fazla olamaz!');
                }
                $newNetSalary = round($payroll->net_salary - $adjustmentAmount, 2);
                $newRemaining = round($payroll->remaining_amount - $adjustmentAmount, 2);
            }
            
            DB::table('payrolls')
                ->where('id', $id)
                ->update([
                    'net_salary' => $newNetSalary,
                    'remaining_amount' => $newRemaining,
                    'notes' => ($payroll->notes ? $payroll->notes . "\n" : '') . 
                              date('d.m.Y H:i') . ' - ' . 
                              ($request->adjustment_type == 'add' ? 'Eklendi' : 'Çıkarıldı') . ': ' . 
                              number_format($adjustmentAmount, 2) . ' TL - ' . 
                              $request->description,
                    'updated_at' => now(),
                ]);
            
            DB::commit();
            
            return back()->with('success', 'Bordro tutarı başarıyla güncellendi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bordro güncellenirken hata oluştu: ' . $e->getMessage());
        }
    }
    
    private function generatePeriodString($type, $date) {
        $carbon = Carbon::parse($date);
        
        return match($type) {
            'daily' => $carbon->format('Y-m-d'),
            'weekly' => $carbon->format('Y') . '-W' . str_pad($carbon->week, 2, '0', STR_PAD_LEFT),
            'monthly' => $carbon->format('Y-m'),
            'hourly' => $carbon->format('Y-m-d'),
            default => $carbon->format('Y-m-d'),
        };
    }
}
