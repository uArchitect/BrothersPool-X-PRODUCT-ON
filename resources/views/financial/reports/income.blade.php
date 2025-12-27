@include('layouts.header')

<style>
    .stat-card {
        border-left: 4px solid #11998e;
        background: #f8f9fc;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
    }
    .top-item {
        border-left: 3px solid #38ef7d;
        padding: 10px;
        margin-bottom: 10px;
        background: #f8f9fc;
    }
</style>

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-container show">
                    <div class="panel-content">
                        
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="fal fa-arrow-left mr-1"></i> Geri Dön
                                </a>
                            </div>
                            <h4 class="mb-0"><i class="fal fa-chart-line mr-2 text-success"></i>Gelir Raporları</h4>
                        </div>

                        <!-- Filter Form -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fal fa-filter mr-2"></i>Filtreleme</h6>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('reports.income') }}">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                                value="{{ request('start_date') }}">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="end_date" class="form-label">Bitiş Tarihi</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                                value="{{ request('end_date') }}">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="income_category_id" class="form-label">Gelir Kategorisi</label>
                                            <select class="form-control" id="income_category_id" name="income_category_id">
                                                <option value="">Tümü</option>
                                                @foreach($incomeCategories as $category)
                                                    <option value="{{ $category->id }}" 
                                                        {{ request('income_category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 d-flex align-items-end">
                                            <button type="submit" class="btn btn-success btn-block">
                                                <i class="fal fa-search mr-1"></i> Filtrele
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Summary Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted mb-1">Toplam Gelir</h6>
                                                <h3 class="mb-0 text-success">₺{{ number_format($totalIncome, 2) }}</h3>
                                            </div>
                                            <div>
                                                <i class="fal fa-coins fa-2x text-success"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted mb-1">Ortalama Gelir</h6>
                                                <h3 class="mb-0 text-success">₺{{ number_format($averageIncome, 2) }}</h3>
                                            </div>
                                            <div>
                                                <i class="fal fa-chart-line fa-2x text-success"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted mb-1">Toplam İşlem</h6>
                                                <h3 class="mb-0 text-success">{{ number_format($count) }}</h3>
                                            </div>
                                            <div>
                                                <i class="fal fa-list-alt fa-2x text-success"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Top Categories -->
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fal fa-trophy mr-2"></i>En Çok Kazandıran Kategoriler</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($topCategories->count() > 0)
                                            @foreach($topCategories as $index => $category)
                                                <div class="top-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="badge badge-success mr-2">#{{ $index + 1 }}</span>
                                                            <strong>{{ $category->category_name ?? 'Kategorisiz' }}</strong>
                                                        </div>
                                                        <div class="text-right">
                                                            <div class="font-weight-bold text-success">₺{{ number_format($category->total, 2) }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted text-center">Veri bulunamadı</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Monthly Trend -->
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fal fa-chart-area mr-2"></i>Aylık Gelir Trendi</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($monthlyTrend->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Ay</th>
                                                            <th class="text-right">Toplam</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($monthlyTrend as $trend)
                                                            <tr>
                                                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $trend->month)->format('F Y') }}</td>
                                                                <td class="text-right text-success font-weight-bold">₺{{ number_format($trend->total, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted text-center">Veri bulunamadı</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Income List -->
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fal fa-list-alt mr-2"></i>Gelir Listesi</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Belge No</th>
                                                <th>Tarih</th>
                                                <th>Kategori</th>
                                                <th class="text-right">Tutar</th>
                                                <th>Açıklama</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($incomes as $income)
                                                <tr>
                                                    <td>{{ $income->income_number }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($income->date)->format('d.m.Y') }}</td>
                                                    <td>{{ $income->category_name ?? 'Kategorisiz' }}</td>
                                                    <td class="text-right text-success font-weight-bold">₺{{ number_format($income->amount, 2) }}</td>
                                                    <td>{{ $income->description ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Gelir kaydı bulunamadı</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
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

