@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Finansal Yönetim</li>
        <li class="breadcrumb-item active">Gelir Tablosu</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">

                <div class="panel-container show">
                    <div class="panel-content">
                        <!-- Tarih Filtreleri -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('income-statement.index') }}" class="form-inline">
                                    <div class="form-group mr-3">
                                        <label for="start_date" class="mr-2">Başlangıç:</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="{{ $startDate }}">
                                    </div>
                                    <div class="form-group mr-3">
                                        <label for="end_date" class="mr-2">Bitiş:</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                               value="{{ $endDate }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fal fa-search mr-1"></i>Filtrele
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary" onclick="setDateRange('today')">Bugün</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="setDateRange('week')">Bu Hafta</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="setDateRange('month')">Bu Ay</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="setDateRange('year')">Bu Yıl</button>
                                </div>
                            </div>
                        </div>

                        <!-- Özet Kartları -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3 class="card-title">
                                            <i class="fal fa-arrow-up mr-2"></i>
                                            ₺{{ number_format($totalRevenue, 2) }}
                                        </h3>
                                        <p class="card-text">Toplam Gelir</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h3 class="card-title">
                                            <i class="fal fa-arrow-down mr-2"></i>
                                            ₺{{ number_format($totalExpense, 2) }}
                                        </h3>
                                        <p class="card-text">Toplam Gider</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card {{ $netProfit >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
                                    <div class="card-body text-center">
                                        <h3 class="card-title">
                                            <i class="fal fa-{{ $netProfit >= 0 ? 'chart-line' : 'exclamation-triangle' }} mr-2"></i>
                                            ₺{{ number_format($netProfit, 2) }}
                                        </h3>
                                        <p class="card-text">{{ $netProfit >= 0 ? 'Net Kar' : 'Net Zarar' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gelir Tablosu Detayları -->
                        <div class="row">
                            <!-- Gelirler -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i class="fal fa-arrow-up mr-2"></i>Gelirler
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @if($revenues->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Kategori</th>
                                                            <th>Alt Kategori</th>
                                                            <th class="text-right">Tutar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($revenues as $revenue)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $revenue['category'] }}</strong>
                                                            </td>
                                                            <td>{{ $revenue['subcategory'] }}</td>
                                                            <td class="text-right">
                                                                <span class="font-weight-bold text-success">
                                                                    ₺{{ number_format($revenue['amount'], 2) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="bg-light">
                                                        <tr>
                                                            <th colspan="2">TOPLAM GELİR</th>
                                                            <th class="text-right text-success">
                                                                ₺{{ number_format($totalRevenue, 2) }}
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-3">
                                                <i class="fal fa-inbox fa-2x text-muted mb-2"></i>
                                                <p class="text-muted">Bu dönemde gelir kaydı bulunmuyor.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Giderler -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0">
                                            <i class="fal fa-arrow-down mr-2"></i>Giderler
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @if($expenses->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Kategori</th>
                                                            <th>Alt Kategori</th>
                                                            <th class="text-right">Tutar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($expenses as $expense)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $expense['category'] }}</strong>
                                                            </td>
                                                            <td>{{ $expense['subcategory'] }}</td>
                                                            <td class="text-right">
                                                                <span class="font-weight-bold text-danger">
                                                                    ₺{{ number_format($expense['amount'], 2) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="bg-light">
                                                        <tr>
                                                            <th colspan="2">TOPLAM GİDER</th>
                                                            <th class="text-right text-danger">
                                                                ₺{{ number_format($totalExpense, 2) }}
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-3">
                                                <i class="fal fa-inbox fa-2x text-muted mb-2"></i>
                                                <p class="text-muted">Bu dönemde gider kaydı bulunmuyor.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Net Kar Analizi -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header {{ $netProfit >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
                                        <h5 class="mb-0">
                                            <i class="fal fa-{{ $netProfit >= 0 ? 'chart-line' : 'exclamation-triangle' }} mr-2"></i>
                                            Net Kar Analizi
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h6 class="text-muted">Kar Marjı</h6>
                                                    <h4 class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ $totalRevenue > 0 ? number_format(($netProfit / $totalRevenue) * 100, 2) : 0 }}%
                                                    </h4>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h6 class="text-muted">Gider Oranı</h6>
                                                    <h4 class="text-warning">
                                                        {{ $totalRevenue > 0 ? number_format(($totalExpense / $totalRevenue) * 100, 2) : 0 }}%
                                                    </h4>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h6 class="text-muted">Gelir/Gider Oranı</h6>
                                                    <h4 class="text-info">
                                                        {{ $totalExpense > 0 ? number_format($totalRevenue / $totalExpense, 2) : '∞' }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
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

<script>
// Tarih aralığı ayarlama
function setDateRange(range) {
    const today = new Date();
    let startDate, endDate;
    
    switch(range) {
        case 'today':
            startDate = endDate = today.toISOString().split('T')[0];
            break;
        case 'week':
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay());
            startDate = startOfWeek.toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
        case 'year':
            startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
    }
    
    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
}

// PDF export
function exportPDF() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    window.open(`{{ route('income-statement.pdf') }}?start_date=${startDate}&end_date=${endDate}`, '_blank');
}

// Excel export
function exportExcel() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    window.open(`{{ route('income-statement.excel') }}?start_date=${startDate}&end_date=${endDate}`, '_blank');
}
</script>
