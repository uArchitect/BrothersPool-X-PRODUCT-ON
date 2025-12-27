@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Restoran Yönetimi</li>
        <li class="breadcrumb-item active">Modül Yönetimi</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>Restoran Yönetimi <span class="fw-300"><i>Modül Yönetimi</i></span></h2>
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
                            <div class="col-xl-4 col-lg-4 col-md-6 mb-4">
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
                            <div class="col-xl-4 col-lg-4 col-md-6 mb-4">
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
                            <div class="col-xl-4 col-lg-4 col-md-6 mb-4">
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
                            <div class="col-xl-4 col-lg-4 col-md-6 mb-4">
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
                            <div class="col-xl-4 col-lg-4 col-md-6 mb-4">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')
