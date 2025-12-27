@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Müşteriler</li>
        <li class="breadcrumb-item active">Müşteri Listesi</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    @if(session('success'))
        <script>window.addEventListener('DOMContentLoaded', () => showSuccess('{{ session('success') }}'));</script>
    @endif

    @if(session('error'))
        <script>window.addEventListener('DOMContentLoaded', () => showError('{{ session('error') }}'));</script>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="panel" id="panel-1">

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <div class="alert-icon">
                                    <i class="fal fa-info-circle"></i>
                                </div>
                                <div class="flex-1 ml-2">
                                    <span class="h5">Müşteri Yönetim Paneli</span>
                                    <br>Müşteri ekle, düzenle veya detaylarını görüntüle.
                                </div>
                            </div>
                        </div>

                        <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-highlight">
                                <tr>
                                    <th class="text-center" style="width: 20px;"><i class="fal fa-user" title="Müşteri"></i></th>
                                    <th>Müşteri Bilgileri</th>
                                    <th>İletişim</th>
                                    <th>Bakiye</th>
                                    <th>Borç Durumu</th>
                                    <th style="width: 200px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                <tr>
                                    <td class="text-center">
                                        <i class="fal fa-user text-success"></i>
                                    </td>
                                    <td>
                                        <div class="customer-info">
                                            <div class="customer-avatar rounded-circle">
                                                <span class="avatar-initials">{{ strtoupper(substr($customer->title ?? 'M',0,1)) }}{{ strtoupper(substr($customer->code ?? 'C',0,1)) }}</span>
                                            </div>
                                            <div class="customer-name">
                                                {{ $customer->title ?? 'Müşteri' }}
                                                @if($customer->code)
                                                    <small class="text-muted">({{ $customer->code }})</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            @if($customer->phone)
                                                <div><i class="fal fa-phone-alt text-primary mr-1"></i><a href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a></div>
                                            @endif
                                            @if($customer->email)
                                                <div><i class="fal fa-envelope text-primary mr-1"></i><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($customer->current_balance > 0)
                                            <span class="badge badge-success">
                                                <i class="fal fa-arrow-up mr-1"></i>Alacağım: ₺{{ number_format($customer->current_balance, 2) }}
                                            </span>
                                        @elseif($customer->current_balance < 0)
                                            <span class="badge badge-danger">
                                                <i class="fal fa-arrow-down mr-1"></i>Borç: ₺{{ number_format(abs($customer->current_balance), 2) }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fal fa-balance-scale mr-1"></i>Dengeli
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // Borç özetini hesapla
                                            $debtSummary = app(\App\Services\CustomerDebtService::class)->getCustomerDebtSummary($customer->id);
                                        @endphp
                                        @if($debtSummary['total_remaining'] > 0)
                                            <span class="badge badge-danger">
                                                <i class="fal fa-exclamation-circle mr-1"></i>
                                                Kalan Borç: ₺{{ number_format($debtSummary['total_remaining'], 2) }}
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $debtSummary['unpaid_count'] }} ödenmemiş
                                                @if($debtSummary['partial_count'] > 0)
                                                    / {{ $debtSummary['partial_count'] }} kısmi
                                                @endif
                                            </small>
                                        @elseif($debtSummary['total_debt'] > 0)
                                            <span class="badge badge-success">
                                                <i class="fal fa-check-circle mr-1"></i>Ödendi
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fal fa-minus mr-1"></i>Borç yok
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-xs btn-info" title="Görüntüle">
                                                <i class="fal fa-eye"></i>
                                            </a>
                                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-xs btn-warning" title="Düzenle">
                                                <i class="fal fa-edit"></i>
                                            </a>
                                            <a href="{{ route('customers.transactions.index', $customer->id) }}" class="btn btn-xs btn-primary" title="Hareketler">
                                                <i class="fal fa-list"></i>
                                            </a>
                                            <button type="button" class="btn btn-xs btn-danger btn-delete-customer" data-id="{{ $customer->id }}" data-name="{{ $customer->title ?? 'Müşteri' }}">
                                                <i class="fal fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')


<script defer src="{{ asset('js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#dt-basic-example').DataTable({
        responsive: true,
        stateSave: true,
        pageLength: 25,
        order: [[1, 'asc']],
        language: { url: "{{ asset('media/data/tr.json') }}" },
        columnDefs: [
            { orderable: false, targets: [0, 5] },
            { searchable: false, targets: [0, 5] }
        ]
    });

    $(document).on('click', '.btn-delete-customer', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const button = $(this);
        
        Swal.fire({
            title: 'Emin misiniz?',
            html: `<strong>${name}</strong> isimli müşteriyi silmek istiyor musunuz?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet, Sil',
            cancelButtonText: 'İptal'
        }).then(result => {
            if (result.isConfirmed) {
                button.prop('disabled', true);
                button.html('<i class="fal fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: `/customers/${id}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı',
                            text: 'Müşteri başarıyla silindi.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Müşteri silinirken bir hata oluştu.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata',
                            text: errorMessage
                        });
                        button.prop('disabled', false);
                        button.html('<i class="fal fa-trash"></i>');
                    }
                });
            }
        });
    });
});
</script>