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

.section-header-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
</style>

<main id="js-page-content" role="main" class="page-content">

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-container show">
                    <div class="panel-content">

                        <!-- ========== PERSONEL YÖNETİMİ ========== -->
                        <div class="card mb-4 hover-shadow">
                            <div class="section-header section-header-primary">
                                <h4 class="mb-0 text-white"><i class="fal fa-users-cog mr-2"></i>Personel Yönetimi</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Personel Oluştur -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('employees.create') }}" class="card h-100 text-decoration-none hover-shadow border-primary">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-user-plus fa-3x text-primary"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Personel Oluştur</h6>
                                                <p class="card-text text-muted small mb-0">Yeni personel kaydı oluştur</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Personel Listesi -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('employees') }}" class="card h-100 text-decoration-none hover-shadow border-primary">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-users fa-3x text-primary"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Personel Listesi</h6>
                                                <p class="card-text text-muted small mb-0">Tüm personelleri görüntüle</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== BORDRO YÖNETİMİ ========== -->
                        <div class="card mb-4 hover-shadow">
                            <div class="section-header section-header-success">
                                <h4 class="mb-0 text-white"><i class="fal fa-money-check-alt mr-2"></i>Bordro Yönetimi</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Bordro Oluştur -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('payrolls.create') }}" class="card h-100 text-decoration-none hover-shadow border-success">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-plus-circle fa-3x text-success"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Bordro Oluştur</h6>
                                                <p class="card-text text-muted small mb-0">Yeni bordro kaydı oluştur</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Bordro Listesi -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('payrolls.index') }}" class="card h-100 text-decoration-none hover-shadow border-success">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-list-alt fa-3x text-success"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Bordro Listesi</h6>
                                                <p class="card-text text-muted small mb-0">Tüm bordroları görüntüle</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== GRUP ve POZİSYON YÖNETİMİ ========== -->
                        <div class="card mb-4 hover-shadow">
                            <div class="section-header section-header-info">
                                <h4 class="mb-0 text-white"><i class="fal fa-sitemap mr-2"></i>Grup ve Pozisyon Yönetimi</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Grup Yönetimi -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('hr.groups.index') }}" class="card h-100 text-decoration-none hover-shadow border-info">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-users fa-3x text-info"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Grup Yönetimi</h6>
                                                <p class="card-text text-muted small mb-0">Departmanları yönet</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Yeni Grup Ekle -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('hr.groups.create') }}" class="card h-100 text-decoration-none hover-shadow border-info">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-plus-circle fa-3x text-info"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Yeni Grup Ekle</h6>
                                                <p class="card-text text-muted small mb-0">Yeni departman oluştur</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Pozisyon Yönetimi -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('hr.positions.index') }}" class="card h-100 text-decoration-none hover-shadow border-info">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-user-tag fa-3x text-info"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Pozisyon Yönetimi</h6>
                                                <p class="card-text text-muted small mb-0">Görevleri yönet</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Yeni Pozisyon Ekle -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <a href="{{ route('hr.positions.create') }}" class="card h-100 text-decoration-none hover-shadow border-info">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-plus-circle fa-3x text-info"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Yeni Pozisyon Ekle</h6>
                                                <p class="card-text text-muted small mb-0">Yeni görev oluştur</p>
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

