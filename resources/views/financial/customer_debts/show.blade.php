@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('financial.management') }}">Finansal İşlemler</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customer-debts.index') }}">Borç Yönetimi</a></li>
        <li class="breadcrumb-item active">{{ $customer->title ?? $customer->name ?? 'Müşteri' }}</li>
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
                        <!-- Müşteri Bilgileri ve Özet -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Toplam Borç</h6>
                                        <h3 class="mb-0">₺{{ number_format($summary['total_debt'] ?? 0, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Ödenen</h6>
                                        <h3 class="mb-0">₺{{ number_format($summary['total_paid'] ?? 0, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Kalan Borç</h6>
                                        <h3 class="mb-0">₺{{ number_format($summary['total_remaining'] ?? 0, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Ödenmemiş</h6>
                                        <h3 class="mb-0">{{ $summary['unpaid_count'] ?? 0 }}</h3>
                                        <small>adet borç</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filtreler ve İşlemler -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('customer-debts.show', $customer->id) }}" 
                                           class="btn btn-sm {{ !$status ? 'btn-primary' : 'btn-secondary' }}">
                                            Tümü
                                        </a>
                                        <a href="{{ route('customer-debts.show', [$customer->id, 'status' => 'unpaid']) }}" 
                                           class="btn btn-sm {{ $status == 'unpaid' ? 'btn-danger' : 'btn-secondary' }}">
                                            Ödenmemiş
                                        </a>
                                        <a href="{{ route('customer-debts.show', [$customer->id, 'status' => 'partial']) }}" 
                                           class="btn btn-sm {{ $status == 'partial' ? 'btn-warning' : 'btn-secondary' }}">
                                            Kısmi Ödenmiş
                                        </a>
                                        <a href="{{ route('customer-debts.show', [$customer->id, 'status' => 'paid']) }}" 
                                           class="btn btn-sm {{ $status == 'paid' ? 'btn-success' : 'btn-secondary' }}">
                                            Ödenmiş
                                        </a>
                                    </div>
                                    <div class="mt-2 mt-md-0">
                                        <a href="{{ route('expenses.create', ['customer_id' => $customer->id]) }}" 
                                           class="btn btn-sm btn-secondary">
                                            <i class="fal fa-plus-circle mr-1"></i>Borç Oluştur
                                        </a>
                                        <a href="{{ route('customers.show', $customer->id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fal fa-user mr-1"></i>Müşteri Detayı
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Borç Listesi -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fal fa-list mr-2"></i>Borç Listesi
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead class="bg-highlight">
                                            <tr>
                                                <th>Gider No</th>
                                                <th>Tarih</th>
                                                <th>Açıklama</th>
                                                <th>Borç Tutarı</th>
                                                <th>Ödenen</th>
                                                <th>Kalan</th>
                                                <th>Durum</th>
                                                <th style="width: 150px;">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($debts as $debt)
                                                <tr>
                                                    <td><strong>{{ $debt->expense_number }}</strong></td>
                                                    <td>{{ \Carbon\Carbon::parse($debt->debt_date)->format('d.m.Y') }}</td>
                                                    <td>{{ $debt->description ?? '-' }}</td>
                                                    <td class="text-danger font-weight-bold">
                                                        ₺{{ number_format($debt->debt_amount, 2) }}
                                                    </td>
                                                    <td class="text-success">
                                                        ₺{{ number_format($debt->paid_amount, 2) }}
                                                    </td>
                                                    <td class="font-weight-bold {{ $debt->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                        ₺{{ number_format($debt->remaining_amount, 2) }}
                                                    </td>
                                                    <td>
                                                        @if($debt->is_paid)
                                                            <span class="badge badge-success">Ödendi</span>
                                                        @elseif($debt->payment_status == 'partial')
                                                            <span class="badge badge-warning">Kısmi Ödenmiş</span>
                                                        @else
                                                            <span class="badge badge-danger">Ödenmemiş</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-1">
                                                            @if(!$debt->is_paid)
                                                                <button type="button" class="btn btn-xs btn-primary" 
                                                                        onclick="openPaymentModal({{ $debt->id }}, {{ $debt->remaining_amount }}, '{{ $debt->expense_number }}')"
                                                                        title="Borç Ödemesi Yap">
                                                                    <i class="fal fa-money-bill-wave"></i>
                                                                </button>
                                                            @endif
                                                            <a href="{{ route('expenses.edit', $debt->id) }}" 
                                                               class="btn btn-xs btn-warning" title="Borç Düzenle">
                                                                <i class="fal fa-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-xs btn-danger" 
                                                                    onclick="deleteDebt({{ $debt->id }})"
                                                                    title="Borç Sil">
                                                                <i class="fal fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-4">
                                                        <i class="fal fa-info-circle fa-2x text-muted mb-2"></i>
                                                        <p class="text-muted">Borç kaydı bulunamadı.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Ödeme Geçmişi -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fal fa-history mr-2"></i>Ödeme Geçmişi
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-highlight">
                                            <tr>
                                                <th>Ödeme No</th>
                                                <th>Tarih</th>
                                                <th>Ödeme Yöntemi</th>
                                                <th>Tutar</th>
                                                <th>Açıklama</th>
                                                <th style="width: 100px;">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($paymentHistory as $payment)
                                                <tr>
                                                    <td><strong>{{ $payment->payment_number }}</strong></td>
                                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d.m.Y') }}</td>
                                                    <td>
                                                        @if($payment->payment_method == 'cash')
                                                            <span class="badge badge-success">Nakit</span>
                                                        @elseif($payment->payment_method == 'card')
                                                            <span class="badge badge-info">Kart</span>
                                                        @else
                                                            <span class="badge badge-secondary">{{ ucfirst($payment->payment_method) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="font-weight-bold text-success">
                                                        ₺{{ number_format($payment->paid_amount, 2) }}
                                                    </td>
                                                    <td>{{ $payment->description ?? '-' }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                onclick="cancelPayment({{ $payment->id }})">
                                                            <i class="fal fa-times mr-1"></i>İptal
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <i class="fal fa-info-circle fa-2x text-muted mb-2"></i>
                                                        <p class="text-muted">Ödeme geçmişi bulunamadı.</p>
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

    <!-- Ödeme Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">
                        <i class="fal fa-money-bill-wave mr-2"></i>Borç Ödemesi
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="paymentForm" method="POST" action="{{ route('customer-debts.pay') }}">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    <input type="hidden" name="reference_id" id="reference_id">
                    <input type="hidden" name="reference_type" value="expense">
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Gider No</label>
                            <input type="text" class="form-control" id="expense_number" readonly>
                        </div>
                        <div class="form-group">
                            <label>Kalan Borç</label>
                            <input type="text" class="form-control" id="remaining_debt_display" readonly>
                        </div>
                        <div class="form-group">
                            <label for="account_id">Kasa Hesabı <span class="text-danger">*</span></label>
                            <select class="form-control" name="account_id" id="account_id" required>
                                <option value="">Seçiniz...</option>
                                @foreach($cashAccounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->name }} (Bakiye: ₺{{ number_format($account->balance, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="paid_amount">Ödeme Tutarı <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" max="999999999999.99" class="form-control" 
                                   name="paid_amount" id="paid_amount" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Ödeme Yöntemi <span class="text-danger">*</span></label>
                            <select class="form-control" name="payment_method" id="payment_method" required>
                                <option value="cash">Nakit</option>
                                <option value="card">Kart</option>
                                <option value="transfer">Havale/EFT</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="payment_date">Ödeme Tarihi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="payment_date" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notlar</label>
                            <textarea class="form-control" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fal fa-check mr-1"></i>Ödemeyi Tamamla
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')

<script>
function openPaymentModal(expenseId, remainingAmount, expenseNumber) {
    document.getElementById('reference_id').value = expenseId;
    document.getElementById('expense_number').value = expenseNumber;
    document.getElementById('remaining_debt_display').value = '₺' + remainingAmount.toFixed(2);
    // Max limit kaldırıldı - sınırsız ödeme yapılabilir
    const paidAmountField = document.getElementById('paid_amount');
    if (paidAmountField) {
        paidAmountField.removeAttribute('max');
    }
    document.getElementById('paid_amount').value = remainingAmount;
    $('#paymentModal').modal('show');
}

function cancelPayment(paymentId) {
    if (confirm('Bu ödemeyi iptal etmek istediğinizden emin misiniz?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("customer-debts.cancel-payment", ":id") }}'.replace(':id', paymentId);
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteDebt(expenseId) {
    if (confirm('Bu borç kaydını silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("expenses.destroy", ":id") }}'.replace(':id', expenseId);
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

