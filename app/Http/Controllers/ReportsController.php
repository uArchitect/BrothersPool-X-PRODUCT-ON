<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ReportsController extends Controller
{
    private $data = [];

    public function __construct()
    {
        // Authorization middleware should be applied at route level
        // $this->middleware('auth');
        // $this->middleware('permission:view-reports');
        $this->loadCommonData();
    }

    /**
     * Load common data needed by views
     */
    protected function loadCommonData()
    {
        $this->data = [];
    }

    /**
     * Validate date range parameters
     */
    private function validateDateRange($startDate, $endDate)
    {
        try {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            
            if ($start->gt($end)) {
                throw new ValidationException('Başlangıç tarihi bitiş tarihinden sonra olamaz.');
            }
            
            // Prevent very large date ranges that could cause performance issues
            if ($start->diffInDays($end) > 365) {
                throw new ValidationException('Tarih aralığı en fazla 365 gün olabilir.');
            }
            
            return true;
        } catch (\Exception $e) {
            throw new ValidationException('Geçersiz tarih formatı.');
        }
    }

    /**
     * Sanitize and validate report type
     */
    private function validateReportType($type)
    {
        $allowedTypes = [
            'daily_reservations', 'reservation_performance', 'revenue_analysis',
            'payment_summary', 'profit_loss', 'customer_insights', 'customer_loyalty',
            'customer_retention', 'service_performance', 'popular_services',
            'employee_performance', 'commission_report', 'monthly_sales',
            'payments_list', 'capacity_utilization', 'inventory_status',
            'data_verification', 'business_insights', 'customer_list',
            'employee_list', 'services_list'
        ];
        
        if (!in_array($type, $allowedTypes)) {
            throw new ValidationException('Geçersiz rapor türü.');
        }
        
        return true;
    }

    public function getReportData($type, Request $request)
    {
        try {
            // Validate report type
            $this->validateReportType($type);
            
            // Validate and sanitize date inputs
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
            
            $this->validateDateRange($startDate, $endDate);

            switch ($type) {
                // Reservation Reports
                case 'daily_reservations':
                    return $this->getDailyReservations($startDate, $endDate);
                case 'reservation_performance':
                    return $this->getReservationPerformance($startDate, $endDate);
                
                // Financial Reports
                case 'revenue_analysis':
                    return $this->getRevenueAnalysis($startDate, $endDate);
                case 'payment_summary':
                    return $this->getPaymentSummary($startDate, $endDate);
                case 'profit_loss':
                    return $this->getProfitLossReport($startDate, $endDate);
                
                // Customer Analytics
                case 'customer_insights':
                    return $this->getCustomerInsights($startDate, $endDate);
                case 'customer_loyalty':
                    return $this->getCustomerLoyalty();
                case 'customer_retention':
                    return $this->getCustomerRetention($startDate, $endDate);
                
                // Service Analytics
                case 'service_performance':
                    return $this->getServicePerformance($startDate, $endDate);
                case 'popular_services':
                    return $this->getPopularServices($startDate, $endDate);
                
                // Employee Reports
                case 'employee_performance':
                    return $this->getEmployeePerformance($startDate, $endDate);
                case 'commission_report':
                    return $this->getCommissionReport($startDate, $endDate);
                
                // Financial Reports
                case 'monthly_sales':
                    return $this->getMonthlySales($startDate, $endDate);
                case 'payments_list':
                    return $this->getPaymentsList($startDate, $endDate);
                
                // Operational Reports
                case 'capacity_utilization':
                    return $this->getCapacityUtilization($startDate, $endDate);
                case 'inventory_status':
                    return $this->getInventoryStatus();
                
                // Data Validation & Business Intelligence
                case 'data_verification':
                    return $this->verifyDataRelationships();
                case 'business_insights':
                    return $this->getBusinessInsights($startDate, $endDate);
                
                // Basic Lists (Legacy support)
                case 'customer_list':
                    return $this->getCustomerList();
                case 'employee_list':
                    return $this->getEmployeeList();
                case 'services_list':
                    return $this->getServicesList();
                
                default:
                    return response()->json([
                        'success' => false,
                        'error' => 'Geçersiz rapor türü'
                    ], 400);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Report generation error: ' . $e->getMessage(), [
                'type' => $type,
                'user_id' => Auth::id() ?? 'guest',
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Rapor oluşturulurken bir hata oluştu.'
            ], 500);
        }
    }

    // RESERVATION REPORTS
    private function getDailyReservations($startDate, $endDate)
    {
        // Add pagination support for large datasets
        $query = DB::table('reservations')
            ->leftJoin('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('employees', 'reservations.employee_id', '=', 'employees.id')
            ->leftJoin('tables', 'reservations.table_id', '=', 'tables.id')
            ->select(
                'reservations.id as "Rezervasyon ID"',
                DB::raw('DATE(reservations.start_date) as Tarih'),
                DB::raw('TIME(reservations.start_date) as "Başlangıç Saati"'),
                DB::raw('TIME(reservations.end_date) as "Bitiş Saati"'),
                DB::raw('COALESCE(customers.name, CONCAT(COALESCE(customers.first_name, ""), " ", COALESCE(customers.last_name, "")), "Müşteri Belirtilmemiş") as Müşteri'),
                DB::raw('COALESCE(employees.name, "Personel Belirtilmemiş") as Personel'),
                DB::raw('COALESCE(tables.name, "Oda Belirtilmemiş") as Oda'),
                DB::raw('COALESCE(CAST(reservations.total_price AS DECIMAL(10,2)), 0.00) as "Tutar (₺)"'),
                DB::raw('CASE 
                    WHEN reservations.status = "pending" THEN "Bekliyor"
                    WHEN reservations.status = "completed" THEN "Tamamlandı"
                    WHEN reservations.status = "cancelled" THEN "İptal"
                    WHEN reservations.status = "started" THEN "Başlatıldı"
                    ELSE COALESCE(reservations.status, "Bilinmiyor")
                END as Durum')
            )
            ->whereBetween(DB::raw('DATE(reservations.start_date)'), [$startDate, $endDate]);

        // Add limit for performance on large datasets
        $reservations = $query->orderBy('reservations.start_date', 'desc')
            ->limit(1000) // Prevent memory issues
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reservations,
            'title' => 'Günlük Rezervasyonlar (' . $startDate . ' - ' . $endDate . ')',
            'total_records' => $reservations->count(),
            'metadata' => [
                'generated_at' => now()->toISOString(),
                'date_range' => ['start' => $startDate, 'end' => $endDate],
                'limited_to' => 1000
            ],
            'summary' => [
                'Toplam Tutar' => (float) $reservations->sum('Tutar (₺)'),
                'Tamamlanan' => $reservations->where('Durum', 'Tamamlandı')->count(),
                'Bekleyen' => $reservations->where('Durum', 'Bekliyor')->count(),
                'İptal Edilen' => $reservations->where('Durum', 'İptal')->count()
            ]
        ]);
    }

    private function getReservationPerformance($startDate, $endDate)
    {
        $performance = DB::table('reservations')
            ->select(
                DB::raw('DATE(start_date) as Tarih'),
                DB::raw('COUNT(*) as "Toplam Rezervasyon"'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as "Tamamlanan"'),
                DB::raw('SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as "İptal Edilen"'),
                DB::raw('ROUND(SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 1) as "Başarı Oranı %"'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN COALESCE(CAST(total_price AS DECIMAL(10,2)), 0) ELSE 0 END) as "Gelir (₺)"')
            )
            ->whereBetween(DB::raw('DATE(start_date)'), [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(start_date)'))
            ->orderBy('Tarih', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $performance,
            'title' => 'Rezervasyon Performans Analizi (' . $startDate . ' - ' . $endDate . ')',
            'total_records' => $performance->count()
        ]);
    }

    // FINANCIAL REPORTS
    private function getRevenueAnalysis($startDate, $endDate)
    {
        $revenueData = DB::table('sales')
            ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->leftJoin('employees', 'sales.seller_id', '=', 'employees.id')
            ->select(
                DB::raw('DATE(sales.date) as Tarih'),
                DB::raw('COUNT(sales.id) as "Satış Sayısı"'),
                DB::raw('SUM(COALESCE(sales.total, 0)) as "Brüt Gelir (₺)"'),
                DB::raw('SUM(COALESCE(sales.total_discount, 0)) as "Toplam İndirim (₺)"'),
                DB::raw('SUM(COALESCE(sales.total_tax, 0)) as "Toplam Vergi (₺)"'),
                DB::raw('SUM(COALESCE(sales.grand_total, 0)) as "Net Gelir (₺)"'),
                DB::raw('SUM(COALESCE(sales.paid, 0)) as "Tahsilat (₺)"'),
                DB::raw('SUM(COALESCE(sales.grand_total, 0) - COALESCE(sales.paid, 0)) as "Alacak (₺)"'),
                DB::raw('ROUND(AVG(COALESCE(sales.grand_total, 0)), 2) as "Ortalama Satış (₺)"')
            )
            ->whereBetween('sales.date', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(sales.date)'))
            ->orderBy('Tarih', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $revenueData,
            'title' => 'Gelir Analizi (' . $startDate . ' - ' . $endDate . ')',
            'total_records' => $revenueData->count(),
            'summary' => [
                'Toplam Brüt Gelir' => $revenueData->sum('Brüt Gelir (₺)'),
                'Toplam Net Gelir' => $revenueData->sum('Net Gelir (₺)'),
                'Toplam Tahsilat' => $revenueData->sum('Tahsilat (₺)'),
                'Toplam Alacak' => $revenueData->sum('Alacak (₺)')
            ]
        ]);
    }

    private function getPaymentSummary($startDate, $endDate)
    {
        $payments = DB::table('payments')
            ->leftJoin('customers', 'payments.customer_id', '=', 'customers.id')
            ->leftJoin('reservations', 'payments.reservation_id', '=', 'reservations.id')
            ->select(
                DB::raw('DATE(payments.created_at) as Tarih'),
                DB::raw('COALESCE(payments.payment_method, "Belirtilmemiş") as "Ödeme Yöntemi"'),
                DB::raw('COUNT(*) as "İşlem Sayısı"'),
                DB::raw('SUM(COALESCE(payments.payment_amount, 0)) as "Toplam Tutar (₺)"'),
                DB::raw('CASE 
                    WHEN payments.payment_status = 1 THEN "Ödendi"
                    WHEN payments.payment_status = 2 THEN "İptal"
                    WHEN payments.payment_status = 3 THEN "Fazla Ödeme"
                    WHEN payments.payment_status = 4 THEN "Eksik Ödeme"
                    ELSE "Ödenmedi"
                END as Durum')
            )
            ->whereBetween(DB::raw('DATE(payments.created_at)'), [$startDate, $endDate])
            ->groupBy(
                DB::raw('DATE(payments.created_at)'),
                'payments.payment_method',
                'payments.payment_status'
            )
            ->orderBy('Tarih', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $payments,
            'title' => 'Ödeme Özeti (' . $startDate . ' - ' . $endDate . ')',
            'total_records' => $payments->count()
        ]);
    }

    private function getProfitLossReport($startDate, $endDate)
    {
        // Gelirler
        $revenue = DB::table('sales')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('SUM(COALESCE(grand_total, 0)) as total_revenue')
            ->value('total_revenue') ?? 0;

        // Giderler
        $expenses = DB::table('expenses')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('SUM(COALESCE(total, 0)) as total_expenses')
            ->value('total_expenses') ?? 0;

        // Komisyonlar
        $commissions = DB::table('employee_commissions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 1)
            ->selectRaw('SUM(COALESCE(amount, 0)) as total_commissions')
            ->value('total_commissions') ?? 0;

        $netProfit = $revenue - $expenses - $commissions;
        $profitMargin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 2) : 0;

        $data = collect([
            [
                'Kategori' => 'Gelir',
                'Tutar (₺)' => number_format($revenue, 2),
                'Yüzde' => '100%',
                'Tip' => 'Pozitif'
            ],
            [
                'Kategori' => 'Giderler',
                'Tutar (₺)' => number_format($expenses, 2),
                'Yüzde' => $revenue > 0 ? round(($expenses / $revenue) * 100, 2) . '%' : '0%',
                'Tip' => 'Negatif'
            ],
            [
                'Kategori' => 'Komisyonlar',
                'Tutar (₺)' => number_format($commissions, 2),
                'Yüzde' => $revenue > 0 ? round(($commissions / $revenue) * 100, 2) . '%' : '0%',
                'Tip' => 'Negatif'
            ],
            [
                'Kategori' => 'Net Kar',
                'Tutar (₺)' => number_format($netProfit, 2),
                'Yüzde' => $profitMargin . '%',
                'Tip' => $netProfit >= 0 ? 'Pozitif' : 'Negatif'
            ]
        ]);

        return response()->json([
            'success' => true,
            'data' => $data,
            'title' => 'Kar-Zarar Raporu (' . $startDate . ' - ' . $endDate . ')',
            'total_records' => $data->count()
        ]);
    }

    private function getMonthlySales($startDate, $endDate)
    {
        $sales = DB::table('sales')
            ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->select(
                'sales.id as ID',
                'sales.date as Tarih',
                DB::raw('COALESCE(sales.total, 0.00) as Toplam'),
                DB::raw('COALESCE(sales.grand_total, 0.00) as "Genel Toplam"'),
                DB::raw('COALESCE(sales.paid, 0.00) as Ödenen'),
                DB::raw('COALESCE(customers.name, CONCAT(COALESCE(customers.first_name, ""), " ", COALESCE(customers.last_name, "")), "Müşteri Belirtilmemiş") as Müşteri'),
                DB::raw('COALESCE(sales.payment_method, "Belirtilmemiş") as "Ödeme Yöntemi"')
            )
            ->whereBetween('sales.date', [$startDate, $endDate])
            ->orderBy('sales.date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sales,
            'title' => 'Aylık Satışlar (' . $startDate . ' - ' . $endDate . ')',
            'total_records' => $sales->count()
        ]);
    }

    private function getPaymentsList($startDate, $endDate)
    {
        $payments = DB::table('payments')
            ->leftJoin('customers', 'payments.customer_id', '=', 'customers.id')
            ->leftJoin('reservations', 'payments.reservation_id', '=', 'reservations.id')
            ->select(
                'payments.id as ID',
                DB::raw('COALESCE(payments.payment_amount, 0.00) as Tutar'),
                DB::raw('COALESCE(payments.payment_method, "Belirtilmemiş") as "Ödeme Yöntemi"'),
                DB::raw('CASE 
                    WHEN payments.payment_status = 1 THEN "Ödendi"
                    WHEN payments.payment_status = 2 THEN "İptal"
                    WHEN payments.payment_status = 3 THEN "Fazla Ödeme"
                    WHEN payments.payment_status = 4 THEN "Eksik Ödeme"
                    ELSE "Ödenmedi"
                END as Durum'),
                DB::raw('DATE(payments.created_at) as Tarih'),
                DB::raw('COALESCE(customers.name, CONCAT(COALESCE(customers.first_name, ""), " ", COALESCE(customers.last_name, "")), "Müşteri Belirtilmemiş") as Müşteri'),
                DB::raw('COALESCE(reservations.id, "Rezervasyon Yok") as "Rezervasyon ID"')
            )
            ->whereBetween(DB::raw('DATE(payments.created_at)'), [$startDate, $endDate])
            ->orderBy('payments.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $payments,
            'title' => 'Ödeme Listesi (' . $startDate . ' - ' . $endDate . ')',
            'total_records' => $payments->count()
        ]);
    }

    // CUSTOMER ANALYTICS
    private function getCustomerInsights($startDate, $endDate)
    {
        $insights = DB::table('customers')
            ->leftJoin('reservations', 'customers.id', '=', 'reservations.customer_id')
            ->leftJoin('payments', 'reservations.id', '=', 'payments.reservation_id')
            ->select(
                DB::raw('COALESCE(customers.name, CONCAT(COALESCE(customers.first_name, ""), " ", COALESCE(customers.last_name, "")), "Müşteri Adı") as "Müşteri Adı"'),
                'customers.phone as Telefon',
                'customers.email as Email',
                DB::raw('COUNT(DISTINCT reservations.id) as "Rezervasyon Sayısı"'),
                DB::raw('SUM(CASE WHEN reservations.status = "completed" THEN 1 ELSE 0 END) as "Tamamlanan"'),
                DB::raw('SUM(COALESCE(payments.payment_amount, 0)) as "Toplam Harcama (₺)"'),
                DB::raw('COALESCE(MAX(reservations.start_date), "Hiç rezervasyon yok") as "Son Rezervasyon"'),
                DB::raw('CASE WHEN customers.is_vip = 1 THEN "VIP" ELSE "Normal" END as "Müşteri Tipi"'),
                DB::raw('COALESCE(customers.total_visits, 0) as "Toplam Ziyaret"')
            )
            ->whereExists(function($query) use ($startDate, $endDate) {
                $query->select(DB::raw(1))
                      ->from('reservations as r2')
                      ->whereColumn('r2.customer_id', 'customers.id')
                      ->whereBetween(DB::raw('DATE(r2.start_date)'), [$startDate, $endDate]);
            })
            ->groupBy(
                'customers.id',
                'customers.name as first_name',
                'customers.last_name',
                'customers.phone',
                'customers.email',
                'customers.is_vip',
                'customers.total_visits'
            )
            ->orderBy('Toplam Harcama (₺)', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $insights,
            'title' => 'Müşteri Analizi (' . $startDate . ' - ' . $endDate . ')',
            'total_records' => $insights->count()
        ]);
    }

    private function getCustomerLoyalty()
    {
        $loyalty = DB::table('customers')
            ->select(
                DB::raw('CONCAT(first_name, " ", last_name) as "Müşteri Adı"'),
                'total_visits as "Toplam Ziyaret"',
                DB::raw('COALESCE(total_spent, 0) as "Toplam Harcama (₺)"'),
                'last_visit as "Son Ziyaret"',
                DB::raw('CASE WHEN is_vip = 1 THEN "VIP" ELSE "Normal" END as "Durum"'),
                DB::raw('CASE 
                    WHEN total_visits >= 20 THEN "Platin"
                    WHEN total_visits >= 10 THEN "Altın"
                    WHEN total_visits >= 5 THEN "Gümüş"
                    ELSE "Bronz"
                END as "Sadakat Seviyesi"'),
                DB::raw('DATEDIFF(CURDATE(), last_visit) as "Son Ziyaretten Gün"')
            )
            ->where('total_visits', '>', 0)
            ->orderBy('total_spent', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $loyalty,
            'title' => 'Müşteri Sadakat Analizi',
            'total_records' => $loyalty->count()
        ]);
    }

    private function getCustomerRetention($startDate, $endDate)
    {
        $retention = DB::table('customers')
            ->select(
                DB::raw('CONCAT(first_name, " ", last_name) as "Müşteri Adı"'),
                'phone as Telefon',
                DB::raw('COALESCE(total_visits, 0) as "Ziyaret Sayısı"'),
                DB::raw('COALESCE(last_visit, "Hiç ziyaret yok") as "Son Ziyaret"'),
                DB::raw('DATEDIFF(CURDATE(), COALESCE(last_visit, created_at)) as "Gün Farkı"'),
                DB::raw('CASE 
                    WHEN DATEDIFF(CURDATE(), COALESCE(last_visit, created_at)) <= 30 THEN "Aktif"
                    WHEN DATEDIFF(CURDATE(), COALESCE(last_visit, created_at)) <= 90 THEN "Düşük Risk"
                    WHEN DATEDIFF(CURDATE(), COALESCE(last_visit, created_at)) <= 180 THEN "Orta Risk" 
                    ELSE "Yüksek Risk"
                END as "Risk Durumu"'),
                DB::raw('COALESCE(total_spent, 0) as "Toplam Harcama (₺)"')
            )
            ->orderBy('Gün Farkı', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $retention,
            'title' => 'Müşteri Tutma Analizi',
            'total_records' => $retention->count(),
            'summary' => [
                'Aktif' => $retention->where('Risk Durumu', 'Aktif')->count(),
                'Düşük Risk' => $retention->where('Risk Durumu', 'Düşük Risk')->count(),
                'Orta Risk' => $retention->where('Risk Durumu', 'Orta Risk')->count(),
                'Yüksek Risk' => $retention->where('Risk Durumu', 'Yüksek Risk')->count()
            ]
        ]);
    }

    // SERVICE ANALYTICS
    private function getServicePerformance($startDate, $endDate)
    {
        $performance = DB::table('menu_items')
            ->leftJoin('reservations_items', 'services.id', '=', 'reservations_items.service_id')
            ->leftJoin('reservations', 'reservations_items.reservation_id', '=', 'reservations.id')
            ->leftJoin('categories', 'services.category_id', '=', 'categories.id')
            ->select(
                'services.name as "Hizmet Adı"',
                DB::raw('COALESCE(categories.name, "Kategori Yok") as Kategori'),
                DB::raw('services.price as "Liste Fiyatı (₺)"'),
                DB::raw('COUNT(reservations_items.id) as "Satış Adedi"'),
                DB::raw('SUM(COALESCE(reservations_items.price, 0)) as "Toplam Gelir (₺)"'),
                DB::raw('ROUND(AVG(COALESCE(reservations_items.price, 0)), 2) as "Ortalama Fiyat (₺)"'),
                DB::raw('SUM(CASE WHEN reservations.status = "completed" THEN 1 ELSE 0 END) as "Tamamlanan"'),
                DB::raw('ROUND(SUM(CASE WHEN reservations.status = "completed" THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(reservations_items.id), 0), 1) as "Başarı Oranı %"')
            )
            ->whereBetween(DB::raw('DATE(reservations.start_date)'), [$startDate, $endDate])
            ->groupBy('services.id', 'services.name', 'services.price', 'categories.name')
            ->having('Satış Adedi', '>', 0)
            ->orderBy('Toplam Gelir (₺)', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $performance,
            'title' => 'Hizmet Performans Analizi (' . $startDate . ' - ' . $endDate . ')',
            'total_records' => $performance->count()
        ]);
    }

    private function getPopularServices($startDate, $endDate)
    {
        $popular = DB::table('menu_items')
            ->leftJoin('reservations_items', 'services.id', '=', 'reservations_items.service_id')
            ->leftJoin('reservations', 'reservations_items.reservation_id', '=', 'reservations.id')
            ->leftJoin('categories', 'services.category_id', '=', 'categories.id')
            ->select(
                'services.name as "Hizmet Adı"',
                DB::raw('COALESCE(categories.name, "Kategori Yok") as Kategori'),
                DB::raw('COUNT(reservations_items.id) as "Satış Adedi"'),
                DB::raw('SUM(COALESCE(reservations_items.price, 0)) as "Toplam Gelir (₺)"'),
                DB::raw('ROUND(AVG(COALESCE(reservations_items.price, 0)), 2) as "Ortalama Fiyat (₺)"'),
                DB::raw('services.price as "Liste Fiyatı (₺)"'),
                DB::raw('ROUND(COUNT(reservations_items.id) * 100.0 / (SELECT COUNT(*) FROM reservations_items ri2 JOIN reservations r2 ON ri2.reservation_id = r2.id WHERE DATE(r2.start_date) BETWEEN "' . $startDate . '" AND "' . $endDate . '"), 2) as "Pazar Payı %"')
            )
            ->whereBetween(DB::raw('DATE(reservations.start_date)'), [$startDate, $endDate])
            ->groupBy('services.id', 'services.name', 'services.price', 'categories.name')
            ->having('Satış Adedi', '>', 0)
            ->orderBy('Satış Adedi', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $popular,
            'title' => 'En Popüler Hizmetler (' . $startDate . ' - ' . $endDate . ')',
            'total_records' => $popular->count()
        ]);
    }

    // EMPLOYEE REPORTS
    private function getEmployeePerformance($startDate, $endDate)
    {
        $performance = DB::table('employees')
            ->leftJoin('reservations', 'employees.id', '=', 'reservations.employee_id')
            ->leftJoin('payments', 'reservations.id', '=', 'payments.reservation_id')
            ->leftJoin('employee_commissions', function($join) use ($startDate, $endDate) {
                $join->on('employees.id', '=', 'employee_commissions.employee_id')
                     ->whereBetween('employee_commissions.created_at', [$startDate, $endDate]);
            })
            ->select(
                'employees.name as "Personel Adı"',
                'employees.position as Pozisyon',
                DB::raw('COUNT(DISTINCT reservations.id) as "Rezervasyon Sayısı"'),
                DB::raw('SUM(CASE WHEN reservations.status = "completed" THEN 1 ELSE 0 END) as "Tamamlanan"'),
                DB::raw('ROUND(SUM(CASE WHEN reservations.status = "completed" THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(DISTINCT reservations.id), 0), 1) as "Başarı Oranı %"'),
                DB::raw('SUM(COALESCE(payments.payment_amount, 0)) as "Toplam Satış (₺)"'),
                DB::raw('SUM(COALESCE(employee_commissions.amount, 0)) as "Toplam Komisyon (₺)"'),
                DB::raw('ROUND(AVG(COALESCE(payments.payment_amount, 0)), 2) as "Ortalama Satış (₺)"'),
                DB::raw('CASE WHEN employees.is_active = 1 THEN "Aktif" ELSE "Pasif" END as Durum')
            )
            ->whereExists(function($query) use ($startDate, $endDate) {
                $query->select(DB::raw(1))
                      ->from('reservations as r2')
                      ->whereColumn('r2.employee_id', 'employees.id')
                      ->whereBetween(DB::raw('DATE(r2.start_date)'), [$startDate, $endDate]);
            })
            ->groupBy('employees.id', 'employees.name', 'employees.position', 'employees.is_active')
            ->orderBy('Toplam Satış (₺)', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $performance,
            'title' => 'Personel Performans Analizi (' . $startDate . ' - ' . $endDate . ')',
            'total_records' => $performance->count()
        ]);
    }

    private function getCommissionReport($startDate, $endDate)
    {
        $commissions = DB::table('employee_commissions')
            ->leftJoin('employees', 'employee_commissions.employee_id', '=', 'employees.id')
            ->leftJoin('reservations', 'employee_commissions.reservation_id', '=', 'reservations.id')
            ->leftJoin('menu_items', 'employee_commissions.service_id', '=', 'services.id')
            ->select(
                'employees.name as "Personel Adı"',
                'services.name as "Hizmet"',
                DB::raw('DATE(employee_commissions.created_at) as Tarih'),
                DB::raw('COALESCE(employee_commissions.amount, 0) as "Komisyon Tutarı (₺)"'),
                DB::raw('CASE WHEN employee_commissions.status = 1 THEN "Ödendi" ELSE "Ödenmedi" END as "Ödeme Durumu"'),
                'reservations.id as "Rezervasyon ID"'
            )
            ->whereBetween(DB::raw('DATE(employee_commissions.created_at)'), [$startDate, $endDate])
            ->orderBy('employee_commissions.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $commissions,
            'title' => 'Komisyon Raporu (' . $startDate . ' - ' . $endDate . ')',
            'total_records' => $commissions->count(),
            'summary' => [
                'Toplam Komisyon' => $commissions->sum('Komisyon Tutarı (₺)'),
                'Ödenen Komisyon' => $commissions->where('Ödeme Durumu', 'Ödendi')->sum('Komisyon Tutarı (₺)'),
                'Ödenmemiş Komisyon' => $commissions->where('Ödeme Durumu', 'Ödenmedi')->sum('Komisyon Tutarı (₺)')
            ]
        ]);
    }

    // OPERATIONAL REPORTS
    private function getCapacityUtilization($startDate, $endDate)
    {
        $utilization = DB::table('tables')
            ->leftJoin('reservations', 'tables.id', '=', 'reservations.table_id')
            ->leftJoin('employees', 'tables.employee_id', '=', 'employees.id')
            ->select(
                'tables.name as "Oda/Masa"',
                'tables.capacity as Kapasite',
                DB::raw('COALESCE(employees.name, "Atanmamış") as "Sorumlu Personel"'),
                DB::raw('COUNT(reservations.id) as "Toplam Rezervasyon"'),
                DB::raw('SUM(CASE WHEN reservations.status = "completed" THEN 1 ELSE 0 END) as "Tamamlanan"'),
                DB::raw('SUM(CASE WHEN reservations.status = "cancelled" THEN 1 ELSE 0 END) as "İptal"'),
                DB::raw('ROUND(COUNT(reservations.id) * 100.0 / ((DATEDIFF("' . $endDate . '", "' . $startDate . '") + 1) * 8), 1) as "Kullanım Oranı %"')
            )
            ->whereBetween(DB::raw('DATE(reservations.start_date)'), [$startDate, $endDate])
            ->groupBy('tables.id', 'tables.name', 'tables.capacity', 'employees.name')
            ->orderBy('Kullanım Oranı %', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $utilization,
            'title' => 'Kapasite Kullanım Analizi (' . $startDate . ' - ' . $endDate . ')',
            'total_records' => $utilization->count()
        ]);
    }

    private function getInventoryStatus()
    {
        $inventory = DB::table('menu_items')
            ->leftJoin('categories', 'services.category_id', '=', 'categories.id')
            ->leftJoin('warehouses', 'services.warehouse_id', '=', 'warehouses.id')
            ->select(
                'services.name as "Ürün/Hizmet"',
                DB::raw('COALESCE(categories.name, "Kategori Yok") as Kategori'),
                DB::raw('COALESCE(warehouses.name, "Depo Belirtilmemiş") as Depo'),
                DB::raw('CASE WHEN services.is_stock = 1 THEN "Stoklu" ELSE "Hizmet" END as Tip'),
                DB::raw('COALESCE(services.stock, 0) as "Mevcut Stok"'),
                DB::raw('services.price as "Birim Fiyat (₺)"'),
                DB::raw('CASE 
                    WHEN services.is_stock = 1 AND COALESCE(services.stock, 0) <= 5 THEN "Kritik"
                    WHEN services.is_stock = 1 AND COALESCE(services.stock, 0) <= 10 THEN "Düşük"
                    WHEN services.is_stock = 1 THEN "Normal"
                    ELSE "Hizmet"
                END as "Stok Durumu"'),
                DB::raw('CASE WHEN services.is_active = 1 THEN "Aktif" ELSE "Pasif" END as Durum')
            )
            ->orderBy('services.is_stock', 'desc')
            ->orderBy('services.stock', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $inventory,
            'title' => 'Envanter Durumu',
            'total_records' => $inventory->count(),
            'summary' => [
                'Kritik Stok' => $inventory->where('Stok Durumu', 'Kritik')->count(),
                'Düşük Stok' => $inventory->where('Stok Durumu', 'Düşük')->count(),
                'Toplam Ürün' => $inventory->where('Tip', 'Stoklu')->count(),
                'Toplam Hizmet' => $inventory->where('Tip', 'Hizmet')->count()
            ]
        ]);
    }

    // LEGACY METHODS (kept for backward compatibility)
    private function getCustomerList()
    {
        $customers = DB::table('customers')
            ->select(
                'id as ID',
                'first_name as Ad',
                'last_name as Soyad',
                DB::raw('COALESCE(email, "E-posta Yok") as "E-posta"'),
                DB::raw('COALESCE(phone, "Telefon Yok") as Telefon'),
                DB::raw('COALESCE(total_visits, 0) as "Toplam Ziyaret"'),
                DB::raw('COALESCE(total_spent, 0.00) as "Toplam Harcama"'),
                DB::raw('COALESCE(last_visit, "Ziyaret Yok") as "Son Ziyaret"'),
                DB::raw('CASE WHEN is_vip = 1 THEN "VIP" ELSE "Normal" END as Durum')
            )
            ->orderBy('total_spent', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $customers,
            'title' => 'Müşteri Listesi',
            'total_records' => $customers->count()
        ]);
    }

    private function getEmployeeList()
    {
        $employees = DB::table('employees')
            ->select(
                'id as ID',
                'name as "Personel Adı"',
                DB::raw('COALESCE(email, "E-posta Yok") as "E-posta"'),
                DB::raw('COALESCE(phone, "Telefon Yok") as Telefon'),
                DB::raw('COALESCE(hire_date, "Tarih Yok") as "İşe Başlama"'),
                DB::raw('COALESCE(position, "Pozisyon Belirtilmemiş") as Pozisyon'),
                DB::raw('CASE WHEN is_active = 1 THEN "Aktif" ELSE "Pasif" END as Durum')
            )
            ->get();

        return response()->json([
            'success' => true,
            'data' => $employees,
            'title' => 'Personel Listesi',
            'total_records' => $employees->count()
        ]);
    }

    private function getServicesList()
    {
        $services = DB::table('menu_items')
            ->leftJoin('categories', 'services.category_id', '=', 'categories.id')
            ->select(
                'services.id as ID',
                'services.name as "Hizmet Adı"',
                DB::raw('COALESCE(services.price, 0.00) as Fiyat'),
                DB::raw('COALESCE(services.discount_price, 0.00) as "İndirimli Fiyat"'),
                DB::raw('CASE WHEN services.is_active = 1 THEN "Aktif" ELSE "Pasif" END as Durum'),
                DB::raw('COALESCE(categories.name, "Kategori Yok") as Kategori'),
                DB::raw('COALESCE(services.stock, 0) as Stok')
            )
            ->get();

        return response()->json([
            'success' => true,
            'data' => $services,
            'title' => 'Hizmet Listesi',
            'total_records' => $services->count()
        ]);
    }

    public function getStats()
    {
        try {
            // Use caching for frequently accessed stats (5 minutes cache)
            $cacheKey = 'dashboard_stats_' . Carbon::now()->format('Y-m-d-H-i');
            
            return Cache::remember($cacheKey, 300, function () {
                $today = Carbon::today();
                $thisMonth = Carbon::now()->startOfMonth();
                $thisYear = Carbon::now()->startOfYear();

                // Optimize with single queries instead of multiple DB calls
                $customerStats = DB::table('customers')
                    ->selectRaw('
                        COUNT(*) as total_customers,
                        SUM(CASE WHEN is_vip = 1 THEN 1 ELSE 0 END) as vip_customers
                    ')
                    ->first();

                $employeeStats = DB::table('employees')
                    ->selectRaw('COUNT(*) as total_employees')
                    ->where('is_active', 1)
                    ->first();

                $reservationStats = DB::table('reservations')
                    ->selectRaw('
                        SUM(CASE WHEN DATE(start_date) = ? THEN 1 ELSE 0 END) as today_reservations,
                        SUM(CASE WHEN status = "pending" AND DATE(start_date) >= ? THEN 1 ELSE 0 END) as pending_reservations,
                        SUM(CASE WHEN status = "completed" AND DATE(start_date) = ? THEN 1 ELSE 0 END) as completed_today
                    ')
                    ->setBindings([$today->toDateString(), $today->toDateString(), $today->toDateString()])
                    ->first();

                $revenueStats = DB::table('payments')
                    ->selectRaw('
                        SUM(CASE WHEN DATE(created_at) = ? AND payment_status = 1 THEN payment_amount ELSE 0 END) as today_revenue,
                        SUM(CASE WHEN YEAR(created_at) = ? AND MONTH(created_at) = ? AND payment_status = 1 THEN payment_amount ELSE 0 END) as month_revenue,
                        SUM(CASE WHEN YEAR(created_at) = ? AND payment_status = 1 THEN payment_amount ELSE 0 END) as year_revenue
                    ')
                    ->setBindings([
                        $today->toDateString(),
                        $thisMonth->year, $thisMonth->month,
                        $thisYear->year
                    ])
                    ->first();

                $stockStats = DB::table('menu_items')
                    ->selectRaw('COUNT(*) as low_stock_items')
                    ->where('is_stock', 1)
                    ->where('stock', '<=', 10)
                    ->where('is_active', 1)
                    ->first();

                $stats = [
                    'total_customers' => (int) $customerStats->total_customers,
                    'total_employees' => (int) $employeeStats->total_employees,
                    'today_reservations' => (int) $reservationStats->today_reservations,
                    'month_revenue' => (float) ($revenueStats->month_revenue ?? 0),
                    'today_revenue' => (float) ($revenueStats->today_revenue ?? 0),
                    'year_revenue' => (float) ($revenueStats->year_revenue ?? 0),
                    'pending_reservations' => (int) $reservationStats->pending_reservations,
                    'completed_today' => (int) $reservationStats->completed_today,
                    'vip_customers' => (int) $customerStats->vip_customers,
                    'low_stock_items' => (int) $stockStats->low_stock_items
                ];

                return response()->json([
                    'success' => true,
                    'data' => $stats,
                    'metadata' => [
                        'cached_at' => now()->toISOString(),
                        'cache_ttl' => 300
                    ]
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Stats generation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'İstatistikler yüklenirken bir hata oluştu.'
            ], 500);
        }
    }

    /**
     * Verify data relationships and integrity with optimized queries
     */
    public function verifyDataRelationships()
    {
        try {
            $relationships = [];

            // Optimize relationship checks with single queries
            $integrityCheck = DB::select("
                SELECT 
                    'customer_reservations' as check_type,
                    COUNT(r.id) as total_reservations,
                    COUNT(c.id) as valid_reservations,
                    COUNT(r.id) - COUNT(c.id) as orphaned_records
                FROM reservations r 
                LEFT JOIN customers c ON r.customer_id = c.id
                
                UNION ALL
                
                SELECT 
                    'employee_reservations' as check_type,
                    COUNT(r.id) as total_reservations,
                    COUNT(e.id) as valid_reservations,
                    COUNT(r.id) - COUNT(e.id) as orphaned_records
                FROM reservations r 
                LEFT JOIN employees e ON r.employee_id = e.id
                
                UNION ALL
                
                SELECT 
                    'reservation_payments' as check_type,
                    COUNT(p.id) as total_payments,
                    COUNT(r.id) as valid_payments,
                    COUNT(p.id) - COUNT(r.id) as orphaned_records
                FROM payments p 
                LEFT JOIN reservations r ON p.reservation_id = r.id
                WHERE p.reservation_id IS NOT NULL
                
                UNION ALL
                
                SELECT 
                    'service_categories' as check_type,
                    COUNT(s.id) as total_services,
                    COUNT(c.id) as categorized_services,
                    COUNT(s.id) - COUNT(c.id) as orphaned_records
                FROM services s 
                LEFT JOIN categories c ON s.category_id = c.id
                WHERE s.category_id IS NOT NULL
            ");

            // Process results efficiently
            foreach ($integrityCheck as $check) {
                $relationships[$check->check_type] = [
                    'description' => $this->getRelationshipDescription($check->check_type),
                    'orphaned_records' => (int) $check->orphaned_records,
                    'status' => $check->orphaned_records == 0 ? 'healthy' : 'warning',
                    'total_records' => (int) $check->total_reservations ?? $check->total_payments ?? $check->total_services,
                    'valid_records' => (int) $check->valid_reservations ?? $check->valid_payments ?? $check->categorized_services
                ];
            }

            // Business validations with single aggregated query
            $businessValidations = $this->performBusinessValidations();

            return response()->json([
                'success' => true,
                'title' => 'Veri Bütünlüğü ve İlişki Analizi',
                'relationships' => $relationships,
                'business_validations' => $businessValidations,
                'overall_status' => $this->calculateOverallHealthStatus($relationships, $businessValidations),
                'total_records' => count($relationships) + count($businessValidations),
                'metadata' => [
                    'generated_at' => now()->toISOString(),
                    'execution_time' => microtime(true) - LARAVEL_START
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Data verification error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Veri doğrulama işlemi sırasında bir hata oluştu.'
            ], 500);
        }
    }

    /**
     * Get human-readable relationship descriptions
     */
    private function getRelationshipDescription($type)
    {
        $descriptions = [
            'customer_reservations' => 'Müşteri - Rezervasyon İlişkisi',
            'employee_reservations' => 'Personel - Rezervasyon İlişkisi',
            'reservation_payments' => 'Rezervasyon - Ödeme İlişkisi',
            'service_categories' => 'Hizmet - Kategori İlişkisi'
        ];

        return $descriptions[$type] ?? 'Bilinmeyen İlişki';
    }

    /**
     * Perform business logic validations efficiently
     */
    private function performBusinessValidations()
    {
        $validations = [];

        // Efficient business validation queries
        $businessChecks = DB::select("
            SELECT 
                'negative_prices' as validation_type,
                COUNT(*) as issue_count,
                'Negatif fiyatlı hizmetler' as description
            FROM services 
            WHERE price < 0
            
            UNION ALL
            
            SELECT 
                'future_payments' as validation_type,
                COUNT(*) as issue_count,
                'Gelecek tarihli ödemeler' as description
            FROM payments 
            WHERE DATE(created_at) > CURDATE()
            
            UNION ALL
            
            SELECT 
                'overlapping_reservations' as validation_type,
                COUNT(*) as issue_count,
                'Çakışan rezervasyonlar' as description
            FROM reservations r1
            INNER JOIN reservations r2 ON r1.table_id = r2.table_id 
                AND r1.id != r2.id
                AND r1.start_date < r2.end_date 
                AND r1.end_date > r2.start_date
                AND r1.status NOT IN ('cancelled')
                AND r2.status NOT IN ('cancelled')
        ");

        foreach ($businessChecks as $check) {
            $validations[] = [
                'validation' => $check->description,
                'status' => $check->issue_count == 0 ? 'healthy' : 'error',
                'issues_found' => (int) $check->issue_count
            ];
        }

        return $validations;
    }

    /**
     * Calculate overall system health status
     */
    private function calculateOverallHealthStatus($relationships, $businessValidations)
    {
        $errorCount = 0;
        $warningCount = 0;

        foreach ($relationships as $rel) {
            if ($rel['status'] === 'error') $errorCount++;
            if ($rel['status'] === 'warning') $warningCount++;
        }

        foreach ($businessValidations as $val) {
            if ($val['status'] === 'error') $errorCount++;
            if ($val['status'] === 'warning') $warningCount++;
        }

        if ($errorCount > 0) return 'error';
        if ($warningCount > 0) return 'warning';
        return 'healthy';
    }

    /**
     * Get comprehensive business insights
     */
    public function getBusinessInsights($startDate, $endDate)
    {
        try {
            // Revenue per day analysis
            $dailyRevenue = DB::table('payments')
                ->selectRaw('DATE(created_at) as Tarih, COUNT(*) as "İşlem Sayısı", SUM(payment_amount) as "Günlük Gelir"')
                ->where('payment_status', 1)
                ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('Tarih', 'desc')
                ->get();

            // Top performing employees
            $topEmployees = DB::table('employees')
                ->leftJoin('reservations', 'employees.id', '=', 'reservations.employee_id')
                ->leftJoin('payments', 'reservations.id', '=', 'payments.reservation_id')
                ->select(
                    'employees.name as "Personel Adı"',
                    DB::raw('COUNT(DISTINCT reservations.id) as "Rezervasyon Sayısı"'),
                    DB::raw('SUM(COALESCE(payments.payment_amount, 0)) as "Toplam Satış"'),
                    DB::raw('ROUND(AVG(COALESCE(payments.payment_amount, 0)), 2) as "Ortalama İşlem"')
                )
                ->whereBetween(DB::raw('DATE(reservations.start_date)'), [$startDate, $endDate])
                ->where('payments.payment_status', 1)
                ->groupBy('employees.id', 'employees.name')
                ->orderBy('Toplam Satış', 'desc')
                ->limit(10)
                ->get();

            // Peak hours analysis
            $peakHours = DB::table('reservations')
                ->selectRaw('HOUR(start_date) as "Saat", COUNT(*) as "Rezervasyon Sayısı"')
                ->whereBetween(DB::raw('DATE(start_date)'), [$startDate, $endDate])
                ->groupBy(DB::raw('HOUR(start_date)'))
                ->orderBy('Rezervasyon Sayısı', 'desc')
                ->get();

            // Customer retention analysis
            $customerFrequency = DB::table('customers')
                ->leftJoin('reservations', 'customers.id', '=', 'reservations.customer_id')
                ->select(
                    DB::raw('COALESCE(customers.name, CONCAT(COALESCE(customers.first_name, ""), " ", COALESCE(customers.last_name, "")), "Müşteri") as "Müşteri"'),
                    DB::raw('COUNT(reservations.id) as "Toplam Rezervasyon"'),
                    DB::raw('MAX(reservations.start_date) as "Son Rezervasyon"'),
                    DB::raw('DATEDIFF(CURDATE(), MAX(reservations.start_date)) as "Son Ziyaretten Gün"')
                )
                ->whereBetween(DB::raw('DATE(reservations.start_date)'), [$startDate, $endDate])
                ->groupBy('customers.id', 'customers.name', 'customers.last_name')
                ->having('Toplam Rezervasyon', '>', 1)
                ->orderBy('Toplam Rezervasyon', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'title' => 'İş Zekası Analizi (' . $startDate . ' - ' . $endDate . ')',
                'sections' => [
                    [
                        'title' => 'Günlük Gelir Trendi',
                        'data' => $dailyRevenue
                    ],
                    [
                        'title' => 'En Başarılı Personeller',
                        'data' => $topEmployees
                    ],
                    [
                        'title' => 'Yoğun Saatler',
                        'data' => $peakHours
                    ],
                    [
                        'title' => 'Sadık Müşteriler',
                        'data' => $customerFrequency
                    ]
                ],
                'summary' => [
                    'Toplam Günlük Gelir Kayıtları' => $dailyRevenue->count(),
                    'En Yüksek Günlük Gelir' => '₺' . number_format($dailyRevenue->max('Günlük Gelir'), 2),
                    'Ortalama Günlük Gelir' => '₺' . number_format($dailyRevenue->avg('Günlük Gelir'), 2),
                    'En Yoğun Saat' => $peakHours->first()->Saat ?? 'Veri yok',
                    'Tekrarlayan Müşteri Sayısı' => $customerFrequency->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'İş analizi hatası: ' . $e->getMessage()], 500);
        }
    }
} 
