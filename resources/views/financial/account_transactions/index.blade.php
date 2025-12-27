@include('layouts.header')

<style>
.hover-shadow {
    transition: all 0.3s ease;
    border: 1px solid #e3e6f0;
}

.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
}

.badge-income {
    background-color: #1cc88a;
    color: white;
}

.badge-expense {
    background-color: #e74a3b;
    color: white;
}

.badge-transfer {
    background-color: #36b9cc;
    color: white;
}
</style>

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-container show">
                    <div class="panel-content">
                        <!-- Başlık -->
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="alert-icon">
                                        <i class="fal fa-exchange-alt"></i>
                                    </div>
                                    <div class="flex-1 ml-2">
                                        <span class="h5">Hesap Hareketleri</span>
                                        <br>Tüm hesap hareketlerini görüntüleyin, filtreleyin ve düzenleyin.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Özet İstatistikler -->
                        <div class="row mb-4">
                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Toplam Hareket</h6>
                                                <h3 class="mb-0">{{ number_format($summary['total_transactions']) }}</h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fal fa-list fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Toplam Gelir</h6>
                                                <h3 class="mb-0">₺{{ number_format($summary['total_income'], 2) }}</h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fal fa-arrow-up fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Toplam Gider</h6>
                                                <h3 class="mb-0">₺{{ number_format($summary['total_expense'], 2) }}</h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fal fa-arrow-down fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Transfer</h6>
                                                <h3 class="mb-0">{{ number_format($summary['total_transfer']) }}</h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fal fa-exchange-alt fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filtreler -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fal fa-filter mr-2"></i>Filtreler
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('account-transactions.index') }}">
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
                                            <label for="type" class="form-label">Hareket Türü</label>
                                            <select class="form-control" id="type" name="type">
                                                <option value="">Tümü</option>
                                                <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Gelir</option>
                                                <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Gider</option>
                                                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="account_id" class="form-label">Hesap</label>
                                            <select class="form-control" id="account_id" name="account_id">
                                                <option value="">Tümü</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                                        {{ $account->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fal fa-search mr-1"></i>Filtrele
                                            </button>
                                            <a href="{{ route('account-transactions.index') }}" class="btn btn-secondary">
                                                <i class="fal fa-times mr-1"></i>Temizle
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Hareket Listesi -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fal fa-list mr-2"></i>Hesap Hareketleri
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="transactionsTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Hareket No</th>
                                                <th>Tarih</th>
                                                <th>Hesap</th>
                                                <th>Tür</th>
                                                <th>Tutar</th>
                                                <th>Açıklama</th>
                                                <th style="width: 100px;">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($transactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->transaction_number }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d.m.Y') }}</td>
                                                    <td>{{ $transaction->account_name ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($transaction->type == 'income')
                                                            <span class="badge badge-income">
                                                                <i class="fal fa-arrow-up mr-1"></i>Gelir
                                                            </span>
                                                        @elseif($transaction->type == 'expense')
                                                            <span class="badge badge-expense">
                                                                <i class="fal fa-arrow-down mr-1"></i>Gider
                                                            </span>
                                                        @else
                                                            <span class="badge badge-transfer">
                                                                <i class="fal fa-exchange-alt mr-1"></i>Transfer
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-right {{ $transaction->type == 'income' ? 'text-success' : ($transaction->type == 'expense' ? 'text-danger' : 'text-info') }}">
                                                        {{ $transaction->type == 'income' ? '+' : ($transaction->type == 'expense' ? '-' : '±') }}₺{{ number_format($transaction->amount, 2) }}
                                                    </td>
                                                    <td>{{ $transaction->description ?? '-' }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-warning" onclick="editTransaction({{ $transaction->id }})" title="Düzenle">
                                                                <i class="fal fa-edit"></i>
                                                            </button>
                                                            <form action="{{ route('account-transactions.destroy', $transaction->id) }}" 
                                                                  method="POST" style="display: inline-block;" 
                                                                  onsubmit="return confirm('Bu hareketi silmek istediğinizden emin misiniz?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                                                    <i class="fal fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">Henüz hareket bulunmamaktadır.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="mt-3">
                                    {{ $transactions->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Düzenleme Modalı -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" role="dialog" aria-labelledby="editTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTransactionModalLabel">
                    <i class="fal fa-edit mr-2"></i>Hareket Düzenle
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editTransactionForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="edit_transaction_id" name="id">
                    
                    <div class="form-group">
                        <label for="edit_account_id">Hesap <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_account_id" name="account_id" required>
                            <option value="">Seçiniz</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_type">Hareket Türü <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_type" name="type" required>
                            <option value="">Seçiniz</option>
                            <option value="income">Gelir</option>
                            <option value="expense">Gider</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_amount">Tutar <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="edit_amount" name="amount" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_date">Tarih <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="edit_date" name="date" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_description">Açıklama</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fal fa-save mr-1"></i>Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('layouts.footer')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function editTransaction(id) {
    // AJAX ile hareket bilgilerini getir
    const url = '/account-transactions/' + id + '/get';
    $.ajax({
        url: url,
        method: 'GET',
        success: function(response) {
            $('#edit_transaction_id').val(response.id);
            $('#edit_account_id').val(response.account_id);
            $('#edit_type').val(response.type);
            $('#edit_amount').val(response.amount);
            $('#edit_date').val(response.date);
            $('#edit_description').val(response.description || '');
            $('#editTransactionModal').modal('show');
        },
        error: function(xhr) {
            alert('Hareket bilgileri yüklenirken bir hata oluştu.');
            console.error(xhr);
        }
    });
}

// Form submit işlemi
$(document).ready(function() {
    $('#editTransactionForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#edit_transaction_id').val();
        if (!id) {
            alert('Hareket ID bulunamadı.');
            return;
        }
        
        const formData = $(this).serialize() + '&_method=PUT';
        
        const updateUrl = '/account-transactions/' + id;
        $.ajax({
            url: updateUrl,
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#editTransactionModal').modal('hide');
                alert('Hareket başarıyla güncellendi.');
                location.reload();
            },
            error: function(xhr) {
                let errorMsg = 'Hareket güncellenirken bir hata oluştu.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
                console.error(xhr);
            }
        });
    });
});

// DataTables initialization
$(document).ready(function() {
    $('#transactionsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json"
        },
        "pageLength": 25,
        "order": [[1, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": 6 }
        ]
    });
});
</script>

