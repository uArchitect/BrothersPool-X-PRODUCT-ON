<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <title>Sipariş Masanda | Restaurant Management System</title>
    <meta name="description" content="Sipariş Masanda Restaurant Management Application">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Permissions-Policy" content="unload=()">

    <!-- Loading Elements -->
    <div class="progress-bar" id="progressBar"></div>
    <div class="loading-spinner" id="loadingSpinner"></div>

    <!-- Performance optimizations -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.datatables.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- DNS prefetch for faster loading -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdn.datatables.net">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">

    <!-- CSS Dosyaları -->
    <link rel="stylesheet" href="{{ asset('css/vendors.bundle.css') }}" media="screen, print">
    <link rel="stylesheet" href="{{ asset('css/app.bundle.css') }}" media="screen, print">
    <link rel="stylesheet" href="{{ asset('css/skins/skin-master.css') }}" media="screen, print">
    <link rel="stylesheet" href="{{ asset('css/datagrid/datatables/datatables.bundle.css') }}" media="screen, print">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.css" media="screen, print">
    
    <!-- CSS Fix for undefined color values -->
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        /* Fix for undefined CSS variables */
        .btn-primary { background-color: var(--primary-color, #007bff) !important; }
        .btn-secondary { background-color: var(--secondary-color, #6c757d) !important; }
        .btn-success { background-color: var(--success-color, #28a745) !important; }
        .btn-danger { background-color: var(--danger-color, #dc3545) !important; }
        .btn-warning { background-color: var(--warning-color, #ffc107) !important; }
        .btn-info { background-color: var(--info-color, #17a2b8) !important; }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .page-content .row > .col-xl-3,
            .page-content .row > .col-md-4,
            .page-content .row > .col-lg-3 {
                margin-bottom: 1rem;
            }
            .card .card-body {
                padding: 0.75rem;
            }
            .btn-group .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .page-content {
                background-color: #1a1a1a;
                color: #ffffff;
            }
            .card {
                background-color: #2d2d2d;
                border-color: #404040;
            }
        }

        /* Loading spinner */
        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Progress bar */
        .progress-bar {
            height: 4px;
            background: linear-gradient(90deg, #3498db, #2ecc71);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9998;
            width: 0%;
            transition: width 0.3s ease;
        }
    </style>

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" href="{{ asset('img/favicon/favicon.ico') }}" type="image/x-icon">
    <link rel="mask-icon" href="{{ asset('img/favicon/safari-pinned-tab.svg') }}" color="#5bbad5">

    <!-- Early fix for rgb2hex error -->
    <script>
        // Override rgb2hex function before any other scripts load
        window.rgb2hex = function(color) {
            if (!color || typeof color !== 'string' || color === 'undefined' || color === 'null' || color === '') {
                return '#000000';
            }
            try {
                if (color.startsWith('#')) {
                    return color;
                }
                if (color.startsWith('rgb(') || color.startsWith('rgba(')) {
                    const matches = color.match(/\d+/g);
                    if (matches && matches.length >= 3) {
                        const r = parseInt(matches[0]);
                        const g = parseInt(matches[1]);
                        const b = parseInt(matches[2]);
                        return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
                    }
                }
                return '#000000';
            } catch (e) {
                return '#000000';
            }
        };
    </script>
</head>

<body class="nav-function-top">
    <script>
        class ThemeManager {
            constructor() {
                this.classHolder = document.body;
                this.themeSettings = this.loadSettings();
                this.init();
            }

            loadSettings() {
                try {
                    return JSON.parse(localStorage.getItem('themeSettings')) || {};
                } catch (e) {
                    console.error('Tema ayarları yüklenirken hata:', e);
                    return {};
                }
            }

            init() {
                const {
                    themeOptions,
                    themeURL
                } = this.themeSettings;
                if (themeOptions) {
                    this.classHolder.className = themeOptions;
                    console.log("%c✔ Tema ayarları yüklendi", "color: #148f32; font-weight: bold");
                } else {
                    // Application initialized
                }
                if (themeURL && !document.getElementById('mytheme')) {
                    this.loadCustomTheme(themeURL);
                }
            }

            loadCustomTheme(url) {
                const link = document.createElement('link');
                link.id = 'mytheme';
                link.rel = 'stylesheet';
                link.href = url;
                document.head.appendChild(link);
            }

            saveSettings() {
                try {
                    this.themeSettings.themeOptions = this.classHolder.className
                        .split(/\s+/)
                        .filter(cls => /^(nav|header|footer|mod|display)-/.test(cls))
                        .join(' ');
                    const mytheme = document.getElementById('mytheme');
                    if (mytheme) this.themeSettings.themeURL = mytheme.href;
                    localStorage.setItem('themeSettings', JSON.stringify(this.themeSettings));
                } catch (e) {
                    console.error('Ayarlar kaydedilirken hata:', e);
                }
            }

            resetSettings() {
                localStorage.removeItem('themeSettings');
                location.reload();
            }
        }

        const themeManager = new ThemeManager();
        window.saveSettings = () => themeManager.saveSettings();
        window.resetSettings = () => themeManager.resetSettings();
    </script>
    <!-- BEGIN Page Wrapper -->
    <div class="page-wrapper">
        <div class="page-inner">
            <!-- BEGIN Left Aside -->
            <aside class="page-sidebar">
                <div class="page-logo">
                    <a href="{{ route('dashboard') }}" class="page-logo-link press-scale-down d-flex align-items-center position-relative">
                        <span class="page-logo-text mr-1">Sipariş Masanda</span>
                        <i class="fal fa-angle-down d-inline-block ml-1 fs-lg color-primary-300"></i>
                    </a>
                </div>

                <!-- PRIMARY NAVIGATION -->
                <nav id="js-primary-nav" class="primary-nav" role="navigation">
                    <div class="nav-filter">
                        <div class="position-relative">
                            <input type="text" id="nav_filter_input" placeholder="Menüde ara" class="form-control"
                                tabindex="0">
                            <a href="javascript:void(0);" onclick="return false;"
                                class="btn-primary btn-search-close js-waves-off" data-action="toggle"
                                data-class="list-filter-active" data-target=".page-sidebar">
                                <i class="fal fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>

                    <div class="info-card">
                        <img src="{{ asset('img/demo/avatars/avatar-admin.png') }}"
                            class="profile-image rounded-circle" alt="Profil">
                        <div class="info-card-text">
                            <a href="#" class="d-flex align-items-center text-white">
                                <span
                                    class="text-truncate text-truncate-sm d-inline-block">{{ Auth::user()->name }}</span>
                            </a>
                            <span
                                class="d-inline-block text-truncate text-truncate-sm">{{ Auth::user()->username }}</span>
                        </div>
                        <img src="{{ asset('img/card-backgrounds/cover-2-lg.png') }}" class="cover" alt="kapak">
                        <a href="javascript:void(0);" onclick="return false;" class="pull-trigger-btn"
                            data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar"
                            data-focus="nav_filter_input">
                            <i class="fal fa-angle-down"></i>
                        </a>
                    </div>
                    <ul id="js-nav-menu" class="nav-menu">
                        <!-- Dashboard -->
                        <li id="permission-dashboard">
                            <a href="{{ route('dashboard') }}" title="Dashboard" data-filter-tags="dashboard">
                                <i class="fal fa-utensils"></i>
                                <span class="nav-link-text">Ana Sayfa</span>
                            </a>
                        </li>

                        <!-- Restaurant Management -->
                        <li id="restaurant_management">
                            <a href="{{ route('restaurant.management') }}" title="Restoran Yönetimi">
                                <i class="fal fa-store"></i>
                                <span class="nav-link-text">Restoran Yönetimi</span>
                            </a>
                        </li>


                        <!-- Staff Management -->
                        @canany(['all_employees'])
                            <li id="staff_management">
                                <a href="javascript:void(0);" title="Personel Yönetimi">
                                    <i class="fal fa-users-cog"></i>
                                    <span class="nav-link-text">Personel Yönetimi</span>
                                </a>
                                <ul>
                                    @can('all_employees')
                                        <li><a href="{{ route('employees') }}" title="Personel"><span
                                                    class="nav-link-text">Personel</span></a></li>
                                    @endcan
                                    @can('all_users')
                                        <li><a href="{{ route('users') }}" title="Kullanıcılar"><span
                                                    class="nav-link-text">Kullanıcılar</span></a></li>
                                    @endcan
                                    <li><a href="{{ route('employees.specialties') }}">Pozisyonlar</a></li>
                                </ul>
                            </li>
                        @endcanany


                        <li id="payment_management">
                            <a href="{{ route('financial.management') }}" title="Finansal İşlemler">
                                <i class="fal fa-wallet"></i>
                                <span class="nav-link-text">Finansal İşlemler</span>
                            </a>
                        </li>

                        <li id="hr_management">
                            <a href="{{ route('hr.management') }}" title="İnsan Kaynakları">
                                <i class="fal fa-user-tie"></i>
                                <span class="nav-link-text">İnsan Kaynakları</span>
                            </a>
                        </li>

                        <li id="pos_management">
                            <a href="{{ route('pos') }}" title="Sipariş & Satış">
                                <i class="fal fa-cash-register"></i>
                                <span class="nav-link-text">Sipariş & Satış</span>
                            </a>
                        </li>





                        <li id="inventory_management">
                            <a href="javascript:void(0);" title="Stok Yönetimi" class="nav-link">
                                <i class="fal fa-boxes mr-1"></i>
                                <span class="nav-link-text">Stok Yönetimi</span>
                            </a>
                            <ul class="nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('warehouse') }}" class="nav-link" title="Malzeme Deposu">
                                        <span class="nav-link-text">Malzeme Deposu</span>
                                    </a>
                                </li>
                            </ul>
                        </li>



                        <li id="report_management">
                            <a href="{{ route('reports.index') }}" title="Raporlar">
                                <i class="fal fa-chart-bar"></i>
                                <span class="nav-link-text">Raporlar</span>
                            </a>
                        </li>


                        <li id="settings">
                            <a href="{{ route('settings') }}" title="Ayarlar">
                                <i class="fal fa-cogs"></i>
                                <span class="nav-link-text">Ayarlar</span>
                            </a>
                        </li>
                    </ul>

                </nav>
            </aside>

            <div class="page-content-wrapper">
                <header class="page-header" role="banner">
                    <div class="page-logo">
                        <a href="{{ route('dashboard') }}" class="page-logo-link d-flex align-items-center position-relative">
                            <span class="page-logo-text" style="
                                font-family: 'Arial', sans-serif;
                                font-weight: 700;
                                font-size: 1.25rem;
                                color: #ffffff !important;
                                letter-spacing: -0.5px;
                                text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
                                transition: all 0.3s ease;
                            ">
                                Sipariş Masanda
                                <small class="d-block mt-1" style="
                                    font-size: 0.65rem;
                                    font-weight: 600;
                                    color: #ffffff !important;
                                    letter-spacing: 1.5px;
                                    text-transform: uppercase;
                                    transition: transform 0.2s ease;
                                ">
                                    Restaurant Management
                                </small>
                            </span>
                        </a>
                    </div>

                    <style>
                        .page-logo-link {
                            display: flex;
                            align-items: center;
                            position: relative;
                            transition: transform 0.2s ease;
                        }

                        .page-logo-link:active {
                            transform: scale(0.98);
                        }

                        .page-logo-text:hover {
                            color: #ffffff !important;
                            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.5);
                        }

                        .nav-menu li ul li a,
                        .nav-menu li ul li a span {
                            color: #ffffff !important;
                        }

                        .nav-menu li ul li a:hover,
                        .nav-menu li ul li a:hover span {
                            color: #ffffff !important;
                        }
                    </style>
                    <div class="hidden-md-down dropdown-icon-menu position-relative">
                        <a href="javascript:void(0);" class="header-btn btn js-waves-off" data-action="toggle"
                            data-class="nav-function-hidden" title="Menüyü Gizle">
                            <i class="ni ni-menu"></i>
                        </a>
                        <ul>
                            <li>
                                <a href="javascript:void(0);" class="btn js-waves-off" data-action="toggle"
                                    data-class="nav-function-minify" title="Menüyü Küçült">
                                    <i class="ni ni-minify-nav"></i>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="btn js-waves-off" data-action="toggle"
                                    data-class="nav-function-fixed" title="Menüyü Sabitle">
                                    <i class="ni ni-lock-nav"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="hidden-lg-up">
                        <a href="javascript:void(0);" class="header-btn btn press-scale-down" data-action="toggle"
                            data-class="mobile-nav-on">
                            <i class="ni ni-menu"></i>
                        </a>
                    </div>
                    <div class="ml-auto d-flex">
                        <div>
                            <a href="javascript:void(0);" class="header-icon" id="clearCacheBtn" title="Cache Sıfırla">
                                <i class="fal fa-broom"></i> <!-- Cache sıfırlama için daha anlamlı bir ikon: broom (süpürge) -->
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('quick.menu') }}" class="header-icon" title="Hızlı Menüler">
                                <i class="fal fa-bolt"></i> <!-- Hızlı erişim için bolt ikonu -->
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('possales') }}" class="header-icon" title="POS Yönetimi">
                                <i class="fal fa-credit-card"></i> <!-- POS ile uyumlu başka bir ikon -->
                            </a>
                        </div>
                 
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                var clearCacheBtn = document.getElementById('clearCacheBtn');
                                if (clearCacheBtn) {
                                    clearCacheBtn.addEventListener('click', function () {
                                        if (confirm('Tüm önbelleği sıfırlamak istediğinize emin misiniz?')) {
                                            fetch('{{ route('clear.cache') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Accept': 'application/json'
                                                }
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    alert('Cache başarıyla sıfırlandı!');
                                                    location.reload();
                                                } else {
                                                    alert('Cache sıfırlanırken bir hata oluştu.');
                                                }
                                            })
                                            .catch(() => {
                                                alert('Cache sıfırlanırken bir hata oluştu.');
                                            });
                                        }
                                    });
                                }
                            });
                        </script>

                        <style>
                            .app-list {
                                margin: 0 auto;
                                display: block;
                                width: 21.875rem !important;
                                height: auto !important;
                                font-size: 0;
                                padding: 0.5rem 1rem;
                                text-align: left;
                                -webkit-user-select: none;
                                -moz-user-select: none;
                                -ms-user-select: none;
                                user-select: none;
                            }

                            /* Bildirim Tasarımı */
                            .notification-item {
                                border: none;
                                margin-bottom: 0.75rem;
                                border-radius: 8px;
                                transition: all 0.3s ease;
                                background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
                                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                            }

                            .notification-item:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                            }

                            .low-stock-notification {
                                border-left: 4px solid #ffc107;
                                background: linear-gradient(135deg, #fff 0%, #fff3cd 100%);
                            }

                            .notification-content {
                                padding: 1rem;
                            }

                            .notification-header {
                                display: flex;
                                align-items: center;
                                margin-bottom: 0.75rem;
                                gap: 0.75rem;
                            }

                            .notification-icon {
                                width: 40px;
                                height: 40px;
                                border-radius: 50%;
                                background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                flex-shrink: 0;
                            }

                            .notification-icon i {
                                font-size: 1.2rem;
                                color: #fff;
                            }

                            .notification-title {
                                flex: 1;
                                font-size: 1rem;
                                font-weight: 600;
                                color: #2c3e50;
                            }

                            .notification-time {
                                font-size: 0.75rem;
                                color: #6c757d;
                            }

                            .notification-body {
                                margin-left: 55px;
                            }

                            .notification-message {
                                margin-bottom: 0.75rem;
                                font-size: 0.9rem;
                                line-height: 1.6;
                                color: #495057;
                                background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 249, 250, 0.9) 100%);
                                padding: 1rem;
                                border-radius: 8px;
                                border-left: 4px solid #17a2b8;
                                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                                position: relative;
                            }

                            .notification-message::before {
                                content: '';
                                position: absolute;
                                top: 0;
                                left: 0;
                                right: 0;
                                bottom: 0;
                                background: linear-gradient(45deg, transparent 30%, rgba(23, 162, 184, 0.05) 50%, transparent 70%);
                                border-radius: 8px;
                                pointer-events: none;
                            }

                            .notification-actions {
                                display: flex;
                                align-items: center;
                                justify-content: flex-start;
                                flex-wrap: wrap;
                                gap: 0.5rem;
                            }

                            .notification-actions .badge {
                                font-size: 0.75rem;
                                padding: 0.4rem 0.6rem;
                                border-radius: 20px;
                                background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
                                color: #fff;
                                border: none;
                            }

                            .notification-actions .btn {
                                font-size: 0.8rem;
                                padding: 0.3rem 0.6rem;
                                border-radius: 6px;
                                transition: all 0.3s ease;
                            }

                            .notification-actions .btn:hover {
                                transform: translateY(-1px);
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                            }

                            /* Dropdown menü genişliği */
                            .dropdown-xl {
                                min-width: 400px !important;
                                max-width: 500px !important;
                            }

                            /* Bildirim sayacı animasyonu */
                            .badge-icon {
                                animation: pulse 2s infinite;
                                transition: all 0.3s ease;
                            }

                            @keyframes pulse {
                                0% {
                                    transform: scale(1);
                                    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
                                }

                                70% {
                                    transform: scale(1.05);
                                    box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
                                }

                                100% {
                                    transform: scale(1);
                                    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
                                }
                            }

                            .badge-icon:hover {
                                animation-play-state: paused;
                                transform: scale(1.1);
                            }

                            /* Responsive tasarım */
                            @media (max-width: 768px) {
                                .notification-header {
                                    flex-direction: column;
                                    align-items: flex-start;
                                    gap: 0.5rem;
                                }

                                .notification-body {
                                    margin-left: 0;
                                }

                                .notification-actions {
                                    flex-direction: column;
                                    align-items: stretch;
                                }

                                .dropdown-xl {
                                    min-width: 300px !important;
                                    max-width: 350px !important;
                                }
                            }
                        </style>


                        <!---Pos management--->

                        <div>
                            <a href="javascript:void(0);" class="header-icon" data-toggle="dropdown"
                                title="Hızlı Erişim">
                                <i class="fal fa-rocket"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated w-auto h-auto">
                                <div
                                    class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center rounded-top">
                                    <h4 class="m-0 text-center color-white">
                                        Hızlı Erişim
                                        <small class="mb-0 opacity-80">Kısayollar</small>
                                    </h4>
                                </div>
                                <div class="custom-scroll h-100">
                                    <ul class="app-list">
                                        <!-- Hızlı Satış -->
                                        <li>
                                            <a href="javascript:void(0);" class="app-list-item hover-white"
                                                data-toggle="modal" data-target="#quickSaleModal">
                                                <span class="icon-stack">
                                                    <i class="base-2 icon-stack-3x color-primary-600"></i>
                                                    <i class="base-3 icon-stack-2x color-primary-700"></i>
                                                    <i class="fal fa-calendar-plus icon-stack-1x text-white fs-lg"></i>
                                                </span>
                                                <span class="app-list-name">Hızlı Satış</span>
                                            </a>
                                        </li>

                                        <!-- Hızlı Randevu -->
                                        <li>
                                            <a href="javascript:void(0);" class="app-list-item hover-white"
                                                data-toggle="modal" data-target="#quickAppointmentModal">
                                                <span class="icon-stack">
                                                    <i class="base-2 icon-stack-3x color-info-600"></i>
                                                    <i class="base-3 icon-stack-2x color-info-700"></i>
                                                    <i class="fal fa-calendar-plus icon-stack-1x text-white fs-lg"></i>
                                                </span>
                                                <span class="app-list-name">Hızlı Randevu</span>
                                            </a>
                                        </li>

                                        <!-- Hızlı Gider -->
                                        <li>
                                            <a href="javascript:void(0);" class="app-list-item hover-white"
                                                data-toggle="modal" data-target="#quickExpenseModal">
                                                <span class="icon-stack">
                                                    <i class="base-2 icon-stack-3x color-danger-600"></i>
                                                    <i class="base-3 icon-stack-2x color-danger-700"></i>
                                                    <i class="fal fa-minus-circle icon-stack-1x text-white fs-lg"></i>
                                                </span>
                                                <span class="app-list-name">Hızlı Gider</span>
                                            </a>
                                        </li>

                                        <!-- Hızlı Gelir -->
                                        <li>
                                            <a href="javascript:void(0);" class="app-list-item hover-white"
                                                data-toggle="modal" data-target="#quickIncomeModal">
                                                <span class="icon-stack">
                                                    <i class="base-2 icon-stack-3x color-success-600"></i>
                                                    <i class="base-3 icon-stack-2x color-success-700"></i>
                                                    <i class="fal fa-plus-circle icon-stack-1x text-white fs-lg"></i>
                                                </span>
                                                <span class="app-list-name">Hızlı Gelir</span>
                                            </a>
                                        </li>

                                        <!-- Finansal İşlemler -->
                                        <li>
                                            <a href="{{ route('financial.management') }}" class="app-list-item hover-white">
                                                <span class="icon-stack">
                                                    <i class="base-2 icon-stack-3x color-warning-600"></i>
                                                    <i class="base-3 icon-stack-2x color-warning-700"></i>
                                                    <i class="fal fa-wallet icon-stack-1x text-white fs-lg"></i>
                                                </span>
                                                <span class="app-list-name">Finansal İşlemler</span>
                                            </a>
                                        </li>

                                        <!-- Müşteri Yönetimi -->
                                        <li>
                                            <a href="{{ route('customers.index') }}" class="app-list-item hover-white">
                                                <span class="icon-stack">
                                                    <i class="base-2 icon-stack-3x color-secondary-600"></i>
                                                    <i class="base-3 icon-stack-2x color-secondary-700"></i>
                                                    <i class="fal fa-users icon-stack-1x text-white fs-lg"></i>
                                                </span>
                                                <span class="app-list-name">Müşteri Yönetimi</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Bildirimler (Dinamik) -->
                        <div id="notification-area">
                            <a href="javascript:void(0);" class="header-icon" data-toggle="dropdown"
                                id="notificationBell" title="Yeni Bildiriminiz Var">
                                <i class="fal fa-bell"></i>
                                <span class="badge badge-icon"
                                    id="notificationCount">{{ count($low_stock_products ?? []) }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated dropdown-xl"
                                style="min-width: 450px; max-width: 550px;">
                                <div
                                    class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center rounded-top mb-2">
                                    <h4 class="m-0 text-center color-white">
                                        <i class="fal fa-bell mr-2"></i>
                                        <span id="notificationCountText">{{ count($low_stock_products ?? []) }}</span>
                                        Bildirim
                                        <small class="mb-0 opacity-80 d-block">Stok Uyarıları</small>
                                    </h4>
                                </div>
                                <div class="tab-content tab-notification p-3"
                                    style="max-height: 400px; overflow-y: auto;">
                                    <ul class="notification" id="notificationList">

                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div>
                            <a href="javascript:void(0);" data-toggle="dropdown" title="{{ Auth::user()->email }}"
                                class="header-icon d-flex align-items-center justify-content-center ml-2">
                                <i class="fal fa-user-circle" style="font-size: 21px"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated dropdown-lg">
                                <div class="dropdown-header bg-trans-gradient d-flex flex-row py-4 rounded-top">
                                    <div class="d-flex flex-row align-items-center mt-1 mb-1 color-white">
                                        <span class="mr-2">
                                            <i class="fal fa-user-circle fa-3x"></i>
                                        </span>
                                        <div class="info-card-text">
                                            <div class="fs-lg text-truncate text-truncate-lg font-weight-bold">
                                                {{ Auth::user()->name }}
                                            </div>
                                            <span class="text-truncate text-truncate-md opacity-80">
                                                <i class="fal fa-envelope mr-1"></i>{{ Auth::user()->email }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="dropdown-divider m-0"></div>

                                <a href="javascript:void(0);" class="dropdown-item py-2"
                                    data-action="sistem-kalan-gun">
                                    <i class="fal fa-calendar-alt mr-2 text-primary"></i>
                                    <span>Kalan Süre</span>
                                    <span class="badge badge-primary ml-auto">
                                        @php
                                            use Carbon\Carbon;
                                            $bugun = Carbon::now();
                                            $bitisTarihi = Carbon::now()->addDays(30);
                                            $kalanGun = $bugun->diffInDays($bitisTarihi, false);
                                            echo $kalanGun < 0 ? 'Süresi Doldu' : intval($kalanGun) . ' Gün';
                                        @endphp
                                    </span>
                                </a>

                                <a href="javascript:void(0);" class="dropdown-item py-2" data-action="kalan-sms">
                                    <i class="fal fa-comment-alt mr-2 text-info"></i>
                                    <span>SMS Bakiye</span>
                                    <span class="badge badge-info ml-auto">{{ $settings->remaining_sms_limit ?? 0 }}</span>
                                </a>

                                <!---Paket Adı Bilgisi-->
                                <a href="javascript:void(0);" class="dropdown-item py-2" data-action="paket-ad">
                                    <i class="fal fa-box mr-2 text-warning"></i>
                                    <span>Paket Adı</span>
                                    @if (true)
                                        <span class="badge badge-warning ml-auto"
                                            style="background-color: #ffc107; color: #212529;">Başlangıç</span>
                                    @else
                                        <span class="badge badge-warning ml-auto"
                                            style="background-color: #28a745; color: #fff;">Pro</span>
                                    @endif
                                </a>

                                <div class="dropdown-divider m-0"></div>

                                <a href="{{ route('settings') }}" class="dropdown-item py-2">
                                    <i class="fal fa-cog mr-2 text-dark"></i>
                                    <span>Ayarlar</span>
                                </a>

                                <div class="dropdown-divider m-0"></div>

                                <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}">
                                    <i class="fal fa-sign-out-alt mr-2"></i>
                                    <span>Çıkış Yap</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </header>

                <script>
                    // PHP'den gelen düşük stok verilerini JavaScript'e aktar
                    window.lowStockProducts = @json($low_stock_products ?? []);
                    window.notifications = [
                        // Başlangıçta boş, örnek: {id: 1, ...}
                    ];

                    function renderNotifications() {
                        const list = document.getElementById('notificationList');
                        const lowStockCount = window.lowStockProducts.length;
                        const notificationCount = window.notifications.length;
                        const totalCount = lowStockCount + notificationCount;

                        document.getElementById('notificationCount').textContent = totalCount;
                        document.getElementById('notificationCountText').textContent = totalCount;
                        list.innerHTML = '';

                        // Düşük stok ürünlerini ekle
                        window.lowStockProducts.forEach(product => {
                            const li = document.createElement('li');
                            li.className = 'notification-item low-stock-notification';
                            li.innerHTML = `
                                <div class="notification-content">
                                    <div class="notification-header">
                                        <div class="notification-icon">
                                            <i class="fal fa-exclamation-triangle text-warning"></i>
                                        </div>
                                        <div class="notification-title">
                                            <strong>${product.name}</strong>
                                        </div>
                                        <div class="notification-time">
                                            <small class="text-muted">Şimdi</small>
                                        </div>
                                    </div>
                                    <div class="notification-body">
                                        <p class="notification-message">
                                            <i class="fal fa-box text-info mr-1"></i>
                                            <strong>${product.name}</strong> ürününün mevcut stok miktarı <strong>${product.stock}</strong> adet olarak tespit edilmiştir. 
                                            <br><small class="text-muted mt-1 d-block">
                                                <i class="fal fa-info-circle mr-1"></i>
                                                Bu seviye, belirlenen kritik stok eşiğinin altındadır ve acil stok takviyesi gerektirmektedir.
                                            </small>
                                        </p>
                                        <div class="notification-actions">
                                            <span class="badge badge-warning">
                                                <i class="fal fa-exclamation-circle mr-1"></i>
                                                Kritik Stok Seviyesi
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            `;
                            list.appendChild(li);
                        });

                        // Diğer bildirimleri ekle
                        window.notifications.forEach(n => {
                            const li = document.createElement('li');
                            li.className = n.unread ? 'unread' : '';
                            li.innerHTML = `
                            <div class="d-flex align-items-center show-child-on-hover">
                                <span class="d-flex flex-column flex-1">
                                    <span class="name d-flex align-items-center">
                                        ${n.user}
                                        <span class="badge badge-success fw-n ml-1">${n.type}</span>
                                    </span>
                                    <span class="msg-a fs-sm">${n.message}</span>
                                    <span class="fs-nano text-muted mt-1">${n.time}</span>
                                </span>
                                <div class="show-on-hover-parent position-absolute pos-right pos-bottom p-3">
                                    <a href="#" class="text-muted notification-delete" data-id="${n.id}" title="Sil">
                                        <i class="fal fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>
                        `;
                            list.appendChild(li);
                        });

                        // Eğer hiç bildirim yoksa
                        if (totalCount === 0) {
                            list.innerHTML = `
                                <li class="notification-item">
                                    <div class="notification-content text-center">
                                        <div class="notification-icon mx-auto mb-3" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                                            <i class="fal fa-check-circle"></i>
                                        </div>
                                        <h6 class="text-muted mb-2">Tüm Stoklar Normal</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                            Şu anda kritik stok seviyesinde olan ürün bulunmamaktadır.
                                        </p>
                                    </div>
                                </li>`;
                            return;
                        }

                        document.querySelectorAll('.notification-delete').forEach(btn => {
                            btn.addEventListener('click', function(e) {
                                e.preventDefault();
                                const id = parseInt(this.getAttribute('data-id'));
                                window.notifications = window.notifications.filter(n => n.id !== id);
                                renderNotifications();
                            });
                        });
                    }
                    document.addEventListener('DOMContentLoaded', renderNotifications);
                </script>

                <!-- Hızlı Erişim AJAX -->
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Hızlı erişim modal'ları açıldığında veri yükle
                    $('#quickExpenseModal').on('show.bs.modal', function() {
                        loadQuickAccessData();
                    });
                    
                    $('#quickIncomeModal').on('show.bs.modal', function() {
                        loadQuickAccessData();
                    });
                    
                    function loadQuickAccessData() {
                        fetch('{{ route("quick.access.data") }}')
                            .then(response => response.json())
                            .then(data => {
                                // Gider tiplerini yükle
                                if (data.expense_types) {
                                    const expenseTypeSelect = document.getElementById('quick_expense_type');
                                    if (expenseTypeSelect) {
                                        expenseTypeSelect.innerHTML = '<option value="">Seçiniz...</option>';
                                        data.expense_types.forEach(type => {
                                            const option = document.createElement('option');
                                            option.value = type.id;
                                            option.textContent = type.name;
                                            expenseTypeSelect.appendChild(option);
                                        });
                                    }
                                }
                                
                                // Hesapları yükle
                                if (data.accounts) {
                                    // Gider hesapları
                                    const expenseAccountSelect = document.getElementById('quick_expense_account');
                                    if (expenseAccountSelect) {
                                        expenseAccountSelect.innerHTML = '<option value="">Seçiniz...</option>';
                                        data.accounts.forEach(account => {
                                            const option = document.createElement('option');
                                            option.value = account.id;
                                            option.textContent = account.name;
                                            expenseAccountSelect.appendChild(option);
                                        });
                                    }
                                    
                                    // Gelir hesapları
                                    const incomeAccountSelect = document.getElementById('quick_income_account');
                                    if (incomeAccountSelect) {
                                        incomeAccountSelect.innerHTML = '<option value="">Hesap seçiniz</option>';
                                        data.accounts.forEach(account => {
                                            const option = document.createElement('option');
                                            option.value = account.id;
                                            option.textContent = account.name;
                                            incomeAccountSelect.appendChild(option);
                                        });
                                    }
                                }
                                
                                // Müşterileri yükle
                                if (data.customers) {
                                    const customerSelects = [
                                        'quick_expense_customer',
                                        'quick_income_customer'
                                    ];
                                    
                                    customerSelects.forEach(selectId => {
                                        const select = document.getElementById(selectId);
                                        if (select) {
                                            select.innerHTML = '<option value="">Müşteri seçiniz (opsiyonel)</option>';
                                            data.customers.forEach(customer => {
                                                const option = document.createElement('option');
                                                option.value = customer.id;
                                                let text = customer.title || customer.code || 'Müşteri';
                                                if (customer.current_balance > 0) {
                                                    text += ` (Alacağım: ₺${parseFloat(customer.current_balance).toFixed(2)})`;
                                                } else if (customer.current_balance < 0) {
                                                    text += ` (Borç: ₺${Math.abs(parseFloat(customer.current_balance)).toFixed(2)})`;
                                                }
                                                option.textContent = text;
                                                select.appendChild(option);
                                            });
                                        }
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Hızlı erişim verileri yüklenirken hata:', error);
                            });
                    }
                });
                </script>

                <!-- Hızlı Gider Modal -->
                <div class="modal fade" id="quickExpenseModal" tabindex="-1" role="dialog" aria-labelledby="quickExpenseModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="quickExpenseModalLabel">
                                    <i class="fal fa-minus-circle mr-2"></i>Hızlı Gider Girişi
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="quickExpenseForm" method="POST" action="{{ route('quick.expense') }}"> 
                                @csrf
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="quick_expense_type">Gider Tipi <span class="text-danger">*</span></label>
                                                <select class="form-control" id="quick_expense_type" name="expense_type_id" required>
                                                    <option value="">Seçiniz...</option>
                                                    @if(isset($expense_types))
                                                        @foreach($expense_types as $type)
                                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="quick_expense_amount">Tutar <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">₺</span>
                                                    </div>
                                                    <input type="number" class="form-control" id="quick_expense_amount" name="amount" step="0.01" min="0" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="quick_expense_account">Hesap <span class="text-danger">*</span></label>
                                                <select class="form-control" id="quick_expense_account" name="account_id" required>
                                                    <option value="">Seçiniz...</option>
                                                    @if(isset($accounts))
                                                        @foreach($accounts as $account)
                                                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="quick_expense_customer">Müşteri <span class="text-danger">*</span></label>
                                                <select class="form-control" id="quick_expense_customer" name="customer_id" required>
                                                    <option value="">Müşteri seçiniz</option>
                                                    @if(isset($customers))
                                                        @foreach($customers as $customer)
                                                            <option value="{{ $customer->id }}">
                                                                {{ $customer->title ?? $customer->code ?? 'Müşteri' }}
                                                                @if($customer->current_balance > 0)
                                                                    (Alacağım: ₺{{ number_format($customer->current_balance, 2) }})
                                                                @elseif($customer->current_balance < 0)
                                                                    (Borç: ₺{{ number_format(abs($customer->current_balance), 2) }})
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="quick_expense_description">Açıklama</label>
                                        <textarea class="form-control" id="quick_expense_description" name="description" rows="3" placeholder="Gider açıklaması..."></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="quick_expense_date">Tarih</label>
                                        <input type="date" class="form-control" id="quick_expense_date" name="date" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <!-- Hidden fields for expense form -->
                                    <input type="hidden" name="expense_category_id[]" value="1">
                                    <input type="hidden" name="expense[]" value="Hızlı Gider">
                                    <input type="hidden" name="description[]" value="">
                                    <input type="hidden" name="expense_number" value="HIZ-{{ date('YmdHis') }}">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fal fa-save mr-1"></i>Gider Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Hızlı Gelir Modal -->
                <div class="modal fade" id="quickIncomeModal" tabindex="-1" role="dialog" aria-labelledby="quickIncomeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title" id="quickIncomeModalLabel">
                                    <i class="fal fa-plus-circle mr-2"></i>Hızlı Gelir Girişi
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="quickIncomeForm" method="POST" action="{{ route('quick.income') }}">
                                @csrf
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="quick_income_customer">Müşteri <span class="text-danger">*</span></label>
                                                <select class="form-control" id="quick_income_customer" name="customer_id" required>
                                                    <option value="">Müşteri seçiniz</option>
                                                    @if(isset($customers))
                                                        @foreach($customers as $customer)
                                                            <option value="{{ $customer->id }}">
                                                                {{ $customer->title ?? $customer->code ?? 'Müşteri' }}
                                                                @if($customer->current_balance > 0)
                                                                    (Alacağım: ₺{{ number_format($customer->current_balance, 2) }})
                                                                @elseif($customer->current_balance < 0)
                                                                    (Borç: ₺{{ number_format(abs($customer->current_balance), 2) }})
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="quick_income_amount">Tutar <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">₺</span>
                                                    </div>
                                                    <input type="number" class="form-control" id="quick_income_amount" name="amount" step="0.01" min="0" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="quick_income_account">Hesap</label>
                                                <select class="form-control" id="quick_income_account" name="account_id" required>
                                                    <option value="">Hesap seçiniz</option>
                                                    @if(isset($accounts))
                                                        @foreach($accounts as $account)
                                                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="quick_income_date">Tarih</label>
                                                <input type="date" class="form-control" id="quick_income_date" name="date" value="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="quick_income_description">Açıklama</label>
                                        <textarea class="form-control" id="quick_income_description" name="description" rows="3" placeholder="Gelir açıklaması..."></textarea>
                                    </div>
                                    <input type="hidden" name="type" value="Gelir">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fal fa-save mr-1"></i>Gelir Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
