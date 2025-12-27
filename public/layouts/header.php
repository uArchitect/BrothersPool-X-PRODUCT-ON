<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>
       En İyi Salon App  | Güzellik Salonu Yönetim Sistemi
    </title>
    <meta name="description" content="Page Title">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="msapplication-tap-highlight" content="no">
    <link id="vendorsbundle" rel="stylesheet" media="screen, print" href="css/vendors.bundle.css">
    <link id="appbundle" rel="stylesheet" media="screen, print" href="css/app.bundle.css">
    <link id="mytheme" rel="stylesheet" media="screen, print" href="#">
    <link id="myskin" rel="stylesheet" media="screen, print" href="css/skins/skin-master.css">
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
    <link rel="mask-icon" href="img/favicon/safari-pinned-tab.svg" color="#5bbad5">

</head>

<body class="nav-function-top">
    <script>
        'use strict';

        var classHolder = document.getElementsByTagName("BODY")[0],

            themeSettings = (localStorage.getItem('themeSettings')) ? JSON.parse(localStorage.getItem('themeSettings')) : {},
            themeURL = themeSettings.themeURL || '',
            themeOptions = themeSettings.themeOptions || '';

        if (themeSettings.themeOptions) {
            classHolder.className = themeSettings.themeOptions;
            console.log("%c✔ Theme settings loaded", "color: #148f32");
        } else {
            console.log("%c✔ Heads up! Theme settings is empty or does not exist, loading default settings...", "color: #ed1c24");
        }
        if (themeSettings.themeURL && !document.getElementById('mytheme')) {
            var cssfile = document.createElement('link');
            cssfile.id = 'mytheme';
            cssfile.rel = 'stylesheet';
            cssfile.href = themeURL;
            document.getElementsByTagName('head')[0].appendChild(cssfile);

        } else if (themeSettings.themeURL && document.getElementById('mytheme')) {
            document.getElementById('mytheme').href = themeSettings.themeURL;
        }

        var saveSettings = function() {
            themeSettings.themeOptions = String(classHolder.className).split(/[^\w-]+/).filter(function(item) {
                return /^(nav|header|footer|mod|display)-/i.test(item);
            }).join(' ');
            if (document.getElementById('mytheme')) {
                themeSettings.themeURL = document.getElementById('mytheme').getAttribute("href");
            };
            localStorage.setItem('themeSettings', JSON.stringify(themeSettings));
        }

        var resetSettings = function() {
            localStorage.setItem("themeSettings", "");
        }
    </script>
    <div class="page-wrapper">
        <div class="page-inner">
            <aside class="page-sidebar">
                <div class="page-logo">
                    <a href="#" class="page-logo-link press-scale-down d-flex align-items-center position-relative" data-toggle="modal" data-target="#modal-shortcut">
                        <img src="img/logo.png" alt="SmartAdmin WebApp" aria-roledescription="logo">
                        <span class="page-logo-text mr-1">SmartAdmin WebApp</span>
                        <span class="position-absolute text-white opacity-50 small pos-top pos-right mr-2 mt-n2"></span>
                        <i class="fal fa-angle-down d-inline-block ml-1 fs-lg color-primary-300"></i>
                    </a>
                </div>
                <!-- BEGIN PRIMARY NAVIGATION -->
                <nav id="js-primary-nav" class="primary-nav" role="navigation">
                    <div class="nav-filter">
                        <div class="position-relative">
                            <input type="text" id="nav_filter_input" placeholder="Filter menu" class="form-control" tabindex="0">
                            <a href="#" onclick="return false;" class="btn-primary btn-search-close js-waves-off" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar">
                                <i class="fal fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="info-card">
                        <img src="img/demo/avatars/avatar-admin.png" class="profile-image rounded-circle" alt="Dr. Codex Lantern">
                        <div class="info-card-text">
                            <a href="#" class="d-flex align-items-center text-white">
                                <span class="text-truncate text-truncate-sm d-inline-block">
                                    Dr. Codex Lantern
                                </span>
                            </a>
                            <span class="d-inline-block text-truncate text-truncate-sm">Toronto, Canada</span>
                        </div>
                        <img src="img/card-backgrounds/cover-2-lg.png" class="cover" alt="cover">
                        <a href="#" onclick="return false;" class="pull-trigger-btn" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar" data-focus="nav_filter_input">
                            <i class="fal fa-angle-down"></i>
                        </a>
                    </div>

                    <ul id="js-nav-menu" class="nav-menu">
                        <li class="active">
                            <a href="blank.html" title="Blank Project" data-filter-tags="blank page">
                                <i class="fal fa-globe"></i>
                                <span class="nav-link-text" data-i18n="nav.blankpage">Blank Project</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.html" title="Dashboard" data-filter-tags="dashboard">
                                <i class="fal fa-tachometer-alt-fast"></i>
                                <span class="nav-link-text" data-i18n="nav.dashboard">Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0);" title="Randevular" data-filter-tags="randevular">
                                <i class="fal fa-calendar-alt"></i>
                                <span class="nav-link-text" data-i18n="nav.calendar">Randevular</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="randevular.html" title="Randevular" data-filter-tags="randevular">
                                        <span class="nav-link-text" data-i18n="nav.calendar">Randevular</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="randevu-takvimi.html" title="Randevu Takvimi" data-filter-tags="randevu takvimi">
                                        <span class="nav-link-text" data-i18n="nav.calendar">Randevu Takvimi</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="baslayan-randevular.html" title="Başlayan Randevular" data-filter-tags="başlayan randevular">
                                        <span class="nav-link-text" data-i18n="nav.calendar">Başlayan Randevular</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="tamamlanan-randevular.html" title="Tamamlanan Randevular" data-filter-tags="tamamlanan randevular">
                                        <span class="nav-link-text" data-i18n="nav.calendar">Tamamlanan Randevular</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="iptal-edilen-randevular.html" title="İptal Edilen Randevular" data-filter-tags="iptal edilen randevular">
                                        <span class="nav-link-text" data-i18n="nav.calendar">İptal Edilen Randevular</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="musteriler.html" title="Müşteriler" data-filter-tags="müşteriler">
                                <i class="fal fa-users"></i>
                                <span class="nav-link-text" data-i18n="nav.calendar">Müşteriler</span>
                            </a>
                        </li>

                        <li>
                            <a href="hizmetler.html" title="Hizmetler" data-filter-tags="hizmetler">
                                <i class="fal fa-hand-holding-heart"></i>
                                <span class="nav-link-text" data-i18n="nav.calendar">Hizmetler</span>
                            </a>
                        </li>

                        <li>
                            <a href="kampanyalar.html" title="Kampanyalar" data-filter-tags="kampanyalar">
                                <i class="fal fa-gift"></i>
                                <span class="nav-link-text" data-i18n="nav.calendar">Kampanyalar</span>
                            </a>
                        </li>

                        <li>
                            <a href="geri-bildirimler.html" title="Geri Bildirimler" data-filter-tags="geri bildirimler">
                                <i class="fal fa-comment-alt"></i>
                                <span class="nav-link-text" data-i18n="nav.calendar">Geri Bildirimler</span>
                            </a>
                        </li>

                        <li>
                            <a href="giderler.html" title="Giderler" data-filter-tags="giderler">
                                <i class="fal fa-money-bill-wave"></i>
                                <span class="nav-link-text" data-i18n="nav.calendar">Giderler</span>
                            </a>
                        </li>

                        <!--- Banka / Kasa-->
                        <li>
                            <a href="banka-kasa.html" title="Banka / Kasa" data-filter-tags="banka kasa">
                                <i class="fal fa-wallet"></i>
                                <span class="nav-link-text" data-i18n="nav.calendar">Banka / Kasa</span>
                            </a>
                        </li>

                        <!--- Çalışanlar-->
                        <li>
                            <a href="calisanlar.html" title="Çalışanlar" data-filter-tags="çalışanlar">
                                <i class="fal fa-user-tie"></i>
                                <span class="nav-link-text" data-i18n="nav.calendar">Çalışanlar</span>
                            </a>
                        </li>

                        <!--- Kullanıcılar-->
                        <li>
                            <a href="kullanicilar.html" title="Kullanıcılar" data-filter-tags="kullanıcılar">
                                <i class="fal fa-user"></i>
                                <span class="nav-link-text" data-i18n="nav.calendar">Kullanıcılar</span>
                            </a>
                        </li>

                        <!--- Ayarlar-->
                        <li>
                            <a href="ayarlar.html" title="Ayarlar" data-filter-tags="ayarlar">
                                <i class="fal fa-cog fa-spin"></i>
                                <span class="nav-link-text" data-i18n="nav.calendar">Ayarlar</span>
                            </a>
                        </li>


                    </ul>
                    <div class="filter-message js-filter-message bg-success-600"></div>
                </nav>

                <div class="nav-footer shadow-top">
                    <a href="#" onclick="return false;" data-action="toggle" data-class="nav-function-minify" class="hidden-md-down">
                        <i class="ni ni-chevron-right"></i>
                        <i class="ni ni-chevron-right"></i>
                    </a>
                    <ul class="list-table m-auto nav-footer-buttons">
                        <li>
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Chat logs">
                                <i class="fal fa-comments"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Support Chat">
                                <i class="fal fa-life-ring"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Make a call">
                                <i class="fal fa-phone"></i>
                            </a>
                        </li>
                    </ul>
                </div> <!-- END NAV FOOTER -->
            </aside>
            <!-- END Left Aside -->
            <div class="page-content-wrapper">
                <!-- BEGIN Page Header -->
                <header class="page-header" role="banner">
                    <!-- we need this logo when user switches to nav-function-top -->
                    <div class="page-logo">
                        <a href="#" class="page-logo-link press-scale-down d-flex align-items-center position-relative" data-toggle="modal" data-target="#modal-shortcut">
                            <img src="img/logo.png" alt="SmartAdmin WebApp" aria-roledescription="logo">
                            <span class="page-logo-text mr-1">En İyi Salon App </span>
                            <span class="position-absolute text-white opacity-50 small pos-top pos-right mr-2 mt-n2"></span>
                            <i class="fal fa-angle-down d-inline-block ml-1 fs-lg color-primary-300"></i>
                        </a>
                    </div>
                    <!-- DOC: nav menu layout change shortcut -->
                    <div class="hidden-md-down dropdown-icon-menu position-relative">
                        <a href="#" class="header-btn btn js-waves-off" data-action="toggle" data-class="nav-function-hidden" title="Hide Navigation">
                            <i class="ni ni-menu"></i>
                        </a>
                        <ul>
                            <li>
                                <a href="#" class="btn js-waves-off" data-action="toggle" data-class="nav-function-minify" title="Minify Navigation">
                                    <i class="ni ni-minify-nav"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn js-waves-off" data-action="toggle" data-class="nav-function-fixed" title="Lock Navigation">
                                    <i class="ni ni-lock-nav"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- DOC: mobile button appears during mobile width -->
                    <div class="hidden-lg-up">
                        <a href="#" class="header-btn btn press-scale-down" data-action="toggle" data-class="mobile-nav-on">
                            <i class="ni ni-menu"></i>
                        </a>
                    </div>
                    <div class="search">
                        <form class="app-forms hidden-xs-down" role="search" action="page_search.html" autocomplete="off">
                            <input type="text" id="search-field" placeholder="Search for anything" class="form-control" tabindex="1">
                            <a href="#" onclick="return false;" class="btn-danger btn-search-close js-waves-off d-none" data-action="toggle" data-class="mobile-search-on">
                                <i class="fal fa-times"></i>
                            </a>
                        </form>
                    </div>
                    <div class="ml-auto d-flex">
                        <!-- activate app search icon (mobile) -->
                        <div class="hidden-sm-up">
                            <a href="#" class="header-icon" data-action="toggle" data-class="mobile-search-on" data-focus="search-field" title="Search">
                                <i class="fal fa-search"></i>
                            </a>
                        </div>
                        <!-- app settings -->
                        <div class="hidden-md-down">
                            <a href="#" class="header-icon" data-toggle="modal" data-target=".js-modal-settings">
                                <i class="fal fa-cog"></i>
                            </a>
                        </div>

                        <div>
                            <a href="#" class="header-icon" data-toggle="dropdown" title="You got 11 notifications">
                                <i class="fal fa-bell"></i>
                                <span class="badge badge-icon">11</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated dropdown-xl">
                                <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center rounded-top mb-2">
                                    <h4 class="m-0 text-center color-white">
                                        11 New
                                        <small class="mb-0 opacity-80">User Notifications</small>
                                    </h4>
                                </div>
                                <ul class="nav nav-tabs nav-tabs-clean" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link px-4 fs-md js-waves-on fw-500 active" data-toggle="tab" href="#tab-messages" data-i18n="drpdwn.messages">Geri Bildirimler</a>
                                    </li>
                                    <!-- Sistem Bildirimleri -->
                                    <li class="nav-item">
                                        <a class="nav-link px-4 fs-md js-waves-on fw-500" data-toggle="tab" href="#tab-notifications" data-i18n="drpdwn.notifications">Sistem Bildirimleri</a>
                                    </li>
                                </ul>
                                <div class="tab-content tab-notification">
                                    <!-- Geri Bildirimler (Messages) Sekmesi -->
                                    <div class="tab-pane active p-3 text-center" id="tab-messages">
                                        <h5 class="mt-4 pt-4 fw-500">
                                            <span class="d-block fa-3x pb-4 text-muted">
                                                <i class="ni ni-arrow-up text-gradient opacity-70"></i>
                                            </span> Şu an için bildirim yok
                                        </h5>
                                    </div>

                                    <!-- Sistem Bildirimleri (Notifications) Sekmesi -->
                                    <div class="tab-pane" id="tab-notifications" role="tabpanel">
                                        <div class="custom-scroll h-100">
                                            <ul class="notification">
                                                <li class="unread">
                                                    <a href="#" class="d-flex align-items-center">
                                                        <span class="status mr-2">
                                                            <img src="https://img.icons8.com/color/48/000000/ok.png" />
                                                        </span>
                                                        <span class="d-flex flex-column flex-1 ml-1">
                                                            <span class="name">Sistem Bildirimi</span>
                                                            <span class="msg-a fs-sm">Sistem Kapanıyor</span>
                                                            <span class="msg-b fs-xs">Sistem 1 saat içerisinde kapanacak. Lütfen işlemlerinizi tamamlayınız.</span>
                                                            <span class="fs-nano text-muted mt-1">1 saat önce</span>
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                                <div class="py-2 px-3 bg-faded d-block rounded-bottom text-right border-faded border-bottom-0 border-right-0 border-left-0">
                                    <a href="#" class="fs-xs fw-500 ml-auto">Tümünü Gör</a>
                                </div>
                            </div>
                        </div>

                        <div>
                            <a href="#" data-toggle="dropdown" title="drlantern@gotbootstrap.com" class="header-icon d-flex align-items-center justify-content-center ml-2">
                                <img src="img/demo/avatars/avatar-admin.png" class="profile-image rounded-circle" alt="Dr. Codex Lantern">

                            </a>
                            <div class="dropdown-menu dropdown-menu-animated dropdown-lg">
                                <div class="dropdown-header bg-trans-gradient d-flex flex-row py-4 rounded-top">
                                    <div class="d-flex flex-row align-items-center mt-1 mb-1 color-white">
                                        <span class="mr-2">
                                            <img src="img/demo/avatars/avatar-admin.png" class="rounded-circle profile-image" alt="Dr. Codex Lantern">
                                        </span>
                                        <div class="info-card-text">
                                            <div class="fs-lg text-truncate text-truncate-lg">Dr. Codex Lantern</div>
                                            <span class="text-truncate text-truncate-md opacity-80">drlantern@gotbootstrap.com</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown-divider m-0"></div>
                                <a href="#" class="dropdown-item" data-action="app-reset">
                                    <span data-i18n="drpdwn.reset_layout">Düzeni Sıfırla</span>
                                </a>
                                <a href="#" class="dropdown-item" data-toggle="modal" data-target=".js-modal-settings">
                                    <span data-i18n="drpdwn.settings">Ayarlar</span>
                                </a>
                                <div class="dropdown-divider m-0"></div>
                                <a href="#" class="dropdown-item" data-action="app-fullscreen">
                                    <span data-i18n="drpdwn.fullscreen">Tam Ekran</span>
                                    <i class="float-right text-muted fw-n">F11</i>
                                </a>
                                <a href="#" class="dropdown-item" data-action="app-print">
                                    <span data-i18n="drpdwn.print">Yazdır</span>
                                    <i class="float-right text-muted fw-n">Ctrl + P</i>
                                </a>
                                <div class="dropdown-divider m-0"></div>
                                <a class="dropdown-item fw-500 pt-3 pb-3" href="page_login.html">
                                    <span data-i18n="drpdwn.page-logout">Çıkış Yap</span>
                                    <span class="float-right fw-n">&commat;codexlantern</span>
                                </a>

                            </div>
                        </div>
                    </div>
                </header>