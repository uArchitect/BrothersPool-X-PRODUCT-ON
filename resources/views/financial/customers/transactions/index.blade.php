@include('layouts.header')

<style>
/* SmartAdmin 4.0.1 Tema Uyumlu Stiller */
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

.transaction-stats .panel {
    border-left: 4px solid;
}

.transaction-stats .panel-primary {
    border-left-color: #4e73df;
}

.transaction-stats .panel-success {
    border-left-color: #1cc88a;
}

.transaction-stats .panel-warning {
    border-left-color: #f6c23e;
}

.transaction-stats .panel-info {
    border-left-color: #36b9cc;
}

</style>

<main id="js-page-content" role="main" class="page-content">

    <div class="row">
        <!-- Müşteri Özeti -->
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-container show">
                    <div class="panel-content">
                        <!-- Hızlı Erişim Box'ları -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-primary mb-3"><i class="fal fa-bolt mr-2"></i>Hızlı Erişim</h4>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                <a href="{{ route('customers.transactions.create', $customer->id) }}" class="card h-100 text-decoration-none hover-shadow">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-plus-circle fa-3x text-success"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Yeni Hareket</h5>
                                        <p class="card-text text-muted small">Yeni hareket ekle</p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                <a href="{{ route('customers.transactions.index', $customer->id) }}?type=Gelir" class="card h-100 text-decoration-none hover-shadow">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-arrow-up fa-3x text-success"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Gelir Hareketleri</h5>
                                        <p class="card-text text-muted small">Gelir hareketlerini görüntüle</p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                <a href="{{ route('customers.transactions.index', $customer->id) }}?type=Gider" class="card h-100 text-decoration-none hover-shadow">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-arrow-down fa-3x text-danger"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Gider Hareketleri</h5>
                                        <p class="card-text text-muted small">Gider hareketlerini görüntüle</p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                <a href="{{ route('customers.show', $customer->id) }}" class="card h-100 text-decoration-none hover-shadow">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fal fa-user fa-3x text-primary"></i>
                                        </div>
                                        <h5 class="card-title text-dark">Müşteri Detayı</h5>
                                        <p class="card-text text-muted small">Müşteri bilgilerini görüntüle</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Özet Kartları -->
                        <div class="row mb-4 transaction-stats">
                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                <div class="card border-left-primary hover-shadow">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="small font-weight-bold text-primary text-uppercase mb-1">
                                                    Toplam Hareket
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-dark">
                                                    {{ $summary->total_transactions ?? 0 }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fal fa-list fa-2x text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                <div class="card border-left-success hover-shadow">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="small font-weight-bold text-success text-uppercase mb-1">
                                                    Toplam Gelir
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-dark">
                                                    ₺{{ number_format($summary->total_income ?? 0, 2) }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fal fa-arrow-up fa-2x text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                <div class="card border-left-danger hover-shadow">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="small font-weight-bold text-danger text-uppercase mb-1">
                                                    Toplam Gider
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-dark">
                                                    ₺{{ number_format($summary->total_expense ?? 0, 2) }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fal fa-arrow-down fa-2x text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                <div class="card border-left-info hover-shadow">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="small font-weight-bold text-info text-uppercase mb-1">
                                                    Net Bakiye
                                                </div>
                                                @if(($summary->net_balance ?? 0) > 0)
                                                    <div class="h5 mb-0 font-weight-bold text-success">
                                                        <i class="fal fa-arrow-up mr-1"></i>Alacağım: ₺{{ number_format($summary->net_balance, 2) }}
                                                    </div>
                                                @elseif(($summary->net_balance ?? 0) < 0)
                                                    <div class="h5 mb-0 font-weight-bold text-danger">
                                                        <i class="fal fa-arrow-down mr-1"></i>Borç: ₺{{ number_format(abs($summary->net_balance), 2) }}
                                                    </div>
                                                @else
                                                    <div class="h5 mb-0 font-weight-bold text-secondary">
                                                        <i class="fal fa-balance-scale mr-1"></i>Dengeli
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-auto">
                                                <i class="fal fa-balance-scale fa-2x text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filtre Formu -->
                        <form method="GET" action="{{ route('customers.transactions.index', $customer->id) }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="start_date">Başlangıç Tarihi</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="{{ request('start_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="end_date">Bitiş Tarihi</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                               value="{{ request('end_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="type">Tip</label>
                                        <select class="form-control" id="type" name="type">
                                            <option value="">Tümü</option>
                                            <option value="Gelir" {{ request('type') == 'Gelir' ? 'selected' : '' }}>Gelir</option>
                                            <option value="Gider" {{ request('type') == 'Gider' ? 'selected' : '' }}>Gider</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="account">Hesap</label>
                                        <input type="text" class="form-control" id="account" name="account" 
                                               value="{{ request('account') }}" placeholder="Hesap adı">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fal fa-search mr-1"></i>Filtrele
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Hareketler Tablosu -->
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <div class="alert-icon">
                                    <i class="fal fa-info-circle"></i>
                                </div>
                                <div class="flex-1 ml-2">
                                    <span class="h5">Müşteri Hareketleri</span>
                                    <br>{{ $customer->title ?? 'Müşteri' }} için hareketler listeleniyor.
                                </div>
                            </div>
                        </div>

                        <table id="dt-transactions" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-highlight">
                                <tr>
                                    <th class="text-center" style="width: 20px;"><i class="fal fa-calendar" title="Tarih"></i></th>
                                    <th>Tarih</th>
                                    <th>Hesap</th>
                                    <th>Tip</th>
                                    <th>Tutar</th>
                                    <th>Açıklama</th>
                                    <th style="width: 100px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td class="text-center">
                                        <i class="fal fa-calendar text-primary"></i>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold">{{ \Carbon\Carbon::parse($transaction->date)->format('d.m.Y') }}</span>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($transaction->date)->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-university text-info mr-2"></i>
                                            <span>{{ $transaction->account }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $transaction->type === 'Gelir' ? 'badge-success' : 'badge-danger' }}">
                                            <i class="fal {{ $transaction->type === 'Gelir' ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-1"></i>
                                            {{ $transaction->type }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="font-weight-bold {{ $transaction->type === 'Gelir' ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->type === 'Gelir' ? '+' : '-' }}₺{{ number_format($transaction->amount, 2) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{!! $transaction->description ?? '-' !!}</span>
                                            @if($transaction->created_at)
                                                <small class="text-muted">
                                                    <i class="fal fa-clock mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($transaction->created_at)->diffForHumans() }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('customers.transactions.edit', [$customer->id, $transaction->id]) }}" 
                                               class="btn btn-xs btn-warning" title="Düzenle">
                                                <i class="fal fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-xs btn-danger" 
                                                    onclick="deleteTransaction({{ $customer->id }}, {{ $transaction->id }})" title="Sil">
                                                <i class="fal fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fal fa-inbox fa-2x mb-2"></i><br>
                                        Hareket bulunamadı.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hareket Sil</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bu hareketi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script defer src="{{ asset('js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#dt-transactions').DataTable({
        responsive: true,
        stateSave: true,
        pageLength: 25,
        order: [[1, 'desc']], // Sort by date descending
        language: { url: "{{ asset('media/data/tr.json') }}" },
        columnDefs: [
            { orderable: false, targets: [0, 6] }, // Icon and actions columns
            { searchable: false, targets: [0, 6] }
        ]
    });
});

function deleteTransaction(customerId, transactionId) {
    document.getElementById('deleteForm').action = '/customers/' + customerId + '/transactions/' + transactionId;
    $('#deleteModal').modal('show');
}
</script>

@include('layouts.footer')
