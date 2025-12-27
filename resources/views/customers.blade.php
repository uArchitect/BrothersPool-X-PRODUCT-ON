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
                <div class="panel-hdr">
                    <h2>Müşteri Yönetimi</h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-primary btn-sm mr-2" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                            <i class="fal fa-plus mr-1"></i> Yeni Müşteri
                        </button>
                        <button class="btn btn-panel" data-action="panel-collapse" title="Daralt"></button>
                        <button class="btn btn-panel" data-action="panel-fullscreen" title="Tam Ekran"></button>
                    </div>
                </div>

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
                                    <th style="width: 120px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                <tr @if($customer->allergy) class="allergy-row" title="Alerji Uyarısı" @endif>
                                    <td class="text-center">
                                        <i class="fal {{ $customer->allergy ? 'fa-exclamation-triangle text-danger' : 'fa-user text-success' }}"></i>
                                    </td>
                                    <td>
                                        <div class="customer-info">
                                            <div class="customer-avatar rounded-circle">
                                                <span class="avatar-initials">{{ strtoupper(substr($customer->first_name,0,1)) }}{{ strtoupper(substr($customer->last_name,0,1)) }}</span>
                                            </div>
                                            <div class="customer-name">
                                                {{ $customer->first_name }} {{ $customer->last_name }}
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
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-xs btn-info" data-bs-toggle="modal" data-bs-target="#viewCustomerModal{{ $customer->id }}">
                                                <i class="fal fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-primary" data-bs-toggle="modal" data-bs-target="#editCustomerModal{{ $customer->id }}">
                                                <i class="fal fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-danger btn-delete-customer" data-id="{{ $customer->id }}" data-name="{{ $customer->first_name }} {{ $customer->last_name }}">
                                                <i class="fal fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @once
                                    @push('modals')
                                @endonce
                                @include('layouts.module.customer_edit_modal', ['customer' => $customer])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.module.view_customer_modal', ['customers' => $customers])
@include('layouts.footer')

@stack('modals')

<style>
.dataTables_wrapper .dataTables_filter{float:left;margin-left:0}
.dataTables_wrapper .dataTables_length{float:right}
.dataTables_filter input{width:300px}
.customer-avatar{width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:linear-gradient(45deg,#2196F3,#1976D2);margin-right:12px}
.avatar-initials{font-size:16px;font-weight:500;color:white;text-transform:uppercase}
.customer-info{display:flex;align-items:center}
.customer-name{font-size:14px;font-weight:600;color:#333}
.allergy-row{background-color:#fff3f3}
.allergy-row:hover{background-color:#ffe6e6}
.gap-2{gap:0.5rem}
</style>

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
            { orderable: false, targets: [0, 3] },
            { searchable: false, targets: [0, 3] }
        ]
    });

    $(document).on('click', '.btn-delete-customer', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
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
                window.location.href = `/customers/delete/${id}`;
            }
        });
    });
});
</script>
