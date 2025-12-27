@include('layouts.header')

<style>
.hover-shadow {
    transition: all 0.3s ease;
    border: 1px solid #e3e6f0;
}

.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
    border-color: #4e73df;
}

.hover-shadow .card-body {
    transition: all 0.3s ease;
}

.hover-shadow:hover .card-body {
    background-color: #f8f9fc;
}

.hover-shadow:hover .fa-3x {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

.section-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
    margin-bottom: 0;
}

.section-header-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.section-header-danger {
    background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
}

.section-header-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

<main id="js-page-content" role="main" class="page-content">

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-container show">
                    <div class="panel-content">

                        <!-- ========== GELİR YÖNETİMİ ========== -->
                        <div class="card mb-4 hover-shadow">
                            <div class="section-header section-header-success">
                                <h4 class="mb-0 text-white"><i class="fal fa-arrow-circle-up mr-2"></i>Gelir Yönetimi</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Gelir Oluştur -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('incomes.create') }}" class="card h-100 text-decoration-none hover-shadow border-success">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-plus-circle fa-3x text-success"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Gelir Oluştur</h6>
                                                <p class="card-text text-muted small mb-0">Yeni gelir kaydı oluştur</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Gelir Listesi -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('incomes.index') }}" class="card h-100 text-decoration-none hover-shadow border-success">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-list-alt fa-3x text-success"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Gelir Listesi</h6>
                                                <p class="card-text text-muted small mb-0">Tüm gelirleri görüntüle</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Gelir Kategorileri -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('income_categories.index') }}" class="card h-100 text-decoration-none hover-shadow border-success">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-folder fa-3x text-success"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Gelir Kategorileri</h6>
                                                <p class="card-text text-muted small mb-0">Kategorileri yönet</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Gelir Kalemleri -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('income_types.index') }}" class="card h-100 text-decoration-none hover-shadow border-success">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-tags fa-3x text-success"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Gelir Kalemleri</h6>
                                                <p class="card-text text-muted small mb-0">Gelir kalemlerini yönetin</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== GİDER YÖNETİMİ ========== -->
                        <div class="card mb-4 hover-shadow">
                            <div class="section-header section-header-danger">
                                <h4 class="mb-0 text-white"><i class="fal fa-arrow-circle-down mr-2"></i>Gider Yönetimi</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Gider Oluştur -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('expenses.create') }}" class="card h-100 text-decoration-none hover-shadow border-danger">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-plus-circle fa-3x text-danger"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Gider Oluştur</h6>
                                                <p class="card-text text-muted small mb-0">Yeni gider kaydı oluştur</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Gider Listesi -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('expenses.list') }}" class="card h-100 text-decoration-none hover-shadow border-danger">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-list-alt fa-3x text-danger"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Gider Listesi</h6>
                                                <p class="card-text text-muted small mb-0">Tüm giderleri görüntüle</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Gider Kategorileri -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('expense_categories.index') }}" class="card h-100 text-decoration-none hover-shadow border-danger">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-folder fa-3x text-danger"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Gider Kategorileri</h6>
                                                <p class="card-text text-muted small mb-0">Kategorileri yönet</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Gider Kalemleri -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('expense_types.index') }}" class="card h-100 text-decoration-none hover-shadow border-danger">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-tags fa-3x text-danger"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Gider Kalemleri</h6>
                                                <p class="card-text text-muted small mb-0">Gider kalemlerini yönetin</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Raporlar -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('reports.index') }}" class="card h-100 text-decoration-none hover-shadow border-warning">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-chart-bar fa-3x text-warning"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Raporlar</h6>
                                                <p class="card-text text-muted small mb-0">Gelir ve gider raporları</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== MÜŞTERİ VE CARİ İŞLEMLER ========== -->
                        <div class="card mb-4 hover-shadow">
                            <div class="section-header section-header-primary">
                                <h4 class="mb-0 text-white"><i class="fal fa-users mr-2"></i>Müşteri ve Cari İşlemler</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Müşteri Oluştur -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('customers.create') }}" class="card h-100 text-decoration-none hover-shadow border-primary">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-user-plus fa-3x text-primary"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Müşteri Oluştur</h6>
                                                <p class="card-text text-muted small mb-0">Yeni müşteri kaydı</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Müşteri Yönetimi -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('customers.index') }}" class="card h-100 text-decoration-none hover-shadow border-primary">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-users fa-3x text-primary"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Müşteri Listesi</h6>
                                                <p class="card-text text-muted small mb-0">Müşterileri yönet</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Hesap Hareketleri -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('account-transactions.index') }}" class="card h-100 text-decoration-none hover-shadow border-primary">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-exchange-alt fa-3x text-primary"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Hesap Hareketleri</h6>
                                                <p class="card-text text-muted small mb-0">Tüm hesap hareketleri</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Borç Yönetimi -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('customer-debts.index') }}" class="card h-100 text-decoration-none hover-shadow border-warning">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-money-bill-wave fa-3x text-warning"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Borç Yönetimi</h6>
                                                <p class="card-text text-muted small mb-0">Kasa üzerinden borç ödemeleri</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== ÇEK VE SENET YÖNETİMİ ========== -->
                        <div class="card mb-4 hover-shadow">
                            <div class="section-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <h4 class="mb-0 text-white"><i class="fal fa-file-invoice-dollar mr-2"></i>Çek ve Senet Yönetimi</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Çek Oluştur -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('checks.create') }}" class="card h-100 text-decoration-none hover-shadow border-warning">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-plus-circle fa-3x text-warning"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Çek Oluştur</h6>
                                                <p class="card-text text-muted small mb-0">Yeni çek kaydı</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Çek Listesi -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('checks.index') }}" class="card h-100 text-decoration-none hover-shadow border-warning">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-list-alt fa-3x text-warning"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Çek Listesi</h6>
                                                <p class="card-text text-muted small mb-0">Tüm çekleri görüntüle</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Senet Oluştur -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('promissory_notes.create') }}" class="card h-100 text-decoration-none hover-shadow border-info">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-plus-circle fa-3x text-info"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Senet Oluştur</h6>
                                                <p class="card-text text-muted small mb-0">Yeni senet kaydı</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Senet Listesi -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('promissory_notes.index') }}" class="card h-100 text-decoration-none hover-shadow border-info">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-list-alt fa-3x text-info"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Senet Listesi</h6>
                                                <p class="card-text text-muted small mb-0">Tüm senetleri görüntüle</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== KASA VE HESAP YÖNETİMİ ========== -->
                        <div class="card mb-4 hover-shadow">
                            <div class="section-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <h4 class="mb-0 text-white"><i class="fal fa-cash-register mr-2"></i>Kasa ve Hesap Yönetimi</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Kasa İşlemleri -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('accounts') }}" class="card h-100 text-decoration-none hover-shadow border-info">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-university fa-3x text-info"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Kasa İşlemleri</h6>
                                                <p class="card-text text-muted small mb-0">Hesapları yönet</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== SATIŞ YÖNETİMİ ========== -->
                        <div class="card mb-4 hover-shadow">
                            <div class="section-header" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <h4 class="mb-0 text-white"><i class="fal fa-chart-line mr-2"></i>Satış Yönetimi</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- POS Satışları -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('possales') }}" class="card h-100 text-decoration-none hover-shadow">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-credit-card fa-3x text-primary"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">POS Satışları</h6>
                                                <p class="card-text text-muted small mb-0">POS sistem satışları</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Genel Satışlar -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('sales') }}" class="card h-100 text-decoration-none hover-shadow">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-chart-line fa-3x text-success"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Genel Satışlar</h6>
                                                <p class="card-text text-muted small mb-0">Satış işlemleri</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Müşteri Satışları -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('customers.index') }}" class="card h-100 text-decoration-none hover-shadow">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-users fa-3x text-info"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Müşteri Satışları</h6>
                                                <p class="card-text text-muted small mb-0">Müşteri bazlı analiz</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Ürün Satışları -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('menu.index') }}" class="card h-100 text-decoration-none hover-shadow">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-utensils fa-3x text-warning"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Ürün Satışları</h6>
                                                <p class="card-text text-muted small mb-0">Ürün bazlı analiz</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== MUHASEBE RAPORLARI ========== -->
                        <div class="card mb-4 hover-shadow">
                            <div class="section-header" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                                <h4 class="mb-0 text-white"><i class="fal fa-calculator mr-2"></i>Muhasebe Raporları</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Gelir Tablosu -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('income-statement.index') }}" class="card h-100 text-decoration-none hover-shadow">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-chart-line fa-3x text-success"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Gelir Tablosu</h6>
                                                <p class="card-text text-muted small mb-0">Kar-zarar analizi</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')
