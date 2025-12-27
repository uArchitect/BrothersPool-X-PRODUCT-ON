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

.status-badge {
    font-size: 0.85em;
    padding: 0.35em 0.7em;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-partial {
    background-color: #d1ecf1;
    color: #0c5460;
}

.status-paid {
    background-color: #d4edda;
    color: #155724;
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
                        
                        <!-- Başlık ve Butonlar -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="text-primary mb-1"><i class="fal fa-money-check-alt mr-2"></i>Bordro Listesi</h4>
                                <p class="text-muted mb-0">Tüm bordroları görüntüleyin ve yönetin</p>
                            </div>
                            <div>
                                <a href="{{ route('hr.management') }}" class="btn btn-sm btn-secondary">
                                    <i class="fal fa-arrow-left mr-1"></i> Geri Dön
                                </a>
                                <a href="{{ route('payrolls.create') }}" class="btn btn-sm btn-success">
                                    <i class="fal fa-plus mr-1"></i> Yeni Bordro
                                </a>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fal fa-check-circle mr-2"></i>{{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fal fa-exclamation-circle mr-2"></i>{{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        <!-- Bordro Listesi -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fal fa-list mr-2"></i>Bordrolar</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped w-100" id="payrollsTable">
                                        <thead class="bg-success-600 text-white">
                                            <tr>
                                                <th>Bordro Periyodu</th>
                                                <th>Personel</th>
                                                <th>Periyot Tipi</th>
                                                <th>Başlangıç</th>
                                                <th>Bitiş</th>
                                                <th>Çalışılan Gün</th>
                                                <th>Net Maaş</th>
                                                <th>Ödenen</th>
                                                <th>Kalan</th>
                                                <th>Durum</th>
                                                <th class="text-center" style="width: 120px;">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($payrolls as $payroll)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $payroll->payroll_period }}</strong>
                                                    </td>
                                                    <td>{{ $payroll->employee_name }}</td>
                                                    <td>
                                                        @php
                                                            $periodTypes = [
                                                                'daily' => 'Günlük',
                                                                'weekly' => 'Haftalık',
                                                                'monthly' => 'Aylık',
                                                                'hourly' => 'Saatlik'
                                                            ];
                                                        @endphp
                                                        <span class="badge badge-info">{{ $periodTypes[$payroll->period_type] ?? $payroll->period_type }}</span>
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($payroll->period_start_date)->format('d.m.Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($payroll->period_end_date)->format('d.m.Y') }}</td>
                                                    <td class="text-center">{{ $payroll->working_days }}</td>
                                                    <td class="text-right">
                                                        <strong class="text-success">₺{{ number_format($payroll->net_salary, 2) }}</strong>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-primary">₺{{ number_format($payroll->total_paid, 2) }}</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-warning">₺{{ number_format($payroll->remaining_amount, 2) }}</span>
                                                    </td>
                                                    <td>
                                                        @if($payroll->status == 'paid')
                                                            <span class="badge status-badge status-paid">Ödendi</span>
                                                        @elseif($payroll->status == 'partial')
                                                            <span class="badge status-badge status-partial">Kısmi</span>
                                                        @elseif($payroll->status == 'pending')
                                                            <span class="badge status-badge status-pending">Beklemede</span>
                                                        @elseif($payroll->status == 'cancelled')
                                                            <span class="badge status-badge status-cancelled">İptal</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('payrolls.show', $payroll->id) }}" 
                                                               class="btn btn-info btn-sm" title="Görüntüle">
                                                                <i class="fal fa-eye"></i>
                                                            </a>
                                                            @if($payroll->status != 'cancelled' && $payroll->total_paid == 0)
                                                            <form action="{{ route('payrolls.cancel', $payroll->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bu bordroyu iptal etmek istediğinize emin misiniz?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-warning btn-sm" title="İptal Et">
                                                                    <i class="fal fa-ban"></i>
                                                                </button>
                                                            </form>
                                                            @endif
                                                            @if($payroll->status != 'cancelled' && $payroll->total_paid == 0)
                                                            <form action="{{ route('payrolls.destroy', $payroll->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bu bordroyu silmek istediğinize emin misiniz? Bu işlem geri alınamaz!');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Sil">
                                                                    <i class="fal fa-trash"></i>
                                                                </button>
                                                            </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="11" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fal fa-inbox fa-3x mb-3"></i>
                                                            <p>Henüz bordro kaydı bulunmuyor.</p>
                                                            <a href="{{ route('payrolls.create') }}" class="btn btn-success">
                                                                <i class="fal fa-plus mr-1"></i> İlk Bordroyu Oluştur
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

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
$(document).ready(function() {
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#payrollsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json"
            },
            "pageLength": 25,
            "order": [[3, "desc"]], // Sort by start date descending
            "columnDefs": [
                { "orderable": false, "targets": 10 } // Disable sorting on actions column
            ]
        });
    }
});
</script>

@include('layouts.footer')

