@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Finans Yönetimi</li>
        <li class="breadcrumb-item active">Gider Listesi</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr d-flex justify-content-between align-items-center">
                    <h2>Gider Listesi <span class="fw-300"><i></i></span></h2>
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm"><i class="fal fa-plus mr-1"></i>
                        Yeni Gider</a>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label >Başlangıç Tarihi</label>
                                            <input type="date" class="form-control" id="startDate">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label >Bitiş Tarihi</label>
                                            <input type="date" class="form-control" id="endDate">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label >Gider Tipi</label>
                                            <select class="custom-select" id="expenseType">
                                                <option value="">Tümü</option>
                                                @foreach ($expense_types as $expense_type)
                                                    <option value="{{ $expense_type->name }}">{{ $expense_type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label >Hesap</label>
                                            <select class="custom-select" id="account">
                                                <option value="">Tümü</option>
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="dt-basic-example" class="table table-bordered table-hover w-100">
                                <thead>
                                    <tr>
                                        <th class="border-top-0">Tarih</th>
                                        <th class="border-top-0">Belge No</th>
                                        <th class="border-top-0">Gider Tipi</th>
                                        <th class="border-top-0">Hesap</th>
                                        <th class="border-top-0">Müşteri (CRM)</th>
                                        <th class="text-right border-top-0">Toplam Tutar</th>
                                        <th class="text-center border-top-0" style="width: 120px">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $expense)
                                        <tr>
                                            <td>{{ date('d.m.Y', strtotime($expense->date)) }}</td>
                                            <td><span class="badge badge-primary">{{ $expense->expense_number }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary"
                                                    data-expense-type-id="{{ $expense->expense_type_id }}">
                                                    {{ $expense->expense_type_name }}
                                                </span>
                                            </td>
                                            <td>{{ $expense->account_name }}</td>
                                            <td>
                                                @if($expense->customer_name)
                                                    <span class="badge badge-info">
                                                        {{ $expense->customer_name }}
                                                        @if($expense->customer_code)
                                                            ({{ $expense->customer_code }})
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-right font-weight-bold">
                                                {{ number_format($expense->total, 2, ',', '.') }} ₺</td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info" data-toggle="modal"
                                                        data-target="#detailsModal{{ $expense->id }}"
                                                        title="Detaylar">
                                                        <i class="fal fa-info-circle"></i>
                                                    </button>

                                                    <a href="{{ route('expenses.edit', $expense->id) }}" 
                                                        class="btn btn-warning" title="Düzenle">
                                                        <i class="fal fa-edit"></i>
                                                    </a>

                                                    <button type="button" 
                                                        class="btn btn-danger delete-expense-btn"
                                                        data-expense-id="{{ $expense->id }}"
                                                        title="Sil">
                                                        <i class="fal fa-trash-alt"></i>        
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- end panel -->
            </div> <!-- end col -->
        </div> <!-- end row -->
</main>

<!-- Detail Modals -->
@foreach ($expenses as $expense)
<div class="modal fade" id="detailsModal{{ $expense->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel{{ $expense->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel{{ $expense->id }}">
                    Gider Detayları - {{ $expense->expense_number }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="fal fa-calendar mr-2"></i>Tarih</span>
                        <strong>{{ date('d.m.Y', strtotime($expense->date)) }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="fal fa-tag mr-2"></i>Gider Tipi</span>
                        <strong>{{ $expense->expense_type_name }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="fal fa-wallet mr-2"></i>Hesap</span>
                        <strong>{{ $expense->account_name }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="fal fa-sticky-note mr-2"></i>Notlar</span>
                        <strong>{!! $expense->note ?? '-' !!}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="fal fa-file-image mr-2"></i>Fatura Fotoğrafı</span>
                        @if(isset($expense->receipt_image) && $expense->receipt_image)
                            <img src="{{ asset('images/' . $expense->receipt_image) }}" alt="Fatura Fotoğrafı"
                                class="img-fluid" style="max-width: 200px;">
                        @else
                            <span class="text-muted">Fotoğraf yok</span>
                        @endif
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="fal fa-money-bill-wave mr-2"></i>Toplam Tutar</span>
                        <strong class="text-success">{{ number_format($expense->total, 2, ',', '.') }} ₺</strong>
                    </div>
                </div>
                
                <!-- Gider Kalemleri -->
                <div class="mt-4">
                    <h6><i class="fal fa-list-alt mr-2"></i>Gider Kalemleri</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Kalem</th>
                                    <th>Tutar</th>
                                    <th>Açıklama</th>
                                </tr>
                            </thead>
                            <tbody id="expenseDetails{{ $expense->id }}" class="expense-details-tbody">
                                <tr>
                                    <td colspan="4" class="text-center">Yükleniyor...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-info" onclick="window.print()">
                    <i class="fal fa-print mr-1"></i>Yazdır
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

@include('layouts.footer')

<script src="{{ asset('js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script>
    let expenseTable;
    
    $(document).ready(function() {
        initializeApp();
        bindEvents();
    });

    function initializeApp() {
        // Laravel flash messages
        @if(session('success'))
            showSuccess('{{ session('success') }}');
        @endif
        
        @if(session('error'))
            showError('{{ session('error') }}');
        @endif

        // Initialize DataTable
        expenseTable = $('#dt-basic-example').DataTable({
            responsive: true,
            language: { url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Turkish.json' },
            dom: "<'row mb-3'<'col-md-6'f><'col-md-6 text-right'B>><'row'<'col-12'tr>><'row'<'col-md-5'i><'col-md-7'p>>",
            buttons: [
                { extend: 'excel', text: '<i class="fal fa-file-excel"></i> Excel', className: 'btn-success btn-sm' },
                { extend: 'pdf', text: '<i class="fal fa-file-pdf"></i> PDF', className: 'btn-danger btn-sm' },
                { extend: 'print', text: '<i class="fal fa-print"></i> Yazdır', className: 'btn-primary btn-sm' }
            ],
            pageLength: 25,
            stateSave: true,
            deferRender: true
        });
    }

    function bindEvents() {
        // Filter events
        $('#startDate, #endDate, #expenseType, #account').on('change input', debounce(filterTable, 300));

        // Delete confirmation
        $(document).on('click', '.delete-expense-btn', handleDeleteClick);

        // Detail modal events
        $('[id^="detailsModal"]').on('show.bs.modal', handleDetailModalShow);
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function filterTable() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        const expenseType = $('#expenseType').val();
        const account = $('#account').val();

        expenseTable.columns().every(function() {
            const column = this;
            const columnIndex = column.index();
            
            if (columnIndex === 0) { // Date column
                if (startDate || endDate) {
                    column.search(`${startDate}|${endDate}`, true, false);
                } else {
                    column.search('');
                }
            } else if (columnIndex === 2) { // Expense type column
                if (expenseType) {
                    column.search(expenseType, true, false);
                } else {
                    column.search('');
                }
            } else if (columnIndex === 3) { // Account column
                if (account) {
                    column.search(account, true, false);
                } else {
                    column.search('');
                }
            }
        });
        
        expenseTable.draw();
    }

    function handleDeleteClick(e) {
        e.preventDefault();
        const expenseId = $(this).data('expense-id');
        
        if (confirm('Bu gider kaydını silmek istediğinizden emin misiniz?')) {
            $.ajax({
                url: `/expenses/delete/${expenseId}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        showSuccess(response.message);
                        location.reload();
                    } else {
                        showError(response.message);
                    }
                },
                error: function(xhr) {
                    showError('Gider kaydı silinirken bir hata oluştu.');
                }
            });
        }
    }

    function handleDetailModalShow(e) {
        const modal = $(e.target);
        const expenseId = modal.attr('id').replace('detailsModal', '');
        const tbody = modal.find('.expense-details-tbody');
        
        // Load expense items
        $.ajax({
            url: `/ajax/expense-items/${expenseId}`,
            type: 'GET',
            success: function(response) {
                tbody.empty();
                if (response.items && response.items.length > 0) {
                    response.items.forEach(function(item) {
                        tbody.append(`
                            <tr>
                                <td>${item.category_name || '-'}</td>
                                <td>${item.item_name}</td>
                                <td>${parseFloat(item.amount).toFixed(2)} ₺</td>
                                <td>${item.description || '-'}</td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="4" class="text-center text-muted">Kalem bulunamadı</td></tr>');
                }
            },
            error: function() {
                tbody.html('<tr><td colspan="4" class="text-center text-danger">Kalemler yüklenirken hata oluştu</td></tr>');
            }
        });
    }

    function showSuccess(message) {
        toastr.success(message);
    }

    function showError(message) {
        toastr.error(message);
    }

    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        if (expenseTable) {
            expenseTable.destroy();
        }
    });
</script>