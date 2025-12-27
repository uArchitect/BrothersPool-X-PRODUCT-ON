@include('layouts.header')
@include('layouts.module.account_transaction_add_modal')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Mali İşlemler</li>
        <li class="breadcrumb-item active">Kasa ve Banka Hesapları</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr d-flex justify-content-between align-items-center">
                    <h2>
                        Kasa ve Banka Hesapları <span class="fw-300"><i>Yönetim</i></span>
                    </h2>
                    <div class="panel-toolbar">
                        <a href="{{ route('accounts.create') }}" class="btn btn-success waves-effect waves-themed mr-2">
                            <i class="fal fa-plus-circle mr-1"></i>
                            Hesap Ekle
                        </a>
                        <button class="btn btn-info waves-effect waves-themed mr-2" data-bs-toggle="modal"
                            data-bs-target="#paymentAddModal" style="display: none;">
                            <i class="fal fa-money-bill-wave mr-1"></i>
                            Ödeme Al
                        </button>
                    </div>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Başarılı!</strong> {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Hata!</strong> {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="panel-tag mb-4">
                            Bu bölümde kasa ve banka hesaplarınızı yönetebilirsiniz.
                            Hesaplarınızın bakiyelerini takip edebilir, yeni hesap ekleyebilir ve mevcut hesapları düzenleyebilirsiniz.
                        </div>

                        <div class="table-responsive">
                            <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
                                <thead class="bg-highlight">
                                    <tr>
                                        <th>Hesap Adı</th>
                                        <th>Açıklama</th>
                                        <th>Kalan Tutar</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody id="accounts-table-body">
                                    @foreach ($accounts as $account)
                                        <tr id="account-row-{{ $account->id }}">
                                            <td>
                                                <strong>{{ $account->name }}</strong>
                                                @if($account->type)
                                                    <br><small class="text-muted">{{ ucfirst($account->type) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $account->description ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $balance = $account->current_balance ?? 0;
                                                    $balanceClass = $balance >= 0 ? 'text-success' : 'text-danger';
                                                @endphp
                                                <strong class="{{ $balanceClass }}" style="font-size: 1.1em;">
                                                    {{ number_format($balance, 2) }} ₺
                                                </strong>
                                                @if(($account->total_income ?? 0) > 0 || ($account->total_expense ?? 0) > 0)
                                                    <br>
                                                    <small class="text-muted">
                                                        <span class="text-success">+{{ number_format($account->total_income ?? 0, 2) }}</span>
                                                        <span class="text-danger"> -{{ number_format($account->total_expense ?? 0, 2) }}</span>
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('accounts.edit', $account->id) }}" 
                                                   class="btn btn-warning btn-sm" title="Düzenle">
                                                    <i class="fal fa-pencil-alt"></i>
                                                </a>
                                                <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="tooltip"
                                                    title="Hesap Sil" onclick="deleteAccount({{ $account->id }})">
                                                    <i class="fal fa-trash"></i>
                                                </a>
                                                <a href="{{ route('account-transactions.index', ['account_id' => $account->id]) }}" 
                                                   class="btn btn-info btn-sm" title="İşlemleri Görüntüle">
                                                    <i class="fal fa-list"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> <!-- end panel -->
        </div> <!-- end col -->
    </div> <!-- end row -->

</main>

@include('layouts.footer')

<script src="{{ asset('js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Setup - add a text input to each footer cell with better placeholders
        $('#dt-basic-example thead tr').clone(true).appendTo('#dt-basic-example thead');
        $('#dt-basic-example thead tr:eq(1) th').each(function(i) {
            var title = $(this).text().trim();
            var placeholder = '';
            
            switch(i) {
                case 0:
                    placeholder = 'Hesap adı ara...';
                    break;
                case 1:
                    placeholder = 'Açıklama ara...';
                    break;
                case 2:
                    placeholder = 'Bakiye ara...';
                    break;
                case 3:
                    placeholder = '';
                    $(this).html('');
                    return;
                default:
                    placeholder = title + ' ara...';
                    break;
            }
            
            if (placeholder) {
                $(this).html(
                    '<input type="text" class="form-control form-control-sm" placeholder="' +
                    placeholder + '" />');

                $('input', this).on('keyup change', function() {
                    if (table.column(i).search() !== this.value) {
                        table
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            }
        });

        var table = $('#dt-basic-example').DataTable({
            orderCellsTop: true,
            fixedHeader: true,
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            lengthChange: false,
            columnDefs: [
                { orderable: false, targets: [3] }
            ],
            language: {
                paginate: {
                    first: "İlk",
                    last: "Son",
                    next: "Sonraki",
                    previous: "Önceki"
                },
                info: "Gösterilen: _START_ - _END_ / _TOTAL_",
                infoEmpty: "Gösterilecek kayıt yok",
                zeroRecords: "Eşleşen kayıt bulunamadı",
                lengthMenu: "Sayfa başına _MENU_ kayıt göster"
            }
        });

    });

    // SweetAlert delete confirmation ve AJAX ile silme
    function deleteAccount(accountId) {
        Swal.fire({
            title: 'Hesap Silinsin Mi?',
            text: "Bu işlem geri alınamaz!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet, Sil!',
            cancelButtonText: 'Hayır'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/bank/hesaplar/' + accountId,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            showSuccess(response.message || 'Hesap başarıyla silindi');
                            // DataTable'dan satırı kaldır
                            var table = $('#dt-basic-example').DataTable();
                            table.row('#account-row-' + accountId).remove().draw();
                            // Alternatif olarak DOM'dan kaldır
                            $('#account-row-' + accountId).fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            showError(response.message || 'Hesap silinemedi');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Hesap silinemedi';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 404) {
                            errorMessage = 'Hesap bulunamadı';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Sunucu hatası oluştu';
                        }
                        showError(errorMessage);
                    }
                });
            }
        });
    }
</script>
