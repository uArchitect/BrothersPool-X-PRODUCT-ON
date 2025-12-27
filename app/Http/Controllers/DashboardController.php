<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Response;



class DashboardController extends Controller
{
    protected $data = [];

    public function __construct()
    {
        $this->loadCommonData();
        $this->defineUserGates();
    }

    /**
     * Ortak verileri yükleyen metot - Optimized with caching
     */
    protected function loadCommonData()
    {
        // Cache frequently accessed data
        $this->data['customerCount'] = 0; // Customer module disabled
        
        // Cache settings for 1 hour
        $this->data['settings'] = Cache::remember('settings', 3600, function () {
            return DB::table('settings')->find(1);
        });
        
        // Cache payment methods for 1 hour
        $this->data['payment_methods'] = Cache::remember('payment_methods', 3600, function () {
            return DB::table('payment_methods')->get();
        });
        
        // Rezervasyon modülü kaldırıldı
        $this->data['dailyReservationCount'] = 0;
        
        // Cache package features for 1 hour
        $this->data['package_features'] = Cache::remember('package_features', 3600, function () {
            return DB::table('package_features')->get();
        });

        // Hızlı erişim için gerekli veriler
        $this->data['expense_types'] = DB::table('expense_types')->get();
        $this->data['accounts'] = DB::table('accounts')->get();
        $this->data['customers'] = DB::table('customers')->orderBy('title')->get();
        $this->data['package_features'] = Cache::remember('package_features', 3600, function () {
            return DB::table('package_features')->first() ?? (object)[
                'pos_management' => 1,
                'service_management' => 1,
                'employee_management' => 1,
                'payment_management' => 1,
                'campaign_management' => 1,
                'warehouses_management' => 1,
                'feedback_management' => 1,
                'report_management' => 1,
                'expired_date' => now()->addDays(30)->format('Y-m-d'),
                'name' => 'Başlangıç Paketi'
            ];
        });
        
        $this->data['package_visible'] = $this->getZeroFields('package_features', 1);
        
        // Cache user permissions for 30 minutes
        $this->data['user_permissions'] = Cache::remember('user_permissions_' . Auth::id(), 1800, function () {
            return $this->getUserPermissions(Auth::id());
        });
        
        // Cache categories for 1 hour
        $this->data['categories'] = Cache::remember('categories', 3600, function () {
            return DB::table('categories')->select('id', 'name')->get();
        });
        
        // Cache tables for 1 hour
        $this->data['tables'] = Cache::remember('tables', 3600, function () {
            return DB::table('tables')->get();
        });
        
        // Always load low stock products for header notifications
        $this->data['low_stock_products'] = $this->getLowStockProducts();
        
        // Only calculate stock if needed (not on every page)
        if (request()->routeIs('dashboard') || request()->routeIs('warehouse*')) {
            $this->calculateStock();
        }
    }

    /**
     * Yardımcı metot: Belirtilen tablonun kayıt sayısını döner.
     */
    protected function getCount($table)
    {
        return DB::table($table)->count();
    }

    /**
     * Yardımcı metot: package_features tablosunda,
     * hariç tutulacak sütunlar dışındaki değerleri sıfır olan alanları getirir.
     */
    protected function getZeroFields($table, $id)
    {
        $columns = Schema::getColumnListing($table);
        $excludedColumns = ['id', 'name', 'created_at', 'updated_at'];
        $record = DB::table($table)->find($id);
        $zeroFields = [];

        if ($record) {
            foreach ($columns as $column) {
                if (!in_array($column, $excludedColumns) && (int) $record->$column === 0) {
                    $zeroFields[$column] = 0;
                }
            }
        }
        return (object) $zeroFields;
    }

 
    protected function getLowStockProducts()
    {
        try {
            $settings = DB::table('settings')->find(1);
            $lowStockThreshold = isset($settings->low_stock_threshold) && is_numeric($settings->low_stock_threshold)
                ? (int)$settings->low_stock_threshold
                : 5; // Varsayılan 5

            $products = DB::table('menu_items')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('stock_movements')
                          ->whereRaw('stock_movements.menu_item_id = menu_items.id');
                })
                ->get();

            if ($products->isEmpty()) {
                return [];
            }

            $productIds = $products->pluck('id')->toArray();

            $movements = DB::table('stock_movements')
                ->whereIn('menu_item_id', $productIds)
                ->get()
                ->groupBy('menu_item_id');

            $positiveTypes = ['in', 'purchase', 'return', 'transfer'];
            $lowStockProducts = [];

            foreach ($products as $product) {
                $productMovements = $movements->get($product->id, collect());

                $totalIn = $productMovements->filter(fn($m) => in_array($m->type, $positiveTypes))->sum('quantity');
                $totalOut = $productMovements->filter(fn($m) => !in_array($m->type, $positiveTypes))->sum('quantity');
                $calculatedStock = $totalIn - $totalOut;

                if ($calculatedStock > 0 && $calculatedStock <= $lowStockThreshold) {
                    $lowStockProducts[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'stock' => $calculatedStock
                    ];
                }
            }

