@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('financial.management') }}">Finansal İşlemler</a></li>
        <li class="breadcrumb-item active">Borç Yönetimi</li>
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
            <div id="panel-1" class="panel">
                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <div class="alert-icon">
                                    <i class="fal fa-money-bill-wave"></i>
                                </div>
                                <div class="flex-1 ml-2">
                                    <span class="h5">Borç Yönetimi</span>
                                    <br>Müşterilere atanan giderlerden kaynaklanan borçları kasa üzerinden ödeyebilirsiniz.
                                </div>
                            </div>
                        </div>

                        <!-- İşlemler -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                                            <i class="fal fa-plus-circle mr-1"></i>Borç Oluştur
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Müşteri Listesi -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fal fa-users mr-2"></i>Müşteriler ve Borç Durumları
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="dt-customer-debts" class="table table-bordered table-hover table-striped w-100">
                                        <thead class="bg-highlight">
                                            <tr>
                                                <th>Müşteri</th>
                                                <th>Toplam Borç</th>
                                                <th>Ödenen</th>
                                                <th>Kalan Borç</th>
                                                <th>Durum</th>
                                                <th style="width: 150px;">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($customers as $customer)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-initials rounded-circle bg-primary text-white mr-2" 
                                                                 style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                                                {{ strtoupper(substr($customer->title ?? $customer->name ?? 'M', 0, 1)) }}
                                                            </div>
                                                            <div>
                                                                <strong class="text-dark">{{ $customer->title ?? $customer->name ?? 'Müşteri' }}</strong>
                                                                @if(!empty($customer->code))
                                                                    <br><small class="text-muted">{{ $customer->code }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-dark font-weight-bold">
                                                            ₺{{ number_format($customer->debt_summary['total_debt'] ?? 0, 2) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted">
                                                            ₺{{ number_format($customer->debt_summary['total_paid'] ?? 0, 2) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="font-weight-bold {{ ($customer->debt_summary['total_remaining'] ?? 0) > 0 ? 'text-dark' : 'text-muted' }}">
                                                            ₺{{ number_format($customer->debt_summary['total_remaining'] ?? 0, 2) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if(($customer->debt_summary['total_remaining'] ?? 0) > 0)
                                                            <span class="badge badge-warning">
                                                                <i class="fal fa-exclamation-circle mr-1"></i>
                                                                {{ $customer->debt_summary['unpaid_count'] ?? 0 }} Ödenmemiş
                                                            </span>
                                                        @else
                                                            <span class="badge badge-secondary">
                                                                <i class="fal fa-check-circle mr-1"></i>Ödendi
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('customer-debts.show', $customer->id) }}" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fal fa-eye mr-1"></i>Detay
                                                        </a>
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
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')

<!-- DataTables CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#dt-customer-debts').DataTable({
        responsive: true,
        stateSave: true,
        pageLength: 25,
        order: [[3, 'desc']], // Kalan borç sütununa göre sırala
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json'
        },
        columnDefs: [
            { orderable: false, targets: [5] }, // İşlemler sütunu sıralanamaz
            { searchable: false, targets: [5] }  // İşlemler sütunu aranabilir değil
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function() {
            // Tablo çizildikten sonra özel işlemler
        }
    });
});
</script>

