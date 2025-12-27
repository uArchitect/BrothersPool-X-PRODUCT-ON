@include('layouts.header')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="panel">
               
                <div class="panel-container">
                    <div class="panel-content">
                        <!-- Statistics Cards -->
                        <div class="row mb-4">
                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Toplam Gider</h6>
                                                <h3 class="mb-0">{{ $totalExpenses ?? 0 }}</h3>
                                                <small>₺{{ number_format($totalAmount ?? 0, 2) }}</small>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fal fa-money-bill-wave fa-2x"></i>
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
                                                <h6 class="card-title">Bu Ay Gider</h6>
                                                <h3 class="mb-0">{{ $thisMonthExpenses ?? 0 }}</h3>
                                                <small>₺{{ number_format($thisMonthAmount ?? 0, 2) }}</small>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fal fa-calendar-alt fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Ortalama Gider</h6>
                                                <h3 class="mb-0">{{ $totalExpenses > 0 ? number_format($totalAmount / $totalExpenses, 2) : 0 }}</h3>
                                                <small>₺/gider</small>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fal fa-chart-line fa-2x"></i>
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
                                                <h6 class="card-title">Aktif Hesap</h6>
                                                <h3 class="mb-0">{{ $accounts->where('is_active', true)->count() }}</h3>
                                                <small>hesap</small>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fal fa-university fa-2x"></i>
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
                                <form method="GET" action="{{ route('expenses.index') }}">
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
                                            <label for="expense_type_id" class="form-label">Gider Türü</label>
                                            <select class="form-control" id="expense_type_id" name="expense_type_id">
                                                <option value="">Tümü</option>
                                                @foreach($expenseTypes as $type)
                                                    <option value="{{ $type->id }}" {{ request('expense_type_id') == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
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
                                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                                                <i class="fal fa-times mr-1"></i>Temizle
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Gider Listesi -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fal fa-list mr-2"></i>Gider Listesi
                                </h5>
                                <a href="{{ route('expenses.create') }}" class="btn btn-danger">
                                    <i class="fal fa-plus mr-1"></i>Yeni Gider
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="expensesTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Gider No</th>
                                                <th>Tarih</th>
                                                <th>Gider Kalemi</th>
                                                <th>Hesap</th>
                                                <th>Tutar</th>
                                                <th>İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($expenses as $expense)
                                                <tr>
                                                    <td>{{ $expense->expense_number }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($expense->date)->format('d.m.Y') }}</td>
                                                    <td>{{ $expense->expense_category_name ?? 'N/A' }}</td>
                                                    <td>{{ $expense->account_name ?? 'N/A' }}</td>
                                                    <td class="text-right">₺{{ number_format($expense->amount, 2) }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('expenses.show', $expense->id) }}" 
                                                               class="btn btn-sm btn-info" title="Görüntüle">
                                                                <i class="fal fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('expenses.edit', $expense->id) }}" 
                                                               class="btn btn-sm btn-warning" title="Düzenle">
                                                                <i class="fal fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('expenses.destroy', $expense->id) }}" 
                                                                  method="POST" style="display: inline-block;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                                        title="Sil" 
                                                                        onclick="return confirm('Bu gideri silmek istediğinizden emin misiniz?')">
                                                                    <i class="fal fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Henüz gider kaydı bulunmamaktadır.</td>
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
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

<script>
$(document).ready(function() {
    $('#expensesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json"
        },
        "pageLength": 25,
        "order": [[1, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": 5 }
        ]
    });
});
</script>

@include('layouts.footer')

