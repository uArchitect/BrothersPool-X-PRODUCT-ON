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
</style>

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-container show">
                    <div class="panel-content">
                        
                        <div class="mb-3">
                            <a href="{{ route('financial.management') }}" class="btn btn-sm btn-secondary">
                                <i class="fal fa-arrow-left mr-1"></i> Geri Dön
                            </a>
                        </div>

                        <!-- ========== RAPORLAR ========== -->
                        <div class="card mb-4 hover-shadow">
                            <div class="section-header">
                                <h4 class="mb-0 text-white"><i class="fal fa-chart-bar mr-2"></i>Raporlar</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Gelir Raporları -->
                                    <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                                        <a href="{{ route('reports.income') }}" class="card h-100 text-decoration-none hover-shadow border-success">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-chart-line fa-3x text-success"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Gelir Raporları</h6>
                                                <p class="card-text text-muted small mb-0">Gelir analizi ve detaylı raporlar</p>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Gider Raporları -->
                                    <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                                        <a href="{{ route('reports.expense') }}" class="card h-100 text-decoration-none hover-shadow border-danger">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fal fa-chart-bar fa-3x text-danger"></i>
                                                </div>
                                                <h6 class="card-title text-dark mb-1">Gider Raporları</h6>
                                                <p class="card-text text-muted small mb-0">Gider analizi ve detaylı raporlar</p>
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

