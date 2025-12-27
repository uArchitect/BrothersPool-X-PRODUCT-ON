@include('layouts.header')

<style>
.summary-box {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.summary-box h6 {
    color: #6c757d;
    font-size: 0.85rem;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.summary-box .amount {
    font-size: 1.5rem;
    font-weight: bold;
    color: #212529;
}

.summary-box .amount.positive {
    color: #28a745;
}

.summary-box .amount.negative {
    color: #dc3545;
}

.info-row {
    padding: 12px 0;
    border-bottom: 1px solid #e9ecef;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    color: #6c757d;
    font-weight: 500;
    width: 150px;
    display: inline-block;
}

.info-value {
    color: #212529;
}

.status-badge {
    padding: 0.4em 0.8em;
    border-radius: 4px;
    font-size: 0.85em;
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

.simple-table {
    font-size: 0.9rem;
}

.simple-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.action-buttons {
    display: flex;
    gap: 5px;
}
</style>

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('hr.management') }}">İnsan Kaynakları</a></li>
        <li class="breadcrumb-item"><a href="{{ route('payrolls.index') }}">Bordrolar</a></li>
        <li class="breadcrumb-item active">Bordro Detayı</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>Bordro Detayı</h2>
                    <div class="panel-toolbar">
                        <a href="{{ route('payrolls.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fal fa-arrow-left mr-1"></i> Geri Dön
                        </a>
                        @if($payroll->status != 'cancelled' && $payroll->total_paid == 0)
                        <form action="{{ route('payrolls.cancel', $payroll->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bu bordroyu iptal etmek istediğinize emin misiniz?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">
                                <i class="fal fa-ban mr-1"></i> İptal Et
                            </button>
                        </form>
                        <form action="{{ route('payrolls.destroy', $payroll->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bu bordroyu silmek istediğinize emin misiniz?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fal fa-trash mr-1"></i> Sil
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                <div class="panel-container show">
                    <div class="panel-content">
                        
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

                        <!-- Özet Bilgiler -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="summary-box text-center">
                                    <h6>Net Maaş</h6>
                                    <div class="amount">₺{{ number_format($payroll->net_salary, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-box text-center">
                                    <h6>Ödenen</h6>
                                    <div class="amount positive">₺{{ number_format($payroll->total_paid, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-box text-center">
                                    <h6>Kalan</h6>
                                    <div class="amount {{ $payroll->remaining_amount > 0 ? 'negative' : 'positive' }}">₺{{ number_format($payroll->remaining_amount, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-box text-center">
                                    <h6>Durum</h6>
                                    <div>
                                        @if($payroll->status == 'paid')
                                            <span class="badge status-badge status-paid">Ödendi</span>
                                        @elseif($payroll->status == 'partial')
                                            <span class="badge status-badge status-partial">Kısmi</span>
                                        @elseif($payroll->status == 'pending')
                                            <span class="badge status-badge status-pending">Beklemede</span>
                                        @elseif($payroll->status == 'cancelled')
                                            <span class="badge status-badge status-cancelled">İptal</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Temel Bilgiler -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fal fa-info-circle mr-2"></i>Bordro Bilgileri</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-row">
                                            <span class="info-label">Personel:</span>
                                            <span class="info-value"><strong>{{ $payroll->employee_name }}</strong></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Periyot:</span>
                                            <span class="info-value">
                                                {{ \Carbon\Carbon::parse($payroll->period_start_date)->format('d.m.Y') }} - 
                                                {{ \Carbon\Carbon::parse($payroll->period_end_date)->format('d.m.Y') }}
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Çalışılan Gün:</span>
                                            <span class="info-value"><strong>{{ $payroll->working_days }} gün</strong></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-row">
                                            <span class="info-label">Günlük Ücret:</span>
                                            <span class="info-value">₺{{ number_format($payroll->daily_wage, 2) }}</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Brüt Maaş:</span>
                                            <span class="info-value">₺{{ number_format($payroll->gross_salary, 2) }}</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Kesintiler:</span>
                                            <span class="info-value text-danger">-₺{{ number_format($payroll->deductions, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kesintiler -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fal fa-minus-circle mr-2"></i>Kesintiler</h5>
                                @if($payroll->status != 'cancelled')
                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#addDeductionModal">
                                    <i class="fal fa-plus mr-1"></i> Kesinti Ekle
                                </button>
                                @endif
                            </div>
                            <div class="card-body">
                                @if($deductions->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm simple-table">
                                            <thead>
                                                <tr>
                                                    <th>Kesinti Tipi</th>
                                                    <th class="text-right">Tutar</th>
                                                    <th class="text-right">Oran</th>
                                                    <th>Açıklama</th>
                                                    @if($payroll->status != 'cancelled')
                                                    <th width="60">İşlem</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($deductions as $deduction)
                                                <tr>
                                                    <td>
                                                        @php
                                                            $deductionTypes = [
                                                                'sgk_employee' => 'SGK İşçi Payı',
                                                                'sgk_employer' => 'SGK İşveren Payı',
                                                                'tax' => 'Vergi',
                                                                'stamp_duty' => 'Damga Vergisi',
                                                                'other' => 'Diğer'
                                                            ];
                                                        @endphp
                                                        {{ $deductionTypes[$deduction->deduction_type] ?? $deduction->deduction_type }}
                                                    </td>
                                                    <td class="text-right">₺{{ number_format($deduction->amount, 2) }}</td>
                                                    <td class="text-right">{{ $deduction->rate ? number_format($deduction->rate, 2) . '%' : '-' }}</td>
                                                    <td>{{ $deduction->description ?? '-' }}</td>
                                                    @if($payroll->status != 'cancelled')
                                                    <td>
                                                        <form action="{{ route('payrolls.deduction.delete', [$payroll->id, $deduction->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Silinsin mi?');">
                                                            @csrf
                                                            <button type="submit" class="btn btn-xs btn-danger" title="Sil">
                                                                <i class="fal fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light">
                                                    <th>Toplam</th>
                                                    <th class="text-right">₺{{ number_format($deductions->sum('amount'), 2) }}</th>
                                                    <th colspan="{{ $payroll->status != 'cancelled' ? '3' : '2' }}"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted text-center mb-0">Henüz kesinti kaydı bulunmuyor.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Ödemeler -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fal fa-money-bill-wave mr-2"></i>Ödemeler</h5>
                                @if($payroll->remaining_amount > 0)
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addPaymentModal">
                                        <i class="fal fa-plus mr-1"></i> Ödeme Ekle
                                    </button>
                                @endif
                            </div>
                            <div class="card-body">
                                @if($payments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm simple-table">
                                            <thead>
                                                <tr>
                                                    <th>Tarih</th>
                                                    <th class="text-right">Tutar</th>
                                                    <th>Yöntem</th>
                                                    <th>Banka</th>
                                                    <th>Açıklama</th>
                                                    @if($payroll->status != 'cancelled')
                                                    <th width="60">İşlem</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($payments as $payment)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d.m.Y') }}</td>
                                                    <td class="text-right"><strong>₺{{ number_format($payment->amount, 2) }}</strong></td>
                                                    <td>
                                                        @php
                                                            $methods = [
                                                                'cash' => 'Nakit',
                                                                'bank_transfer' => 'Banka',
                                                                'check' => 'Çek',
                                                                'other' => 'Diğer'
                                                            ];
                                                        @endphp
                                                        {{ $methods[$payment->payment_method] ?? $payment->payment_method }}
                                                    </td>
                                                    <td>{{ $payment->bank_name ?? '-' }}</td>
                                                    <td>{{ $payment->description ?? '-' }}</td>
                                                    @if($payroll->status != 'cancelled' && $payment->status == 'completed')
                                                    <td>
                                                        <form action="{{ route('payrolls.payment.delete', [$payroll->id, $payment->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Silinsin mi?');">
                                                            @csrf
                                                            <button type="submit" class="btn btn-xs btn-danger" title="Sil">
                                                                <i class="fal fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                    @elseif($payroll->status != 'cancelled')
                                                    <td></td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light">
                                                    <th>Toplam Ödenen</th>
                                                    <th class="text-right">₺{{ number_format($payroll->total_paid, 2) }}</th>
                                                    <th colspan="{{ $payroll->status != 'cancelled' ? '4' : '3' }}"></th>
                                                </tr>
                                                @if($payroll->remaining_amount > 0)
                                                <tr class="bg-warning-light">
                                                    <th>Kalan Tutar</th>
                                                    <th class="text-right text-warning">₺{{ number_format($payroll->remaining_amount, 2) }}</th>
                                                    <th colspan="{{ $payroll->status != 'cancelled' ? '4' : '3' }}"></th>
                                                </tr>
                                                @endif
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fal fa-inbox fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-3">Henüz ödeme kaydı bulunmuyor</p>
                                        @if($payroll->remaining_amount > 0)
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addPaymentModal">
                                                <i class="fal fa-plus mr-1"></i> İlk Ödemeyi Ekle
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Ödeme Ekleme Modal -->
@if($payroll->remaining_amount > 0)
<div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Yeni Ödeme Ekle</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('payrolls.add.payment', $payroll->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <small>Kalan tutar: <strong>₺{{ number_format($payroll->remaining_amount, 2) }}</strong></small>
                    </div>
                    <div class="form-group">
                        <label>Ödeme Tarihi <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Tutar <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="amount" step="0.01" min="0.01" max="{{ $payroll->remaining_amount }}" required>
                        <small class="text-muted">Maksimum: ₺{{ number_format($payroll->remaining_amount, 2) }}</small>
                    </div>
                    <div class="form-group">
                        <label>Ödeme Yöntemi <span class="text-danger">*</span></label>
                        <select class="form-control" name="payment_method" required>
                            <option value="cash">Nakit</option>
                            <option value="bank_transfer">Banka Transfer</option>
                            <option value="check">Çek</option>
                            <option value="other">Diğer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Banka Adı</label>
                        <input type="text" class="form-control" name="bank_name" value="{{ $payroll->bank_name ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label>Hesap No / IBAN</label>
                        <input type="text" class="form-control" name="account_number" value="{{ $payroll->iban ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label>Açıklama</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Kesinti Ekleme Modal -->
@if($payroll->status != 'cancelled')
<div class="modal fade" id="addDeductionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Kesinti Ekle</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('payrolls.add.deduction', $payroll->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kesinti Tipi <span class="text-danger">*</span></label>
                        <select class="form-control" name="deduction_type" required>
                            <option value="sgk_employee">SGK İşçi Payı</option>
                            <option value="sgk_employer">SGK İşveren Payı</option>
                            <option value="tax">Vergi</option>
                            <option value="stamp_duty">Damga Vergisi</option>
                            <option value="other">Diğer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tutar <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="amount" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Oran (%)</label>
                        <input type="number" class="form-control" name="rate" step="0.01" min="0" max="100">
                    </div>
                    <div class="form-group">
                        <label>Açıklama</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-warning">Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@include('layouts.footer')
