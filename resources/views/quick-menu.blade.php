@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Hızlı Menüler</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>Hızlı Menüler <span class="fw-300"><i>Hızlı Erişim</i></span></h2>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        <!-- Ana Modüller -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-primary mb-3"><i class="fal fa-store mr-2"></i>Ana Modüller</h4>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Menü Yönetimi -->
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('menu.index') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-utensils fa-3x text-primary"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Menü Yönetimi</h5>
                                        <p class="card-text text-muted small">Yemek menülerini düzenle, fiyatları güncelle ve kategorilere ayır</p>
                                    </div>
                                </a>
                            </div>

                            <!-- Kategoriler -->
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('categories.index') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-tags fa-3x text-success"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Kategoriler</h5>
                                        <p class="card-text text-muted small">Yemek kategorilerini oluştur ve düzenle</p>
                                    </div>
                                </a>
                            </div>

                            <!-- Masa Yönetimi -->
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('tables.index') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-chair fa-3x text-warning"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Masa Yönetimi</h5>
                                        <p class="card-text text-muted small">Masaları ekle, düzenle ve durumlarını takip et</p>
                                    </div>
                                </a>
                            </div>

                            <!-- Sipariş Yönetimi -->
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('orders.index') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-clipboard-list fa-3x text-info"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Sipariş Yönetimi</h5>
                                        <p class="card-text text-muted small">Siparişleri al, takip et ve yönet</p>
                                    </div>
                                </a>
                            </div>


                            <!-- Mutfak Ekranı -->
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('kitchen.index') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-fire fa-3x text-danger"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Mutfak Ekranı</h5>
                                        <p class="card-text text-muted small">Mutfak için sipariş takip ekranı</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Personel Yönetimi -->
                        @canany(['all_employees'])
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-primary mb-3"><i class="fal fa-users-cog mr-2"></i>Personel Yönetimi</h4>
                            </div>
                        </div>

                        <div class="row">
                            @can('all_employees')
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('employees') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-users fa-3x text-primary"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Personel</h5>
                                        <p class="card-text text-muted small">Çalışanları ekle, düzenle ve yönet</p>
                                    </div>
                                </a>
                            </div>
                            @endcan

                            @can('all_users')
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('users') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-user-shield fa-3x text-success"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Kullanıcılar</h5>
                                        <p class="card-text text-muted small">Sistem kullanıcılarını ve yetkilerini yönet</p>
                                    </div>
                                </a>
                            </div>
                            @endcan

                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('employees.specialties') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-star fa-3x text-warning"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Pozisyonlar</h5>
                                        <p class="card-text text-muted small">Çalışan pozisyonlarını ve uzmanlık alanlarını yönet</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        @endcanany

                        <!-- Finansal İşlemler -->
                        @canany(['all_invoices', 'all_payments', 'all_sales', 'all_expenses'])
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-primary mb-3"><i class="fal fa-wallet mr-2"></i>Finansal İşlemler</h4>
                            </div>
                        </div>

                        <div class="row">
                            @can('all_sales')
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('sales') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-chart-line fa-3x text-success"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Satışlar</h5>
                                        <p class="card-text text-muted small">Satış kayıtlarını görüntüle ve yönet</p>
                                    </div>
                                </a>
                            </div>
                            @endcan

                            @can('all_expenses')
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('expenses') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-minus-circle fa-3x text-danger"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Gider Oluştur</h5>
                                        <p class="card-text text-muted small">Yeni gider kaydı oluştur</p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('expenses.list') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-list-alt fa-3x text-info"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Giderleri Listele</h5>
                                        <p class="card-text text-muted small">Tüm gider kayıtlarını görüntüle ve düzenle</p>
                                    </div>
                                </a>
                            </div>
                            @endcan

                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('accounts') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-university fa-3x text-primary"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Kasa İşlemleri</h5>
                                        <p class="card-text text-muted small">Kasa hesaplarını ve işlemlerini yönet</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        @endcanany

                        <!-- Sipariş & Satış -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-primary mb-3"><i class="fal fa-cash-register mr-2"></i>Sipariş & Satış</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('pos') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-cash-register fa-3x text-success"></i>
                                        </div>
                                        <h5 class="card-title text-dark">POS Sistemi</h5>
                                        <p class="card-text text-muted small">Nakit satış işlemleri ve ödeme alma</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Menü Yönetimi (Detaylı) -->
                        @can('all_services')
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-primary mb-3"><i class="fal fa-utensils mr-2"></i>Menü Yönetimi (Detaylı)</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('services') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-hamburger fa-3x text-primary"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Menü Öğeleri</h5>
                                        <p class="card-text text-muted small">Yemek öğelerini detaylı olarak yönet</p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('products') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-box fa-3x text-warning"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Ürünler</h5>
                                        <p class="card-text text-muted small">Stok takibi yapılan ürünleri yönet</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        @endcan

                        <!-- Toplu SMS -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-primary mb-3"><i class="fal fa-sms mr-2"></i>İletişim</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('campaigns') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-sms fa-3x text-info"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Toplu SMS</h5>
                                        <p class="card-text text-muted small">Müşterilere toplu SMS gönder</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Stok Yönetimi -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-primary mb-3"><i class="fal fa-boxes mr-2"></i>Stok Yönetimi</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('warehouse') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-warehouse fa-3x text-secondary"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Malzeme Deposu</h5>
                                        <p class="card-text text-muted small">Depo ve stok yönetimi</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Geri Bildirimler -->
                        @can('all_feedbacks')
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-primary mb-3"><i class="fal fa-smile mr-2"></i>Müşteri Hizmetleri</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('feedbacks') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-smile fa-3x text-warning"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Geri Bildirimler</h5>
                                        <p class="card-text text-muted small">Müşteri geri bildirimlerini görüntüle ve yanıtla</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        @endcan

                        <!-- Raporlar -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-primary mb-3"><i class="fal fa-chart-bar mr-2"></i>Raporlar</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('reports.index') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-chart-bar fa-3x text-success"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Raporlar</h5>
                                        <p class="card-text text-muted small">Detaylı satış ve performans raporları</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Ayarlar -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-primary mb-3"><i class="fal fa-cogs mr-2"></i>Sistem</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('settings') }}" class="card h-100 text-decoration-none">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-cogs fa-3x text-dark"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Ayarlar</h5>
                                        <p class="card-text text-muted small">Sistem ayarlarını yönet</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')
