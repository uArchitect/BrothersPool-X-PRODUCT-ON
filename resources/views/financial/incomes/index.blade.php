@include('layouts.header')

<style>
.income-stats {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
}

.income-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.income-card:hover {
    transform: translateY(-5px);
}

.filter-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.status-badge {
    font-size: 0.8em;
    padding: 0.25em 0.6em;
}

.status-completed {
    background-color: #d4edda;
    color: #155724;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
}
</style>

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel shadow-sm">

                <div class="panel-container show">
                    <div class="panel-content">
                        
                        <!-- Statistics Cards -->
                        <div class="income-stats">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h3 class="mb-1">{{ $totalIncomes ?? 0 }}</h3>
                                        <p class="mb-0">Toplam Gelir</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h3 class="mb-1">₺{{ number_format($totalAmount ?? 0, 2) }}</h3>
                                        <p class="mb-0">Toplam Tutar</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h3 class="mb-1">{{ $thisMonthIncomes ?? 0 }}</h3>
                                        <p class="mb-0">Bu Ay</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h3 class="mb-1">₺{{ number_format($thisMonthAmount ?? 0, 2) }}</h3>
                                        <p class="mb-0">Bu Ay Tutar</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Section -->
                        <div class="filter-section">
                            <form method="GET" action="{{ route('incomes.index') }}" class="row">
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
                                    <label for="category" class="form-label">Kategori</label>
                                    <select class="form-control" id="category" name="income_category_id">
                                            <option value="">Tüm Kategoriler</option>
                                            @foreach($incomeCategories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ request('income_category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                <div class="col-md-3 mb-3">
                                    <label for="status" class="form-label">Durum</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Tüm Durumlar</option>
                                        <option value="TAMAMLANDI" {{ request('status') == 'TAMAMLANDI' ? 'selected' : '' }}>Tamamlandı</option>
                                        <option value="BEKLEMEDE" {{ request('status') == 'BEKLEMEDE' ? 'selected' : '' }}>Beklemede</option>
                                        <option value="IPTAL_EDILDI" {{ request('status') == 'IPTAL_EDILDI' ? 'selected' : '' }}>İptal Edildi</option>
                                        </select>
                                    </div>
                                <div class="col-md-12 text-right">
                                        <button type="submit" class="btn btn-primary">
                                        <i class="fal fa-search mr-1"></i> Filtrele
                                        </button>
                                    <a href="{{ route('incomes.index') }}" class="btn btn-secondary">
                                        <i class="fal fa-times mr-1"></i> Temizle
                                        </a>
                                </div>
                            </form>
                        </div>

                        <!-- Income List -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fal fa-list mr-2"></i>Gelir Listesi</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped w-100" id="incomesTable">
                                        <thead class="bg-success-600 text-white">
                                            <tr>
                                                <th>Belge No</th>
                                                <th>Tarih</th>
                                                <th>Gelir Kalemi</th>
                                                <th>Müşteri</th>
                                                <th>Tutar</th>
                                                <th>Ödeme Yöntemi</th>
                                                <th>Durum</th>
                                                <th>Oluşturan</th>
                                                <th class="text-center" style="width: 120px;">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            @forelse($incomes as $income)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $income->income_number }}</strong>
                                                        @if($income->reference_number)
                                                            <br><small class="text-muted">{{ $income->reference_number }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($income->date)->format('d.m.Y') }}</td>
                                                    <td>
                                                        <span class="badge badge-primary">{{ $income->category_name }}</span>
                                                    </td>
                                                    <td>
                                                        @if($income->customer_name)
                                                            {{ $income->customer_name }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">₺{{ number_format($income->amount, 2) }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">{{ ucfirst($income->payment_method) }}</span>
                                                    </td>
                                                    <td>
                                                        @if($income->status == 'TAMAMLANDI')
                                                            <span class="badge status-badge status-completed">Tamamlandı</span>
                                                        @elseif($income->status == 'BEKLEMEDE')
                                                            <span class="badge status-badge status-pending">Beklemede</span>
                                                        @elseif($income->status == 'IPTAL_EDILDI')
                                                            <span class="badge status-badge status-cancelled">İptal Edildi</span>
                                                        @else
                                                            <span class="badge status-badge">{{ $income->status }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($income->created_by_name)
                                                            {{ $income->created_by_name }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('incomes.show', $income->id) }}" 
                                                               class="btn btn-info btn-sm" title="Görüntüle">
                                                                <i class="fal fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('incomes.edit', $income->id) }}" 
                                                               class="btn btn-warning btn-sm" title="Düzenle">
                                                                <i class="fal fa-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-danger btn-sm delete-income-btn" 
                                                                data-income-id="{{ $income->id }}" title="Sil">
                                                                <i class="fal fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fal fa-inbox fa-3x mb-3"></i>
                                                            <p>Henüz gelir kaydı bulunmuyor.</p>
                                                            <a href="{{ route('incomes.create') }}" class="btn btn-success">
                                                                <i class="fal fa-plus mr-1"></i> İlk Geliri Ekle
                                                    </a>
                                                </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                        </div>
                                    </div>
                        </div>

                        <!-- Pagination removed - Using DataTables for pagination -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable if needed
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#incomesTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json"
            },
            "pageLength": 25,
            "order": [[1, "desc"]], // Sort by date descending
            "columnDefs": [
                { "orderable": false, "targets": 8 } // Disable sorting on actions column
            ]
        });
    }
});

// Silme işlemi
$(document).on('click', '.delete-income-btn', function() {
    const incomeId = $(this).data('income-id');
    
    if (confirm('Bu gelir kaydını silmek istediğinizden emin misiniz?')) {
        $.ajax({
            url: `/incomes/delete/${incomeId}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    alert('Başarılı: ' + response.message);
                    location.reload();
                } else {
                    alert('Hata: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Gelir kaydı silinirken bir hata oluştu.');
            }
        });
    }
});
</script>

@include('layouts.footer')