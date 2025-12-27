<?php

namespace App\Http\Controllers;

use App\Services\CustomerDebtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerDebtController extends Controller
{
    protected $debtService;

    public function __construct(CustomerDebtService $debtService)
    {
        $this->debtService = $debtService;
    }

    /**
     * Müşteri borç listesi sayfası
     */
    public function index(Request $request)
    {
        $query = DB::table('customers')
            ->select('customers.id', 'customers.title', 'customers.name', 'customers.code', 'customers.current_balance');

        // Filtreleme
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customers.title', 'like', "%{$search}%")
                  ->orWhere('customers.name', 'like', "%{$search}%")
                  ->orWhere('customers.phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('customers.title')->get();

        // Her müşteri için borç özeti ekle
        foreach ($customers as $customer) {
            $customer->debt_summary = $this->debtService->getCustomerDebtSummary($customer->id);
        }

        return view('financial.customer_debts.index', compact('customers'));
    }

    /**
     * Müşteri borç detayları
     */
    public function show($customerId, Request $request)
    {
        $customer = DB::table('customers')->where('id', $customerId)->first();

        if (!$customer) {
            return redirect()->route('customer-debts.index')
                ->with('error', 'Müşteri bulunamadı.');
        }

        $status = $request->get('status'); // unpaid, partial, paid
        $debts = $this->debtService->getCustomerDebts($customerId, $status);
        $summary = $this->debtService->getCustomerDebtSummary($customerId);

        // Kasa hesapları (cash accounts) - Tüm kasalar
        $cashAccounts = DB::table('accounts')
            ->where('is_active', true)
            ->where(function($q) {
                $q->where('type', 'cash')
                  ->orWhere('name', 'like', '%kasa%')
                  ->orWhere('name', 'like', '%Kasa%')
                  ->orWhere('name', 'like', '%KASA%')
                  ->orWhere('name', 'like', '%cash%')
                  ->orWhere('name', 'like', '%Cash%')
                  ->orWhere('name', 'like', '%CASH%');
            })
            ->orderBy('name')
            ->get();

        // Eğer kasa hesabı yoksa, tüm aktif hesapları getir
        if ($cashAccounts->isEmpty()) {
            $cashAccounts = DB::table('accounts')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        // Ödeme geçmişi
        $paymentHistory = DB::table('customer_debt_payments')
            ->where('customer_id', $customerId)
            ->where('status', 'completed')
            ->orderBy('payment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('financial.customer_debts.show', compact(
            'customer',
            'debts',
            'summary',
            'cashAccounts',
            'paymentHistory',
            'status'
        ));
    }

    /**
     * Kasa üzerinden borç ödemesi yap
     */
    public function payDebt(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'reference_id' => 'required|exists:expenses,id',
            'account_id' => 'required|exists:accounts,id',
            'paid_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,transfer',
            'payment_date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $result = $this->debtService->payDebtWithCash($request->all());

        return redirect()->back()
            ->with('success', $result['message']);
    }

    /**
     * Borç ödemesini iptal et
     */
    public function cancelPayment($paymentId)
    {
        $debtPayment = DB::table('customer_debt_payments')
            ->where('id', $paymentId)
            ->first();

        if (!$debtPayment) {
            return redirect()->back()
                ->with('error', 'Borç ödemesi bulunamadı.');
        }

        $this->debtService->cancelDebtPayment($paymentId);

        return redirect()->back()
            ->with('success', 'Borç ödemesi başarıyla iptal edildi.');
    }

    /**
     * AJAX: Müşteri borç bilgilerini getir
     */
    public function getDebtInfo($expenseId)
    {
        $expense = DB::table('expenses')
            ->where('id', $expenseId)
            ->first();

        if (!$expense || !$expense->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'Borç kaydı bulunamadı.',
            ], 404);
        }

        // Ödenen tutarı hesapla
        $totalPaid = DB::table('customer_debt_payments')
            ->where('customer_id', $expense->customer_id)
            ->where('reference_id', $expenseId)
            ->where('reference_type', 'expense')
            ->where('status', 'completed')
            ->sum('paid_amount') ?? 0;

        $remaining = $expense->amount - $totalPaid;

        return response()->json([
            'success' => true,
            'data' => [
                'expense_id' => $expense->id,
                'expense_number' => $expense->expense_number,
                'debt_amount' => $expense->amount,
                'paid_amount' => $totalPaid,
                'remaining_amount' => $remaining,
                'debt_date' => $expense->date,
                'description' => $expense->description,
            ],
        ]);
    }

    /**
     * AJAX: Müşteri borç özeti
     */
    public function getDebtSummary($customerId)
    {
        $summary = $this->debtService->getCustomerDebtSummary($customerId);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }
}

