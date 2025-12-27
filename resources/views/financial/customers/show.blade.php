@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">

    <div class="row">
        <!-- Müşteri Bilgileri -->
        <div class="col-xl-8">
            <div id="panel-1" class="panel">

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Temel Bilgiler</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Kod:</strong></td>
                                        <td>{{ $customer->code ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ünvan:</strong></td>
                                        <td>{{ $customer->title ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Hesap Türü:</strong></td>
                                        <td>
                                            @if($customer->account_type)
                                                <span class="badge badge-info">{{ $customer->account_type }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Telefon:</strong></td>
                                        <td>{{ $customer->phone ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $customer->email ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Vergi Bilgileri</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Vergi Dairesi:</strong></td>
                                        <td>{{ $customer->tax_office ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Vergi No:</strong></td>
                                        <td>{{ $customer->tax_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Yetkili Kişi:</strong></td>
                                        <td>{{ $customer->authorized_person ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($customer->address)
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-primary">Adres</h6>
                                    <p class="text-muted">{{ $customer->address }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Mali Bilgiler -->
        <div class="col-xl-4">
            <div id="panel-2" class="panel">

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="mb-3">
                                    <h6 class="text-muted">Mevcut Bakiye</h6>
                                    @if($customer->current_balance > 0)
                                        <h3 class="font-weight-bold text-success">
                                            <i class="fal fa-arrow-up mr-1"></i>Alacağım: ₺{{ number_format($customer->current_balance, 2) }}
                                        </h3>
                                    @elseif($customer->current_balance < 0)
                                        <h3 class="font-weight-bold text-danger">
                                            <i class="fal fa-arrow-down mr-1"></i>Borç: ₺{{ number_format(abs($customer->current_balance), 2) }}
                                        </h3>
                                    @else
                                        <h3 class="font-weight-bold text-secondary">
                                            <i class="fal fa-balance-scale mr-1"></i>Dengeli
                                        </h3>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <h6 class="text-muted">Kredi Limiti</h6>
                                    <h3 class="font-weight-bold text-info">
                                        ₺{{ number_format($customer->credit_limit, 2) }}
                                    </h3>
                                </div>
                            </div>
                        </div>

                        @if($debtSummary['total_debt'] > 0)
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="text-muted mb-3">Borç Durumu</h6>
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Toplam Borç</small>
                                                        <strong class="text-dark">₺{{ number_format($debtSummary['total_debt'], 2) }}</strong>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Ödenen</small>
                                                        <strong class="text-muted">₺{{ number_format($debtSummary['total_paid'], 2) }}</strong>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Kalan Borç</small>
                                                        <strong class="{{ $debtSummary['total_remaining'] > 0 ? 'text-dark' : 'text-muted' }}">
                                                            ₺{{ number_format($debtSummary['total_remaining'], 2) }}
                                                        </strong>
                                                    </div>
                                                </div>
                                            </div>
                                            @if($debtSummary['total_remaining'] > 0)
                                                <div class="mt-2 text-center">
                                                    <span class="badge badge-warning mr-1">
                                                        {{ $debtSummary['unpaid_count'] }} Ödenmemiş
                                                    </span>
                                                    @if($debtSummary['partial_count'] > 0)
                                                        <span class="badge badge-secondary">
                                                            {{ $debtSummary['partial_count'] }} Kısmi
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="mt-2 text-center">
                                                    <span class="badge badge-secondary">
                                                        <i class="fal fa-check-circle mr-1"></i>Tüm Borçlar Ödendi
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <h6 class="text-muted">Kullanılabilir Kredi</h6>
                                    <h4 class="font-weight-bold text-primary">
                                        ₺{{ number_format($customer->credit_limit - $customer->current_balance, 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <a href="{{ route('customers.transactions.index', $customer->id) }}" 
                               class="btn btn-primary">
                                <i class="fal fa-list mr-1"></i>Hareketleri Görüntüle
                            </a>
                            <a href="{{ route('customers.transactions.create', $customer->id) }}" 
                               class="btn btn-success">
                                <i class="fal fa-plus mr-1"></i>Yeni Hareket
                            </a>
                            <button class="btn btn-info" onclick="showMizanReport()">
                                <i class="fal fa-list-ol mr-1"></i>Mizan Raporu
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Borç Yönetimi -->
    @if($debtSummary['total_debt'] > 0)
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-debts" class="panel">
                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <div class="alert-icon">
                                    <i class="fal fa-money-bill-wave"></i>
                                </div>
                                <div class="flex-1 ml-2">
                                    <span class="h5">Borç Yönetimi</span>
                                    <br>Müşterinin borçlarını görüntüleyin ve kasa üzerinden ödeme yapın.
                                </div>
                            </div>
                        </div>

                        <!-- Borç Özeti Kartları -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-2">Toplam Borç</h6>
                                        <h4 class="mb-0 text-dark">₺{{ number_format($debtSummary['total_debt'], 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-2">Ödenen</h6>
                                        <h4 class="mb-0 text-muted">₺{{ number_format($debtSummary['total_paid'], 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-2">Kalan Borç</h6>
                                        <h4 class="mb-0 {{ $debtSummary['total_remaining'] > 0 ? 'text-dark' : 'text-muted' }}">
                                            ₺{{ number_format($debtSummary['total_remaining'], 2) }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-2">Durum</h6>
                                        @if($debtSummary['total_remaining'] > 0)
                                            <span class="badge badge-warning">
                                                {{ $debtSummary['unpaid_count'] + $debtSummary['partial_count'] }} Bekleyen
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">Ödendi</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- İşlem Butonları -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        @if($debtSummary['total_remaining'] > 0)
                                            <button type="button" class="btn btn-primary" onclick="openFirstUnpaidDebtModal()">
                                                <i class="fal fa-money-bill-wave mr-1"></i>Borç Ödemesi Yap
                                            </button>
                                        @endif
                                        <a href="{{ route('expenses.create', ['customer_id' => $customer->id]) }}" 
                                           class="btn btn-secondary">
                                            <i class="fal fa-plus-circle mr-1"></i>Yeni Borç Oluştur
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tüm İşlemler (Birleşik Tablo) -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fal fa-list mr-2"></i>Tüm İşlemler
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($allTransactions->count() > 0)
                                    <div class="table-responsive">
                                        <table id="dt-all-transactions" class="table table-bordered table-hover table-striped w-100">
                                            <thead class="bg-highlight">
                                                <tr>
                                                    <th>Tarih</th>
                                                    <th>İşlem Tipi</th>
                                                    <th>Hesap</th>
                                                    <th>Tutar</th>
                                                    <th>Açıklama</th>
                                                    <th>Durum</th>
                                                    <th style="width: 120px;">İşlemler</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($allTransactions as $transaction)
                                                    @php
                                                        $typeLower = strtolower($transaction->type ?? '');
                                                        $transactionType = $transaction->transaction_type ?? '';
                                                        $isIncome = in_array($typeLower, ['gelir', 'income', 'gelir girişi', 'note issued', 'noteissued', 'alınan senet']) || $transactionType === 'income';
                                                        $isExpense = in_array($typeLower, ['gider', 'expense', 'gider çıkışı', 'check issued', 'checkissued', 'check']) || $transactionType === 'expense';
                                                        $isDebt = $typeLower === 'borç' || $transactionType === 'debt';
                                                        $isDebtPayment = $typeLower === 'borç ödemesi' || $typeLower === 'borc odemesi' || $transactionType === 'debt_payment';
                                                        $isAccountTransaction = $transactionType === 'account_transaction' || (empty($transactionType) && !$isDebt && !$isDebtPayment && !$isIncome && !$isExpense);
                                                        
                                                        // Tip gösterimi
                                                        if ($isDebt) {
                                                            $displayType = 'Borç';
                                                            $badgeClass = 'badge-danger';
                                                            $icon = 'fa-exclamation-circle';
                                                        } elseif ($isDebtPayment) {
                                                            $displayType = 'Borç Ödemesi';
                                                            $badgeClass = 'badge-warning';
                                                            $icon = 'fa-money-bill-wave';
                                                        } elseif ($isIncome) {
                                                            $displayType = 'Gelir';
                                                            $badgeClass = 'badge-success';
                                                            $icon = 'fa-arrow-up';
                                                        } elseif ($isExpense) {
                                                            $displayType = 'Gider';
                                                            $badgeClass = 'badge-danger';
                                                            $icon = 'fa-arrow-down';
                                                        } else {
                                                            $displayType = $transaction->type ?? 'İşlem';
                                                            $badgeClass = 'badge-secondary';
                                                            $icon = 'fa-minus';
                                                        }
                                                        
                                                        // Tutar gösterimi
                                                        if ($isDebt) {
                                                            $amountClass = 'text-dark';
                                                            $amountSign = '';
                                                            $amount = $transaction->debt_info->remaining_amount ?? $transaction->amount;
                                                        } elseif ($isDebtPayment) {
                                                            $amountClass = 'text-success';
                                                            $amountSign = '-';
                                                            $amount = $transaction->amount;
                                                        } elseif ($isIncome) {
                                                            $amountClass = 'text-success';
                                                            $amountSign = '+';
                                                            $amount = $transaction->amount;
                                                        } elseif ($isExpense) {
                                                            $amountClass = 'text-danger';
                                                            $amountSign = '-';
                                                            $amount = $transaction->amount;
                                                        } else {
                                                            $amountClass = 'text-muted';
                                                            $amountSign = '±';
                                                            $amount = $transaction->amount;
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <span class="font-weight-bold">{{ \Carbon\Carbon::parse($transaction->date)->format('d.m.Y') }}</span>
                                                                <small class="text-muted">{{ \Carbon\Carbon::parse($transaction->date)->format('H:i') }}</small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $badgeClass }}">
                                                                <i class="fal {{ $icon }} mr-1"></i>
                                                                {{ $displayType }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="fal fa-university text-info mr-2"></i>
                                                                <span>{{ $transaction->account_name ?? '-' }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="font-weight-bold {{ $amountClass }}">
                                                                {{ $amountSign }}₺{{ number_format($amount, 2) }}
                                                            </span>
                                                            @if($isDebt && isset($transaction->debt_info))
                                                                <br>
                                                                <small class="text-muted">
                                                                    Toplam: ₺{{ number_format($transaction->amount, 2) }} | 
                                                                    Ödenen: ₺{{ number_format($transaction->debt_info->paid_amount, 2) }}
                                                                </small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <span>{!! $transaction->description ?? '-' !!}</span>
                                                                @if(isset($transaction->debt_info))
                                                                    <small class="text-muted">
                                                                        <i class="fal fa-file-invoice mr-1"></i>
                                                                        {{ $transaction->debt_info->expense_number }}
                                                                    </small>
                                                                @endif
                                                                @if(isset($transaction->payment_info))
                                                                    <small class="text-muted">
                                                                        <i class="fal fa-receipt mr-1"></i>
                                                                        {{ $transaction->payment_info->payment_number }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($isDebt && isset($transaction->debt_info))
                                                                @if($transaction->debt_info->is_paid)
                                                                    <span class="badge badge-secondary">Ödendi</span>
                                                                @elseif($transaction->debt_info->remaining_amount < $transaction->amount)
                                                                    <span class="badge badge-warning">Kısmi</span>
                                                                @else
                                                                    <span class="badge badge-danger">Ödenmemiş</span>
                                                                @endif
                                                            @else
                                                                <span class="badge badge-secondary">Tamamlandı</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex justify-content-center gap-1">
                                                                @if($isDebt && isset($transaction->debt_info) && !$transaction->debt_info->is_paid)
                                                                    <button type="button" class="btn btn-xs btn-primary" 
                                                                            onclick="openPaymentModal({{ $transaction->reference_id }}, {{ $transaction->debt_info->remaining_amount }}, '{{ $transaction->debt_info->expense_number }}')"
                                                                            title="Borç Ödemesi Yap">
                                                                        <i class="fal fa-money-bill-wave"></i>
                                                                    </button>
                                                                    <a href="{{ route('expenses.edit', $transaction->reference_id) }}" 
                                                                       class="btn btn-xs btn-warning" title="Borç Düzenle">
                                                                        <i class="fal fa-edit"></i>
                                                                    </a>
                                                                    <button type="button" class="btn btn-xs btn-danger" 
                                                                            onclick="deleteDebt({{ $transaction->reference_id }})"
                                                                            title="Borç Sil">
                                                                        <i class="fal fa-trash"></i>
                                                                    </button>
                                                                @elseif($isDebtPayment)
                                                                    <button type="button" class="btn btn-xs btn-danger" 
                                                                            onclick="cancelDebtPayment({{ $transaction->reference_id }})"
                                                                            title="Ödemeyi İptal Et">
                                                                        <i class="fal fa-times"></i>
                                                                    </button>
                                                                @elseif($isAccountTransaction || $isIncome || $isExpense)
                                                                    @if($isAccountTransaction && isset($transaction->id) && substr($transaction->id, 0, 5) !== 'debt_' && substr($transaction->id, 0, 8) !== 'payment_')
                                                                        <a href="{{ route('customers.transactions.edit', [$customer->id, $transaction->id]) }}" 
                                                                           class="btn btn-xs btn-warning" title="Düzenle">
                                                                            <i class="fal fa-edit"></i>
                                                                        </a>
                                                                        <button type="button" class="btn btn-xs btn-danger" 
                                                                                onclick="deleteCustomerTransaction({{ $customer->id }}, {{ $transaction->id }})"
                                                                                title="Sil">
                                                                            <i class="fal fa-trash"></i>
                                                                        </button>
                                                                    @elseif($isIncome && isset($transaction->reference_id))
                                                                        <a href="{{ route('incomes.edit', $transaction->reference_id) }}" 
                                                                           class="btn btn-xs btn-warning" title="Gelir Düzenle">
                                                                            <i class="fal fa-edit"></i>
                                                                        </a>
                                                                        <button type="button" class="btn btn-xs btn-danger" 
                                                                                onclick="deleteIncome({{ $transaction->reference_id }})"
                                                                                title="Gelir Sil">
                                                                            <i class="fal fa-trash"></i>
                                                                        </button>
                                                                    @elseif($isExpense && isset($transaction->reference_id))
                                                                        <a href="{{ route('expenses.edit', $transaction->reference_id) }}" 
                                                                           class="btn btn-xs btn-warning" title="Gider Düzenle">
                                                                            <i class="fal fa-edit"></i>
                                                                        </a>
                                                                        <button type="button" class="btn btn-xs btn-danger" 
                                                                                onclick="deleteExpense({{ $transaction->reference_id }})"
                                                                                title="Gider Sil">
                                                                            <i class="fal fa-trash"></i>
                                                                        </button>
                                                                    @endif
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fal fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Henüz işlem bulunmuyor.</p>
                                        <a href="{{ route('customers.transactions.create', $customer->id) }}" class="btn btn-primary">
                                            <i class="fal fa-plus mr-1"></i>İlk İşlemi Ekle
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ödeme Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fal fa-money-bill-wave mr-2"></i>Borç Ödemesi
                    </h5>
                    <button type="button" class="close text-white" onclick="closePaymentModal()" style="cursor: pointer;">
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
                        <button type="button" class="btn btn-secondary" onclick="closePaymentModal()">İptal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fal fa-check mr-1"></i>Ödemeyi Tamamla
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Mizan Raporu Modal -->
    <div class="modal fade" id="mizanModal" tabindex="-1" role="dialog" aria-labelledby="mizanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mizanModalLabel">
                        <i class="fal fa-list-ol mr-2"></i>Mizan Raporu - {{ $customer->title ?? 'Müşteri' }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mizanStartDate">Başlangıç Tarihi</label>
                                <input type="date" class="form-control" id="mizanStartDate" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mizanEndDate">Bitiş Tarihi</label>
                                <input type="date" class="form-control" id="mizanEndDate" value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fal fa-info-circle mr-2"></i>
                        <strong>Mizan Raporu:</strong> Seçilen tarih aralığındaki hesap bakiyelerini gösterir.
                    </div>

                    <div id="mizanContent">
                        <div class="text-center py-4">
                            <i class="fal fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Mizan raporu yükleniyor...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-primary" onclick="generateMizanReport()">
                        <i class="fal fa-download mr-1"></i>Raporu İndir
                    </button>
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
    // DataTables initialization for transactions table
    if (document.getElementById('dt-transactions')) {
        $('#dt-transactions').DataTable({
            responsive: true,
            stateSave: true,
            pageLength: 25,
            order: [[1, 'desc']], // Sort by date descending
            language: { url: "{{ asset('media/data/tr.json') }}" },
            columnDefs: [
                { orderable: false, targets: [0, 6] }, // Icon and actions columns
                { searchable: false, targets: [0, 6] }
            ]
        });
    }

    // DataTables initialization for all transactions table
    if (document.getElementById('dt-all-transactions')) {
        $('#dt-all-transactions').DataTable({
            responsive: true,
            stateSave: true,
            pageLength: 25,
            order: [[0, 'desc']], // Sort by date descending
            language: { url: "{{ asset('media/data/tr.json') }}" },
            columnDefs: [
                { orderable: false, targets: [6] }, // Actions column
                { searchable: false, targets: [6] }
            ]
        });
    }
});

// Borç ödemesi modalını aç - Pure JavaScript
function openPaymentModal(expenseId, remainingAmount, expenseNumber) {
    console.log('openPaymentModal called with:', { expenseId, remainingAmount, expenseNumber });
    
    // Form alanlarını doldur
    const referenceIdField = document.getElementById('reference_id');
    const expenseNumberField = document.getElementById('expense_number');
    const remainingDebtField = document.getElementById('remaining_debt_display');
    const paidAmountField = document.getElementById('paid_amount');
    
    if (!referenceIdField) {
        console.error('reference_id field not found');
        alert('Ödeme formu yüklenemedi. Sayfayı yenileyin.');
        return;
    }
    
    // Değerleri ayarla
    referenceIdField.value = expenseId || '';
    if (expenseNumberField) expenseNumberField.value = expenseNumber || '';
    if (remainingDebtField) remainingDebtField.value = '₺' + parseFloat(remainingAmount || 0).toFixed(2);
    if (paidAmountField) {
        // Max limit kaldırıldı - sınırsız ödeme yapılabilir
        paidAmountField.removeAttribute('max');
        paidAmountField.value = remainingAmount || 0;
    }
    
    // Modal elementini bul
    const modalElement = document.getElementById('paymentModal');
    if (!modalElement) {
        console.error('Payment modal element not found');
        alert('Ödeme formu bulunamadı. Sayfayı yenileyin.');
        return;
    }
    
    // Pure JavaScript ile modal açma
    // 1. Modal'ı görünür yap
    modalElement.style.display = 'block';
    modalElement.classList.add('show');
    modalElement.setAttribute('aria-hidden', 'false');
    modalElement.setAttribute('aria-modal', 'true');
    
    // 2. Body'ye modal-open class'ı ekle
    document.body.classList.add('modal-open');
    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = '17px'; // Scrollbar için
    
    // 3. Backdrop oluştur
    let backdrop = document.getElementById('paymentModalBackdrop');
    if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.id = 'paymentModalBackdrop';
        backdrop.className = 'modal-backdrop fade show';
        backdrop.style.position = 'fixed';
        backdrop.style.top = '0';
        backdrop.style.left = '0';
        backdrop.style.zIndex = '1040';
        backdrop.style.width = '100vw';
        backdrop.style.height = '100vh';
        backdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        document.body.appendChild(backdrop);
        
        // Backdrop'a tıklandığında modal'ı kapat
        backdrop.addEventListener('click', function() {
            closePaymentModal();
        });
    }
    
    // 4. Modal'ı merkeze al ve görünür yap
    const modalDialog = modalElement.querySelector('.modal-dialog');
    if (modalDialog) {
        modalDialog.style.display = 'block';
        modalDialog.style.margin = '1.75rem auto';
    }
    
    // 5. Close butonlarına event listener ekle
    const closeButtons = modalElement.querySelectorAll('[data-dismiss="modal"], .close, [data-bs-dismiss="modal"]');
    closeButtons.forEach(function(btn) {
        btn.addEventListener('click', closePaymentModal);
    });
    
    console.log('Modal opened successfully');
}

// Modal'ı kapat - Pure JavaScript
function closePaymentModal() {
    const modalElement = document.getElementById('paymentModal');
    if (modalElement) {
        modalElement.style.display = 'none';
        modalElement.classList.remove('show');
        modalElement.setAttribute('aria-hidden', 'true');
        modalElement.setAttribute('aria-modal', 'false');
    }
    
    // Body'den modal-open class'ını kaldır
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    
    // Backdrop'u kaldır
    const backdrop = document.getElementById('paymentModalBackdrop');
    if (backdrop) {
        backdrop.remove();
    }
}

// İlk ödenmemiş borcu bul ve modal aç
function openFirstUnpaidDebtModal() {
    @if(isset($firstUnpaidDebt) && $firstUnpaidDebt)
        openPaymentModal(
            {{ $firstUnpaidDebt->id }}, 
            {{ $firstUnpaidDebt->remaining_amount }}, 
            '{{ $firstUnpaidDebt->expense_number }}'
        );
    @else
        // Tablodaki tüm borç satırlarını bul
        const debtRows = document.querySelectorAll('#dt-all-transactions tbody tr');
        
        for (let i = 0; i < debtRows.length; i++) {
            const row = debtRows[i];
            const payButton = row.querySelector('button[onclick*="openPaymentModal"]');
            
            if (payButton) {
                // Butonun onclick attribute'unu al
                const onclickAttr = payButton.getAttribute('onclick');
                if (onclickAttr) {
                    // openPaymentModal fonksiyonunu çalıştır
                    eval(onclickAttr);
                    // İlk ödenmemiş borcu bulduk, modal açıldı
                    return;
                }
            }
        }
        
        // Eğer tabloda bulunamazsa, scroll yap
        const panelDebts = document.getElementById('panel-debts');
        if (panelDebts) {
            panelDebts.scrollIntoView({ behavior: 'smooth' });
            alert('Lütfen tablodan ödenecek borcu seçin.');
        } else {
            alert('Ödenmemiş borç bulunamadı.');
        }
    @endif
}

// Müşteri hesap hareketini sil
function deleteCustomerTransaction(customerId, transactionId) {
    if (!confirm('Bu hareketi silmek istediğinizden emin misiniz?')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/customers/${customerId}/transactions/${transactionId}`;
    form.innerHTML = `
        @csrf
        <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
}

// Borç ödemesini iptal et
function cancelDebtPayment(paymentId) {
    if (!confirm('Bu borç ödemesini iptal etmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/customer-debts/cancel-payment/${paymentId}`;
    form.innerHTML = `
        @csrf
    `;
    document.body.appendChild(form);
    form.submit();
}

// Borç (expense) sil
function deleteDebt(expenseId) {
    if (!confirm('Bu borç kaydını silmek istediğinizden emin misiniz? İlişkili tüm ödemeler de silinecektir.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/expenses/${expenseId}`;
    form.innerHTML = `
        @csrf
        <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
}

// Gelir sil
function deleteIncome(incomeId) {
    if (!confirm('Bu gelir kaydını silmek istediğinizden emin misiniz?')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/incomes/${incomeId}`;
    form.innerHTML = `
        @csrf
        <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
}

// Gider sil
function deleteExpense(expenseId) {
    if (!confirm('Bu gider kaydını silmek istediğinizden emin misiniz?')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/expenses/${expenseId}`;
    form.innerHTML = `
        @csrf
        <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
}

// Mizan raporu göster
function showMizanReport() {
    $('#mizanModal').modal('show');
    loadMizanReport();
}

// Mizan raporu yükle
function loadMizanReport() {
    const startDate = document.getElementById('mizanStartDate').value;
    const endDate = document.getElementById('mizanEndDate').value;
    
    // AJAX ile mizan verilerini getir
    fetch(`/api/customers/{{ $customer->id }}/mizan?start_date=${startDate}&end_date=${endDate}`)
        .then(response => response.json())
        .then(data => {
            displayMizanReport(data);
        })
        .catch(error => {
            console.error('Mizan raporu yüklenirken hata:', error);
            document.getElementById('mizanContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fal fa-exclamation-triangle mr-2"></i>
                    Mizan raporu yüklenirken bir hata oluştu.
                </div>
            `;
        });
}

// Mizan raporu göster
function displayMizanReport(data) {
    const content = `
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Hesap Kodu</th>
                        <th>Hesap Adı</th>
                        <th>Borç Bakiyesi</th>
                        <th>Alacak Bakiyesi</th>
                        <th>Net Bakiye</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.accounts.map(account => `
                        <tr>
                            <td><strong>${account.code}</strong></td>
                            <td>${account.name}</td>
                            <td class="text-right">
                                <span class="text-danger">₺${parseFloat(account.debit_balance).toFixed(2)}</span>
                            </td>
                            <td class="text-right">
                                <span class="text-success">₺${parseFloat(account.credit_balance).toFixed(2)}</span>
                            </td>
                            <td class="text-right">
                                <span class="font-weight-bold ${account.net_balance >= 0 ? 'text-success' : 'text-danger'}">
                                    ₺${parseFloat(account.net_balance).toFixed(2)}
                                </span>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <th colspan="2">TOPLAM</th>
                        <th class="text-right text-danger">₺${parseFloat(data.totals.debit).toFixed(2)}</th>
                        <th class="text-right text-success">₺${parseFloat(data.totals.credit).toFixed(2)}</th>
                        <th class="text-right font-weight-bold">₺${parseFloat(data.totals.net).toFixed(2)}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Toplam Borç</h6>
                        <h4 class="text-danger">₺${parseFloat(data.totals.debit).toFixed(2)}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Toplam Alacak</h6>
                        <h4 class="text-success">₺${parseFloat(data.totals.credit).toFixed(2)}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Net Bakiye</h6>
                        <h4 class="text-primary">₺${parseFloat(data.totals.net).toFixed(2)}</h4>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('mizanContent').innerHTML = content;
}

// Mizan raporu indir
function generateMizanReport() {
    const startDate = document.getElementById('mizanStartDate').value;
    const endDate = document.getElementById('mizanEndDate').value;
    
    // PDF olarak indir
    window.open(`/api/customers/{{ $customer->id }}/mizan/pdf?start_date=${startDate}&end_date=${endDate}`, '_blank');
}

// Tarih değiştiğinde raporu yenile
document.getElementById('mizanStartDate').addEventListener('change', loadMizanReport);
document.getElementById('mizanEndDate').addEventListener('change', loadMizanReport);
</script>