            return $lowStockProducts;

        } catch (\Exception $e) {
            return [];
        }
    }


    public function calculateStock()
    {
        \DB::beginTransaction();
        try {
            $settings = \DB::table('settings')->find(1);
            $lowStockThreshold = isset($settings->low_stock_threshold) && is_numeric($settings->low_stock_threshold)
                ? (int)$settings->low_stock_threshold
                : 5; 

            $products = \DB::table('menu_items')->get()->keyBy('id');
            if ($products->isEmpty()) {
                return response()->json(['success' => true, 'message' => 'Hiç ürün yok, stok güncellenmedi.', 'low_stock_products' => []]);
            }

            $productIds = $products->keys()->all();

            $movements = \DB::table('stock_movements')
                ->whereIn('menu_item_id', $productIds)
                ->get()
                ->groupBy('menu_item_id');

            $positiveTypes = ['in', 'purchase', 'return', 'transfer'];
            $now = now();

            $updates = [];
            $lowStockProducts = [];
            foreach ($products as $productId => $product) {
                $productMovements = $movements->get($productId, collect());

                if ($productMovements->isEmpty() && isset($product->stock) && $product->stock > 0) {
                    \DB::table('stock_movements')->insert([
                        'menu_item_id'    => $product->id,
                        'warehouse_id'   => $product->warehouse_id ?? null,
                        'type'           => 'in',
                        'quantity'       => $product->stock,
                        'reference_type' => 'manual',
                        'reference_id'   => null,
                        'notes'          => 'Ürün oluşturuldu',
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ]);
                    $productMovements = collect([(object)[
                        'type' => 'in',
                        'quantity' => $product->stock
                    ]]);
                }

                $totalIn = $productMovements->filter(fn($m) => in_array($m->type, $positiveTypes))->sum('quantity');
                $totalOut = $productMovements->filter(fn($m) => !in_array($m->type, $positiveTypes))->sum('quantity');
                $calculatedStock = $totalIn - $totalOut;
                $isStock = $calculatedStock > 0 ? 1 : 0;

                if ($product->stock != $calculatedStock || $product->is_stock != $isStock) {
                    $updates[] = [
                        'id' => $product->id,
                        'stock' => $calculatedStock,
                        'is_stock' => $isStock
                    ];
                }

                if ($calculatedStock <= $lowStockThreshold) {
                    $lowStockProducts[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'stock' => $calculatedStock
                    ];
                }
            }

            foreach ($updates as $update) {
                \DB::table('menu_items')
                    ->where('id', $update['id'])
                    ->update([
                        'stock' => $update['stock'],
                        'is_stock' => $update['is_stock']
                    ]);
            }

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Stoklar güncellendi.',
                'updated_count' => count($updates),
                'low_stock_products' => $lowStockProducts
            ]);
        } catch (\Throwable $e) {
            \DB::rollBack();
            // Loglama yapılabilir
            return response()->json([
                'success' => false,
                'message' => 'Stok güncellenirken hata oluştu: ' . $e->getMessage(),
                'low_stock_products' => []
            ], 500);
        }
    }

   
    protected function getUserPermissions($userId)
    {
        return DB::table('permissions')->join('user_permissions', 'permissions.id', '=', 'user_permissions.permission_id')->where('user_permissions.user_id', $userId)->pluck('permissions.name')->toArray();
    }

    /**
     * Kullanıcı izinlerine göre Gate tanımlamalarını yapar.
     */
    protected function defineUserGates()
    {
        foreach ($this->data['user_permissions'] as $permission) {
            Gate::define($permission, function () use ($permission) {
                return in_array($permission, $this->data['user_permissions']);
            });
        }
    }

    /**
     * Dashboard (Ana Sayfa) verilerini yükler.
     */
    public function index()
    {
        // Get real financial data
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');
        
        // Real metrics
        $basicCounts = [
            'customerCount' => DB::table('customers')->count(),
            'dailyReservationCount' => 0,
            'totalSales' => DB::table('sales')->whereDate('created_at', $today)->count(),
            'totalCustomers' => DB::table('customers')->count(),
            'totalStaff' => DB::table('employees')->where('is_active', 1)->count(),
            'totalMenuItems' => DB::table('menu_items')->count(),
            'totalExpenses' => DB::table('expenses')->count(),
        ];

        foreach ($basicCounts as $key => $value) {
            $this->data[$key] = $value;
        }

        // Real financial calculations
        $this->data['dailyIncome'] = DB::table('incomes')->whereDate('date', $today)->sum('amount') ?? 0;
        $this->data['dailyExpense'] = DB::table('expenses')->whereDate('date', $today)->sum('total') ?? 0;
        $this->data['monthlyIncome'] = DB::table('incomes')->where('date', 'like', $thisMonth . '%')->sum('amount') ?? 0;
        $this->data['monthlyExpense'] = DB::table('expenses')->where('date', 'like', $thisMonth . '%')->sum('total') ?? 0;
        $this->data['totalRevenue'] = DB::table('incomes')->sum('amount') ?? 0;
        $this->data['totalExpense'] = DB::table('expenses')->sum('total') ?? 0;
        $this->data['totalProfit'] = $this->data['totalRevenue'] - $this->data['totalExpense'];
        
        // Account balance
        $this->data['totalBalance'] = DB::table('accounts')->sum('balance') ?? 0;
        
        // Vadesi yaklaşan çekler ve senetler
        $this->data['upcomingChecks'] = DB::table('checks')
            ->where('status', '!=', 'TAHSIL_EDILDI')
            ->whereDate('maturity_date', '>=', $today)
            ->whereDate('maturity_date', '<=', now()->addDays(7)->format('Y-m-d'))
            ->orderBy('maturity_date')
            ->get();
            
        $this->data['upcomingPromissoryNotes'] = DB::table('promissory_notes')
            ->where('status', '!=', 'ODENDI')
            ->whereDate('maturity_date', '>=', $today)
            ->whereDate('maturity_date', '<=', now()->addDays(7)->format('Y-m-d'))
            ->orderBy('maturity_date')
            ->get();

        // Vade hatırlatma sistemi
        $this->data['vadeHatirlatmalari'] = $this->getVadeHatirlatmalari();

        // Aylık gelir, gider ve kar hesaplamaları
        // Fake monthly data
        $this->data['aylikGelirVerisi'] = [28000, 32500, 31000, 35500, 42000, 44800, 47200, 49000, 0, 0, 0, 0];
        $this->data['aylikGiderVerisi'] = [18000, 20500, 21000, 23000, 25000, 26100, 27000, 28500, 0, 0, 0, 0];
        $this->data['aylikKarVerisi'] = array_map(function($g, $d){return $g-$d;}, $this->data['aylikGelirVerisi'], $this->data['aylikGiderVerisi']);
        $this->data['buyumeOrani'] = 8.4;

        // Hızlı erişim için gerekli veriler
        $this->data['expense_types'] = DB::table('expense_types')->get();
        $this->data['accounts'] = DB::table('accounts')->get();
        $this->data['customers'] = DB::table('customers')->orderBy('title')->get();

        return view('dashboard', $this->data);
    }

    /**
     * Vade hatırlatma verilerini getir
     */
    protected function getVadeHatirlatmalari()
    {
        $today = now();
        $nextWeek = $today->copy()->addDays(7);
        
        $vadeHatirlatmalari = collect();
        
        // Çek vade hatırlatmaları
        $cekler = DB::table('checks')
            ->leftJoin('customers', 'checks.customer_id', '=', 'customers.id')
            ->where('checks.status', '!=', 'TAHSIL_EDILDI')
            ->whereBetween('checks.maturity_date', [$today->format('Y-m-d'), $nextWeek->format('Y-m-d')])
            ->select('checks.*', 'customers.title as customer_name')
            ->get()
            ->map(function($cek) {
                return [
                    'type' => 'Çek',
                    'title' => 'Çek No: ' . $cek->check_number,
                    'amount' => $cek->amount,
                    'due_date' => $cek->maturity_date,
                    'customer' => $cek->customer_name ?? 'Müşteri',
                    'status' => $cek->status,
                    'days_left' => now()->diffInDays($cek->maturity_date, false),
                    'priority' => $cek->maturity_date <= now()->addDays(3)->format('Y-m-d') ? 'high' : 'medium'
                ];
            });
        
        // Senet vade hatırlatmaları
        $senetler = DB::table('promissory_notes')
            ->leftJoin('customers', 'promissory_notes.customer_id', '=', 'customers.id')
            ->where('promissory_notes.status', '!=', 'ODENDI')
            ->whereBetween('promissory_notes.maturity_date', [$today->format('Y-m-d'), $nextWeek->format('Y-m-d')])
            ->select('promissory_notes.*', 'customers.title as customer_name')
            ->get()
            ->map(function($senet) {
                return [
                    'type' => 'Senet',
                    'title' => 'Senet No: ' . $senet->note_number,
                    'amount' => $senet->amount,
                    'due_date' => $senet->maturity_date,
                    'customer' => $senet->customer_name ?? 'Müşteri',
                    'status' => $senet->status,
                    'days_left' => now()->diffInDays($senet->maturity_date, false),
                    'priority' => $senet->maturity_date <= now()->addDays(3)->format('Y-m-d') ? 'high' : 'medium'
                ];
            });
        
        // Müşteri borç hatırlatmaları (30 gün üzeri)
        $musteriBorclari = DB::table('customers')
            ->where('current_balance', '<', 0)
            ->where('current_balance', '<=', -1000) // 1000 TL üzeri borç
            ->get()
            ->map(function($musteri) {
                return [
                    'type' => 'Müşteri Borcu',
                    'title' => $musteri->title ?? 'Müşteri',
                    'amount' => abs($musteri->current_balance),
                    'due_date' => now()->subDays(30)->format('Y-m-d'), // 30 gün önce
                    'customer' => $musteri->title ?? 'Müşteri',
                    'status' => 'Beklemede',
                    'days_left' => -30,
                    'priority' => 'high'
                ];
            });
        
        return $vadeHatirlatmalari
            ->merge($cekler)
            ->merge($senetler)
            ->merge($musteriBorclari)
            ->sortBy('due_date')
            ->take(10); // En fazla 10 hatırlatma göster
    }

    /**
     * Hızlı erişim için AJAX endpoint
     */
    public function quickAccessData()
    {
        try {
            $data = [
                'expense_types' => DB::table('expense_types')->get(),
                'accounts' => DB::table('accounts')->get(),
                'customers' => DB::table('customers')->orderBy('title')->get(),
            ];
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Veri yüklenirken hata oluştu'], 500);
        }
    }

    /**
     * Yardımcı metot: Aylık gelir, gider ve kar verilerini hesaplar.
     */
    protected function calculateMonthlyData()
    {
        $monthlyData = DB::table('sales')->selectRaw('MONTH(created_at) as ay, YEAR(created_at) as yil, SUM(total) as gelir')->whereYear('created_at', now()->year)->whereDate('created_at', '<=', now())->groupBy('yil', 'ay')->orderBy('ay')->get()->keyBy('ay');

        $monthlyExpenses = DB::table('expenses')->selectRaw('MONTH(created_at) as ay, YEAR(created_at) as yil, SUM(total) as gider')->whereYear('created_at', now()->year)->whereDate('created_at', '<=', now())->groupBy('yil', 'ay')->orderBy('ay')->get()->keyBy('ay');

        $aylikGelirVerisi = [];
        $aylikGiderVerisi = [];
        $aylikKarVerisi = [];

        $currentMonth = (int) now()->format('n');

        for ($i = 1; $i <= 12; $i++) {
            if ($i == $currentMonth) {
                $todayIncome = DB::table('sales')
                    ->whereDate('created_at', now()->toDateString())
                    ->sum('paid');
                $gelir = (isset($monthlyData[$i]) ? $monthlyData[$i]->gelir : 0) + $todayIncome;
                $gider = isset($monthlyExpenses[$i]) ? $monthlyExpenses[$i]->gider : 0;
            } else {
                $gelir = isset($monthlyData[$i]) ? $monthlyData[$i]->gelir : 0;
                $gider = isset($monthlyExpenses[$i]) ? $monthlyExpenses[$i]->gider : 0;
            }
            $aylikGelirVerisi[] = $gelir;
            $aylikGiderVerisi[] = $gider;
            $aylikKarVerisi[] = $gelir - $gider;
        }

        // Büyüme oranı hesaplama
        $previousMonth = $currentMonth - 1;
        if ($previousMonth == 0) {
            $previousMonth = 12;
        }
        $currentMonthIncome = $aylikGelirVerisi[$currentMonth - 1];
        $previousMonthIncome = $aylikGelirVerisi[$previousMonth - 1];
        $buyumeOrani = 0;
        if ($previousMonthIncome > 0) {
            $buyumeOrani = round((($currentMonthIncome - $previousMonthIncome) / $previousMonthIncome) * 100, 1);
        }

        $this->data['aylikGelirVerisi'] = $aylikGelirVerisi;
        $this->data['aylikGiderVerisi'] = $aylikGiderVerisi;
        $this->data['aylikKarVerisi'] = $aylikKarVerisi;
        $this->data['buyumeOrani'] = $buyumeOrani;
    }

    /**
     * Kullanıcı yönetimi sayfası.
     */
    /**
     * Kullanıcı yönetimi sayfası.
     * Not: Müşteri satır satır yüklediğini görmesin diye tüm kullanıcılar ve izinler tek seferde yükleniyor.
     */
    public function users()
    {
        // Kullanıcıları ve izinlerini tek sorguda çek
        $users = DB::table('users')
            ->leftJoin('user_permissions', 'users.id', '=', 'user_permissions.user_id')
            ->leftJoin('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                DB::raw('GROUP_CONCAT(permissions.id) as permission_ids'),
                DB::raw('GROUP_CONCAT(permissions.name) as permission_names')
            )
            ->groupBy('users.id', 'users.name', 'users.email', 'users.phone')
            ->get();

        // Her kullanıcıya izinlerini dizi olarak ekle
        foreach ($users as $user) {
            $user->permissions = [];
            if ($user->permission_ids) {
                $ids = explode(',', $user->permission_ids);
                $names = explode(',', $user->permission_names);
                foreach ($ids as $idx => $pid) {
                    if (!empty($pid)) {
                        $user->permissions[] = [
                            'id' => $pid,
                            'name' => $names[$idx] ?? null
                        ];
                    }
                }
            }
            unset($user->permission_ids, $user->permission_names);
        }

        $this->data['users'] = $users;

        // Kullanıcı-izin eşleşmelerini tek sorguda çek
        $this->data['permissions'] = DB::table('user_permissions')
            ->join('permissions', 'permissions.id', '=', 'user_permissions.permission_id')
            ->join('users', 'user_permissions.user_id', '=', 'users.id')
            ->select(
                'permissions.id',
                'permissions.name as permission_name',
                'users.name as user_name',
                'users.email'
            )
            ->orderBy('permissions.name')
            ->get();

        // Tüm izinleri önbellekten veya tek sorguda çek
        $this->data['all_permissions'] = cache()->remember('all_permissions', 60, function () {
            return DB::table('permissions')->get();
        });

        return view('users', $this->data);
    }

    /**
     * Ayarlar sayfası.
     */
    public function settings()
    {
        $this->data['settings'] = DB::table('settings')->find(1) ?? (object)[
            'restaurant_name' => '',
            'salon_name' => '',
            'restaurant_type' => 'restaurant',
            'phone_number' => '',
            'email' => '',
            'address' => '',
            'city' => '',
            'postal_code' => '',
            'business_license' => '',
            'tax_office' => '',
            'tax_number' => '',
            'vat_number' => '',
            'restaurant_logo' => '',
            'logo' => '',
            'opening_time' => '09:00',
            'closing_time' => '22:00',
            'work_start' => '09:00',
            'work_end' => '22:00'
        ];
        $this->data['package_features'] = DB::table('package_features')->first() ?? (object)[
            'pos_management' => 1,
            'service_management' => 1,
            'employee_management' => 1,
            'payment_management' => 1,
            'campaign_management' => 1,
            'warehouses_management' => 1,
            'feedback_management' => 1,
            'report_management' => 1,
            'expired_date' => now()->addDays(30)->format('Y-m-d'),
            'name' => 'Başlangıç Paketi'
        ];
        
        // Restaurant settings data
        $this->data['restaurant_types'] = [
            'restaurant' => 'Restoran',
            'cafe' => 'Kafe', 
            'bar' => 'Bar',
            'fast_food' => 'Fast Food',
            'fine_dining' => 'Fine Dining'
        ];
        
        $this->data['currencies'] = [
            'TRY' => 'Türk Lirası (₺)',
            'USD' => 'Amerikan Doları ($)',
            'EUR' => 'Euro (€)'
        ];
        
        $this->data['timezones'] = [
            'Europe/Istanbul' => 'Türkiye (GMT+3)',
            'Europe/London' => 'Londra (GMT+0)',
            'America/New_York' => 'New York (GMT-5)'
        ];

        return view('settings', $this->data);
    }

    /**
     * Menü Yönetimi sayfası.
     */
    public function menu()
    {
        $this->data['menu_items'] = DB::table('menu_items')
            ->join('categories', 'menu_items.category_id', '=', 'categories.id')
            ->select('menu_items.*', 'categories.name as category_name')
            ->orderBy('menu_items.created_at', 'desc')
            ->paginate(20);

        $this->data['categories'] = DB::table('categories')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('menu.index', $this->data);
    }

    /**
     * Ürün Kategorileri sayfası - MOVED TO CategoryController
     */

    /**
     * Masa Yönetimi sayfası.
     */
    public function tables()
    {
        // Get real tables from database
        $this->data['tables'] = DB::table('tables')
            ->leftJoin('employees', 'tables.employee_id', '=', 'employees.id')
            ->select(
                'tables.*',
                'employees.name as employee_name'
            )
            ->orderBy('tables.table_number')
            ->get();

        // Get employees for assignment
        $this->data['employees'] = DB::table('employees')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('tables.index', $this->data);
    }

    /**
     * Sipariş Yönetimi sayfası.
     */
    public function orders()
    {
        // Get real orders from database
        $this->data['orders'] = DB::table('orders')
            ->leftJoin('tables', 'orders.table_id', '=', 'tables.id')
            ->leftJoin('employees', 'orders.waiter_id', '=', 'employees.id')
            ->select(
                'orders.*',
                'tables.table_number',
                'employees.name as waiter_name'
            )
            ->orderBy('orders.created_at', 'desc')
            ->get();

        // Get real tables from database
        $this->data['tables'] = DB::table('tables')
            ->where('is_active', 1)
            ->orderBy('table_number')
            ->get();

        // Get real waiters from database
        $this->data['waiters'] = DB::table('employees')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('orders.index', $this->data);
    }


    /**
     * Mutfak Ekranı sayfası - Order-based display
     */
    public function kitchen()
    {
        // Gerçek mutfak siparişleri verisi - Sipariş bazlı
        $orders = DB::table('orders')
            ->leftJoin('tables', 'orders.table_id', '=', 'tables.id')
            ->whereIn('orders.status', ['pending', 'preparing', 'ready'])
            ->select(
                'orders.id as order_id',
                'orders.status as order_status',
                'orders.created_at as order_time',
                DB::raw('COALESCE(tables.table_number, "TAKEAWAY") as table_number'),
                'orders.customer_name',
                'orders.notes as order_notes',
                DB::raw('COALESCE(orders.total_amount, 0) as total_amount')
            )
            ->orderBy('orders.created_at', 'asc')
            ->get();

        // Her sipariş için order_items'ları ekle
        $ordersWithItems = $orders->map(function ($order) {
            $orderItems = DB::table('order_items')
                ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->where('order_items.order_id', $order->order_id)
                ->whereIn('order_items.status', ['pending', 'preparing', 'ready'])
                ->select(
                    'order_items.id as item_id',
                    'order_items.quantity',
                    'order_items.special_instructions as item_notes',
                    'order_items.status as item_status',
                    'menu_items.name as item_name',
                    'menu_items.prep_time'
                )
                ->get();

            $order->items = $orderItems;
            
            // Siparişin genel durumunu belirle
            $hasPending = $orderItems->where('item_status', 'pending')->count() > 0;
            $hasPreparing = $orderItems->where('item_status', 'preparing')->count() > 0;
            $hasReady = $orderItems->where('item_status', 'ready')->count() > 0;
            
            // Eğer siparişin kendisi ready ise, kitchen_status'u da ready yap
            if ($order->order_status === 'ready') {
                $order->kitchen_status = 'ready';
            } elseif ($hasPreparing) {
                $order->kitchen_status = 'preparing';
            } elseif ($hasPending) {
                $order->kitchen_status = 'pending';
            } elseif ($hasReady) {
                $order->kitchen_status = 'ready';
            } else {
                $order->kitchen_status = 'completed';
            }
            
            return $order;
        });

        // Sadece mutfakta işlenmesi gereken siparişleri filtrele ve tekrarları kaldır
        $filteredOrders = $ordersWithItems->filter(function ($order) {
            return in_array($order->kitchen_status, ['pending', 'preparing', 'ready']);
        });
        
        // Tekrarları kaldır
        $uniqueOrders = collect();
        $seenOrderIds = [];
        
        foreach ($filteredOrders as $order) {
            if (!in_array($order->order_id, $seenOrderIds)) {
                $uniqueOrders->push($order);
                $seenOrderIds[] = $order->order_id;
            }
        }
        
        $this->data['orders'] = $uniqueOrders;

        // Sipariş bazlı mutfak istatistikleri
        $kitchenStats = (object) [
            'pending_count' => $this->data['orders']->where('kitchen_status', 'pending')->count(),
            'preparing_count' => $this->data['orders']->where('kitchen_status', 'preparing')->count(),
            'ready_count' => $this->data['orders']->where('kitchen_status', 'ready')->count(),
            'total_count' => $this->data['orders']->count()
        ];

        $this->data['kitchen_stats'] = $kitchenStats;

        return view('kitchen.index', $this->data);
    }

    /**
     * Raporlar sayfası.
     */
    public function reports()
    {
        // Get real financial data
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');
        
        // Daily sales
        $this->data['daily_sales'] = DB::table('sales')
            ->whereDate('created_at', $today)
            ->sum('total');
            
        // Monthly sales
        $this->data['monthly_sales'] = DB::table('sales')
            ->where('created_at', 'like', $thisMonth . '%')
            ->sum('total');
            
        // Total orders
        $this->data['total_orders'] = DB::table('orders')
            ->whereDate('created_at', $today)
            ->count();
            
        // Popular items (from sales)
        $popularItems = DB::table('sale_items')
            ->join('menu_items', 'sale_items.menu_item_id', '=', 'menu_items.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereDate('sales.created_at', $today)
            ->select('menu_items.name', DB::raw('SUM(sale_items.quantity) as total_quantity'), DB::raw('SUM(sale_items.total_price) as revenue'))
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();
            
        $this->data['popular_items'] = $popularItems;
        
        // Daily expenses
        $this->data['daily_expenses'] = 0; // Expenses table removed
            
        // Net profit
        $this->data['net_profit'] = $this->data['daily_sales'] - $this->data['daily_expenses'];

        return view('reports.index', $this->data);
    }
    
    // Backward compatibility için eski method'u koruyalım
    public function specialties()
    {
        $this->data['specialties'] = DB::table('categories')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
        
        return view('employees_specialties', $this->data);
    }



    /**
     * Müşteri geri bildirimleri sayfası.
     */
    public function feedback()
    {
        $subdomain = explode('.', request()->getHost())[0];
        $cacheKey = 'feedback_data_' . $subdomain;

        $feedbackData = Cache::remember($cacheKey, 120, function () {
            try {
                $client = new Client([
                    'base_uri' => 'http://altf4.masterbm.com',
                    'timeout'  => 10.0,
                ]);

                $feedbackTypes = json_decode(
                    $client->get('/api/v3/getFeedBackType')->getBody(), true
                );

                $feedbackList = json_decode(
                    $client->get('/api/v3/getFeedback?password=1234')->getBody(), true
                );

                return [
                    'feedback_types' => $feedbackTypes ?? [],
                    'getFeedback'    => $feedbackList ?? [],
                ];
            } catch (\Exception $e) {
                \Log::error('Feedback API Error: ' . $e->getMessage());
                return [
                    'feedback_types' => [],
                    'getFeedback'    => [],
                ];
            }
        });

        $this->data['feedback_types'] = $feedbackData['feedback_types'];
        $this->data['getFeedback']    = $feedbackData['getFeedback'];

        return view('feedback', $this->data);
    }

    /**
     * Çalışanlar sayfası.
     */
    public function employees()
    {
        // Personeli ve HR bilgilerini çek
        $employees = DB::table('employees')
            ->select(
                'employees.id',
                'employees.name',
                'employees.phone',
                'employees.hire_date',
                'employees.avatar',
                'employees.is_active',
                'employees.group_id',
                'employees.position_id',
                'employees.created_at',
                'employees.updated_at'
            )
            ->orderBy('employees.name')
            ->get();

        $this->data['employees'] = $employees;
        
        // HR için grup ve görevler
        $this->data['employee_groups'] = DB::table('employee_groups')->orderBy('name')->get();
        $this->data['employee_positions'] = DB::table('employee_positions')->orderBy('name')->get();
        
        // Add specialties data for employee modal - ensure it's always set
        try {
            $this->data['specialties'] = DB::table('categories')
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            // If query fails, try without is_active filter
            try {
                $this->data['specialties'] = DB::table('categories')
                    ->orderBy('name')
                    ->get();
            } catch (\Exception $e2) {
                // If still fails, set empty collection
                $this->data['specialties'] = collect();
            }
        }
        
        // Ensure specialties is always a collection
        if (!isset($this->data['specialties']) || !$this->data['specialties'] instanceof \Illuminate\Support\Collection) {
            $this->data['specialties'] = collect();
        }
        
        // Add tables data for employee modal
        $this->data['tables'] = DB::table('tables')->orderBy('table_number')->get();

        return view('employees', $this->data);
    }
    
    /**
     * Personel oluşturma sayfası (HR için)
     */
    public function employeesCreate()
    {
        $this->data['employee_groups'] = DB::table('employee_groups')->orderBy('name')->get();
        $this->data['employee_positions'] = DB::table('employee_positions')->orderBy('name')->get();
        return view('hr.employees.create', $this->data);
    }
    
    /**
     * Personel düzenleme sayfası (HR için)
     */
    public function employeesEdit($id)
    {
        $employee = DB::table('employees')->where('id', $id)->first();
        
        if (!$employee) {
            return redirect()->route('employees')->with('error', 'Personel bulunamadı!');
        }
        
        $this->data['employee'] = $employee;
        $this->data['employee_groups'] = DB::table('employee_groups')->orderBy('name')->get();
        $this->data['employee_positions'] = DB::table('employee_positions')
            ->where('group_id', $employee->group_id)
            ->orderBy('name')
            ->get();
        
        return view('hr.employees.edit', $this->data);
    }

    /**
     * Müşteriler ve ilgili işlemler sayfası.
     */
    public function customers()
    {
        // Customer module disabled - but still show basic customer data for other modules
        $this->data['customers'] = DB::table('customers')
            ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'phone')
            ->orderBy('name', 'desc')
            ->get();
        
        return view('customers', $this->data);
    }

    /**
     * Kampanyalar sayfası.
     */
    public function campaigns()
    {
        $this->data['customers'] = DB::table('customers')
            ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'phone')
            ->orderBy('name', 'asc')
            ->get();
        $this->data['campaigns'] = DB::table('campaigns')->get();
        return view('campaigns', $this->data);
    }

    /**
     * Hizmetler sayfası.
     */
    public function services()
    {
        $this->data['units'] = DB::table('units')->get();

        $this->data['employees'] = DB::table('employees')->orderBy('name')->get();
        $this->data['tax_rates'] = DB::table('tax_rates')->orderBy('rate')->get();

        // Warehouses verilerini ekle
        $this->data['warehouses'] = DB::table('warehouses')->where('is_active', 1)->orderBy('name')->get();

        $services = DB::table('menu_items')
            ->join('categories', 'menu_items.category_id', '=', 'categories.id')
            ->leftJoin('warehouses', 'menu_items.warehouse_id', '=', 'warehouses.id')
            ->select([
                'menu_items.*',
                'categories.id as category_id',
                'categories.name as category_name',
                'warehouses.name as warehouse_name'
            ])
            ->orderBy('menu_items.name')
            ->get();

        $this->data['categories'] = DB::table('categories')->where('stock_type', 0)->orderBy('name')->get();
        $this->data['menu_items'] = $services;

        return view('menu_items', $this->data);
    }

    /**
     * JSON olarak hizmet verilerini dönen endpoint.
     */
    public function getServicesData()
    {
        // Örnekte, daha önce tanımlanan $services verisini dönmek istiyorsanız
        $services = DB::table('menu_items')->get();
        return response()->json(['data' => $services]);
    }

    /**
     * Gider ekleme sayfası.
     */
    public function expenses()
    {
        $this->data['staff'] = DB::table('employees')->get();
        $this->data['employees'] = DB::table('employees')->get();
        $this->data['expense_types'] = DB::table('expense_types')->get();
        $this->data['expenses_categories'] = DB::table('expense_categories')->get();
        $this->data['accounts'] = DB::table('accounts')->get();
        $this->data['customers'] = DB::table('customers')->orderBy('title')->get(); // Add customers for CRM
        $this->data['expense_id'] = DB::table('expenses')->max('id') + 1;
        // Services table not available - using empty collection
        $this->data['menu_items'] = collect();
        // Add services data for employee modal
        $this->data['services'] = DB::table('menu_items')->where('is_stock', 0)->orderBy('name')->get();
        // Add specialties data for employee modal
        $this->data['specialties'] = DB::table('categories')->where('is_active', 1)->orderBy('name')->get();
        return view('expenses', $this->data);
    }

    /**
     * Gider listesini detaylarıyla birlikte getirir.
     */
    public function expensesList()
    {
        $this->data['expense_types'] = DB::table('expense_types')->get();
        $this->data['accounts'] = DB::table('accounts')->get();
        $this->data['staff'] = DB::table('employees')->get();
        $this->data['employees'] = DB::table('employees')->get(); // Add employees for the view
        $this->data['expenses_categories'] = DB::table('expense_categories')->get();
        // Add services data for employee modal
        $this->data['services'] = DB::table('menu_items')->where('is_stock', 0)->orderBy('name')->get();
        // Add specialties data for employee modal
        $this->data['specialties'] = DB::table('categories')->where('is_active', 1)->orderBy('name')->get();

        $expenses = DB::table('expenses')
            ->leftJoin('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id')
            ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->leftJoin('accounts', 'expenses.account_id', '=', 'accounts.id')
            ->leftJoin('customers', 'expenses.customer_id', '=', 'customers.id')
            ->select(
                'expenses.*',
                'expense_types.name as expense_type_name',
                'expense_categories.name as expense_category_name',
                'accounts.name as account_name',
                'customers.title as customer_name',
                'customers.code as customer_code'
            )
            ->orderBy('expenses.date', 'desc')
            ->get();

        $this->data['expenses'] = $expenses;
        return view('expenses_list', $this->data);
    }

    /**
     * Hesaplar sayfası.
     */
    public function accounts()
    {
        $this->data['customers'] = collect([]); // Customer module disabled
        
        // Hesapları transaction'lara göre kalan tutarla birlikte getir
        $this->data['accounts'] = DB::table('accounts')
            ->select(
                'accounts.*',
                // Gelirler (income transactions)
                DB::raw('COALESCE((SELECT SUM(amount) FROM transactions WHERE account_id = accounts.id AND type = "income"), 0) as total_income'),
                // Giderler (expense transactions)
                DB::raw('COALESCE((SELECT SUM(amount) FROM transactions WHERE account_id = accounts.id AND type = "expense"), 0) as total_expense'),
                // Kalan tutar: Başlangıç bakiyesi + Gelirler - Giderler
                DB::raw('(COALESCE(accounts.balance, 0) + 
                    COALESCE((SELECT SUM(amount) FROM transactions WHERE account_id = accounts.id AND type = "income"), 0) - 
                    COALESCE((SELECT SUM(amount) FROM transactions WHERE account_id = accounts.id AND type = "expense"), 0)
                ) as current_balance')
            )
            ->orderBy('accounts.name')
            ->get();
        
        return view('accounts', $this->data);
    }

    /**
     * Satışlar sayfası.
     */
    public function sales()
    {
        return view('sales', $this->data);
    }

    /**
     * AJAX: Satış verilerini getirir
     */
    public function getSalesData()
    {
        $query = DB::table('sales')
            ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->leftJoin('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('menu_items', 'sale_items.menu_item_id', '=', 'menu_items.id')
            ->leftJoin('orders', 'sales.order_id', '=', 'orders.id');
        
        // Apply date filter if provided
        $dateFilter = request('date_filter');
        if ($dateFilter && $dateFilter !== 'all') {
            $today = now();
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('sales.created_at', $today->toDateString());
                    break;
                case 'week':
                    $startOfWeek = $today->copy()->startOfWeek();
                    $query->whereBetween('sales.created_at', [$startOfWeek, $today]);
                    break;
                case 'month':
                    $startOfMonth = $today->copy()->startOfMonth();
                    $query->whereBetween('sales.created_at', [$startOfMonth, $today]);
                    break;
            }
        }
        
        $sales = $query->select([
                'sales.id',
                'sales.sale_number',
                'sales.order_id',
                'sales.customer_id',
                'customers.name as customer_name',
                'customers.phone as customer_phone',
                'sales.subtotal',
                'sales.tax_amount',
                'sales.discount_amount',
                'sales.total',
                'sales.payment_method',
                'sales.status',
                'sales.notes',
                'sales.seller_id',
                'sales.created_at',
                'sales.updated_at',
                'orders.order_number',
                DB::raw('GROUP_CONCAT(DISTINCT menu_items.name) as product_names'),
                DB::raw('GROUP_CONCAT(DISTINCT sale_items.quantity) as quantities')
            ])
            ->groupBy([
                'sales.id',
                'sales.sale_number',
                'sales.order_id',
                'sales.customer_id',
                'customers.name',
                'customers.phone',
                'sales.subtotal',
                'sales.tax_amount',
                'sales.discount_amount',
                'sales.total',
                'sales.payment_method',
                'sales.status',
                'sales.notes',
                'sales.seller_id',
                'sales.created_at',
                'sales.updated_at',
                'orders.order_number'
            ])
            ->orderBy('sales.created_at', 'desc')
            ->get();

        // Format sales data
        $sales->transform(function ($sale) {
            // Set customer name
            $sale->customer = $sale->customer_name ?? 'Misafir Müşteri';
            
            // Calculate remaining amount (for future payment tracking)
            $sale->remaining = 0; // All sales are fully paid in our system
            
            // Format numeric values
            $sale->total = number_format($sale->total ?? 0, 2, '.', '');
            $sale->subtotal = number_format($sale->subtotal ?? 0, 2, '.', '');
            $sale->tax_amount = number_format($sale->tax_amount ?? 0, 2, '.', '');
            $sale->discount_amount = number_format($sale->discount_amount ?? 0, 2, '.', '');

            return $sale;
        });

        return response()->json([
            'success' => true,
            'data' => $sales
        ]);
    }

    /**
     * Satış detaylarını getirir.
     */
    public function getSaleDetails($saleId)
    {
        $sale = DB::table('sales as s')
            ->leftJoin('customers as c', 's.customer_id', '=', 'c.id')
            ->leftJoin('orders as o', 's.order_id', '=', 'o.id')
            ->where('s.id', $saleId)
            ->select([
                's.id as sale_id',
                's.sale_number',
                's.total',
                's.subtotal',
                's.tax_amount',
                's.discount_amount',
                's.payment_method',
                's.status',
                's.notes',
                's.created_at',
                'c.name as customer_name',
                'c.phone as customer_phone',
                'o.order_number'
            ])
            ->first();
    
        if (!$sale) {
            abort(404, 'Satış bulunamadı.');
        }
    
        // Sale Items + Services tek sorguda çekilir
        $items = DB::table('sale_items as si')
            ->leftJoin('services as sv', 'si.service_id', '=', 'sv.id')
            ->where('si.sale_id', $saleId)
            ->select([
                'si.product_name',
                'si.subtotal',
                'sv.name as service_name'
            ])
            ->get();
    
        return redirect()->route('dashboard')->with('error', 'Satış detayları modülü devre dışı bırakılmıştır.');
    }
    

    /**
     * Depo (warehouse) sayfası.
     */
    public function warehouse()
    {
        $this->data['warehouses'] = DB::table('warehouses')->join('employees', 'warehouses.manager', '=', 'employees.id')->select('warehouses.*', 'employees.name as manager_name')->get();
        $this->data['staff'] = DB::table('employees')->get();
        $this->data['employees'] = DB::table('employees')->get(); // Add employees for the view
        return view('warehouses22', $this->data);
    }

    /**
     * Ürünler sayfası.
     */
    public function products()
    {
        $this->data['units'] = cache()->remember('units', 60 * 24, function () {
            return DB::table('units')->get();
        });

        $this->data['staff'] = cache()->remember('employees', 60 * 24, function () {
            return DB::table('employees')->orderBy('name')->get();
        });
        $this->data['tax_rates'] = cache()->remember('tax_rates', 60 * 24, function () {
            return DB::table('tax_rates')->orderBy('rate')->get();
        });

        // Warehouses verilerini ekle
        $this->data['warehouses'] = cache()->remember('warehouses', 60 * 24, function () {
            return DB::table('warehouses')->where('is_active', 1)->orderBy('name')->get();
        });

        $services = DB::table('menu_items')
            ->join('categories', 'menu_items.category_id', '=', 'categories.id')
            ->leftJoin('warehouses', 'menu_items.warehouse_id', '=', 'warehouses.id')
            ->select([
                'menu_items.*',
                'categories.id as category_id',
                'categories.name as category_name',
                'warehouses.name as warehouse_name'
            ])
            ->orderBy('menu_items.name')
            ->get();

        // Sadece stock_type 1 olan kategoriler gelecek
        $this->data['categories'] = DB::table('categories')
            ->where('stock_type', 1)
            ->orderBy('name')
            ->get();

        $this->data['menu_items'] = $services;

        return view('products', $this->data);
    }

    /**
     * Hizmet düzenleme modalı için verileri getirir.
     */
    public function ServicesEditModal($id)
    {
        $service = DB::table('menu_items')->find($id);

        if (!$service) {
            return response()->json(
                [
                    'error' => 'Hizmet bulunamadı',
                ],
                404,
            );
        }

        // Services module disabled
        return response()->json([
            'success' => false,
            'message' => 'Hizmetler modülü devre dışı bırakılmıştır.',
        ]);
    }


    public function pos()
    {
        $this->data['customers'] = collect([]); // Customer module disabled
        return view('payment_link_send', $this->data);
    }

    public function posSale()
    {
        // Get menu items with categories
        $menuItems = DB::table('menu_items')
            ->join('categories', 'menu_items.category_id', '=', 'categories.id')
            ->select('menu_items.*', 'categories.name as category_name', 'categories.level')
            ->where('menu_items.is_available', 1)
            ->orderBy('categories.level', 'asc')
            ->orderBy('categories.sort_order', 'asc')
            ->orderBy('menu_items.name', 'asc')
            ->get();

        // Get categories for filtering
        $categories = DB::table('categories')
            ->where('is_active', 1)
            ->orderBy('level', 'asc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        // Get tables
        $tables = DB::table('tables')
            ->where('is_active', 1)
            ->orderBy('location', 'asc')
            ->orderBy('table_number', 'asc')
            ->get();

        $this->data['services'] = $menuItems; // For backward compatibility
        $this->data['menu_items'] = $menuItems;
        $this->data['categories'] = $categories;
        $this->data['tables'] = $tables;
        $this->data['customers'] = DB::table('customers')->get();
        $this->data['accounts'] = DB::table('accounts')->get();
        $this->data['payment_methods'] = DB::table('payment_methods')->get();
        
        return view('pos_sale', $this->data);
    }





    public function clearCache()
    {
        $results = [];

        // Clear Laravel cache
        try {
            Cache::flush();
            $results['cache'] = 'success';
        } catch (\Exception $e) {
            $results['cache'] = 'error: ' . $e->getMessage();
        }

        // Clear config cache
        try {
            Artisan::call('config:clear');
            $results['config'] = 'success';
        } catch (\Exception $e) {
            $results['config'] = 'error: ' . $e->getMessage();
        }

        // Clear route cache
        try {
            Artisan::call('route:clear');
            $results['route'] = 'success';
        } catch (\Exception $e) {
            $results['route'] = 'error: ' . $e->getMessage();
        }

        // Clear view cache
        try {
            Artisan::call('view:clear');
            $results['view'] = 'success';
        } catch (\Exception $e) {
            $results['view'] = 'error: ' . $e->getMessage();
        }

        // Clear compiled files
        try {
            Artisan::call('clear-compiled');
            $results['compiled'] = 'success';
        } catch (\Exception $e) {
            $results['compiled'] = 'error: ' . $e->getMessage();
        }

        // Optionally, clear opcache if enabled
        if (function_exists('opcache_reset')) {
            try {
                opcache_reset();
                $results['opcache'] = 'success';
            } catch (\Exception $e) {
                $results['opcache'] = 'error: ' . $e->getMessage();
            }
        } else {
            $results['opcache'] = 'not available';
        }

        $allSuccess = collect($results)->every(function ($item) {
            return $item === 'success' || $item === 'not available';
        });

        return response()->json([
            'success' => $allSuccess,
            'results' => $results,
        ]);
    }

    /**
     * Restoran Yönetimi Ana Sayfası
     */
    public function restaurantManagement()
    {
        return view('restaurant.management', $this->data);
    }

    /**
     * Hızlı Menüler Sayfası
     */
    public function quickMenu()
    {
        return view('quick-menu', $this->data);
    }

    /**
     * Finansal İşlemler Sayfası
     */
    public function financialManagement()
    {
        // Get financial statistics
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');
        
        // Daily sales
        $dailySales = DB::table('sales')
            ->whereDate('created_at', $today)
            ->sum('total');
            
        // Monthly sales
        $monthlySales = DB::table('sales')
            ->where('created_at', 'like', $thisMonth . '%')
            ->sum('total');
            
        // Daily expenses
        $dailyExpenses = DB::table('expenses')
            ->whereDate('date', $today)
            ->sum('total');
            
        // Calculate net profit
        $netProfit = $dailySales - $dailyExpenses;
        
        // Add financial data to the data array
        $this->data['dailySales'] = $dailySales;
        $this->data['monthlySales'] = $monthlySales;
        $this->data['dailyExpenses'] = $dailyExpenses;
        $this->data['netProfit'] = $netProfit;
        
        return view('financial.management', $this->data);
    }

    /**
     * İnsan Kaynakları Yönetimi Ana Sayfası
     */
    public function hrManagement()
    {
        return view('hr.management', $this->data);
    }


}
