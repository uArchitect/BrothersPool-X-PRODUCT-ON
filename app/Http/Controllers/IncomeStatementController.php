<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncomeStatementController extends Controller
{
    /**
     * Gelir Tablosu ana sayfası
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Gelir hesaplamaları
        $revenues = $this->calculateRevenues($startDate, $endDate);
        
        // Gider hesaplamaları
        $expenses = $this->calculateExpenses($startDate, $endDate);
        
        // Net kar hesaplama
        $totalRevenue = $revenues->sum('amount');
        $totalExpense = $expenses->sum('amount');
        $netProfit = $totalRevenue - $totalExpense;

        return view('financial.income-statement', compact(
            'revenues', 
            'expenses', 
            'totalRevenue', 
            'totalExpense', 
            'netProfit',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Gelir hesaplamaları
     */
    protected function calculateRevenues($startDate, $endDate)
    {
        // Satış gelirleri (sales tablosundan)
        $salesRevenue = DB::table('sales')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total') ?? 0;

        // Sipariş gelirleri (orders tablosundan)
        $orderRevenue = 0;
        try {
            $orderRevenue = DB::table('orders')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('total_amount') ?? 0;
        } catch (\Exception $e) {
            // Tablo yoksa veya hata varsa 0 döndür
        }

        // Cari hesap gelirleri
        $accountRevenue = 0;
        try {
            $accountRevenue = DB::table('customers_account_transactions')
                ->where('type', 'Gelir')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('amount') ?? 0;
        } catch (\Exception $e) {
            // Tablo yoksa veya hata varsa 0 döndür
        }

        // Kasa gelirleri (transactions tablosu varsa)
        $cashRevenue = 0;
        try {
            $cashRevenue = DB::table('transactions')
                ->where('type', 'income')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount') ?? 0;
        } catch (\Exception $e) {
            // Tablo yoksa veya hata varsa 0 döndür
        }

        return collect([
            [
                'category' => 'Satış Gelirleri',
                'subcategory' => 'Restoran Satışları',
                'amount' => $salesRevenue,
                'percentage' => 0
            ],
            [
                'category' => 'Sipariş Gelirleri',
                'subcategory' => 'Tamamlanan Siparişler',
                'amount' => $orderRevenue,
                'percentage' => 0
            ],
            [
                'category' => 'Cari Hesap Gelirleri',
                'subcategory' => 'Müşteri Hesap Gelirleri',
                'amount' => $accountRevenue,
                'percentage' => 0
            ],
            [
                'category' => 'Kasa Gelirleri',
                'subcategory' => 'Genel Kasa Gelirleri',
                'amount' => $cashRevenue,
                'percentage' => 0
            ]
        ])->filter(function($item) {
            return $item['amount'] > 0;
        });
    }

    /**
     * Gider hesaplamaları
     */
    protected function calculateExpenses($startDate, $endDate)
    {
        $expenses = collect();

        // Expenses tablosundan giderler
        try {
            $expenseData = DB::table('expenses')
                ->whereBetween('date', [$startDate, $endDate])
                ->select('description', DB::raw('SUM(total) as amount'))
                ->groupBy('description')
                ->get();

            foreach ($expenseData as $expense) {
                $expenses->push([
                    'category' => 'Genel Giderler',
                    'subcategory' => $expense->description ?? 'Belirtilmemiş',
                    'amount' => $expense->amount,
                    'percentage' => 0
                ]);
            }
        } catch (\Exception $e) {
            // Tablo yoksa veya hata varsa devam et
        }

        // Cari hesap giderleri
        try {
            $accountExpenses = DB::table('customers_account_transactions')
                ->where('type', 'Gider')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('amount') ?? 0;

            if ($accountExpenses > 0) {
                $expenses->push([
                    'category' => 'Cari Hesap Giderleri',
                    'subcategory' => 'Müşteri Hesap Giderleri',
                    'amount' => $accountExpenses,
                    'percentage' => 0
                ]);
            }
        } catch (\Exception $e) {
            // Tablo yoksa veya hata varsa devam et
        }

        // Kasa giderleri
        try {
            $cashExpenses = DB::table('transactions')
                ->where('type', 'expense')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount') ?? 0;

            if ($cashExpenses > 0) {
                $expenses->push([
                    'category' => 'Kasa Giderleri',
                    'subcategory' => 'Genel Kasa Giderleri',
                    'amount' => $cashExpenses,
                    'percentage' => 0
                ]);
            }
        } catch (\Exception $e) {
            // Tablo yoksa veya hata varsa devam et
        }

        return $expenses;
    }

    /**
     * Gelir tablosu PDF export
     */
    public function exportPDF(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $revenues = $this->calculateRevenues($startDate, $endDate);
        $expenses = $this->calculateExpenses($startDate, $endDate);
        
        $totalRevenue = $revenues->sum('amount');
        $totalExpense = $expenses->sum('amount');
        $netProfit = $totalRevenue - $totalExpense;

        // PDF oluşturma (şimdilik JSON response)
        return response()->json([
            'message' => 'PDF oluşturma özelliği yakında eklenecek',
            'data' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_revenue' => $totalRevenue,
                'total_expense' => $totalExpense,
                'net_profit' => $netProfit
            ]
        ]);
    }

    /**
     * Gelir tablosu Excel export
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $revenues = $this->calculateRevenues($startDate, $endDate);
        $expenses = $this->calculateExpenses($startDate, $endDate);
        
        $totalRevenue = $revenues->sum('amount');
        $totalExpense = $expenses->sum('amount');
        $netProfit = $totalRevenue - $totalExpense;

        // Excel oluşturma (şimdilik JSON response)
        return response()->json([
            'message' => 'Excel oluşturma özelliği yakında eklenecek',
            'data' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_revenue' => $totalRevenue,
                'total_expense' => $totalExpense,
                'net_profit' => $netProfit
            ]
        ]);
    }
}
