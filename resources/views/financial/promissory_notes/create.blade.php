@include('layouts.header')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="panel-container">
                    <div class="panel-content">
                        <form action="{{ route('promissory_notes.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="note_number" >Senet Numarası</label>
                                        <input type="text" class="form-control @error('note_number') is-invalid @enderror" 
                                               id="note_number" name="note_number" value="{{ old('note_number') }}" 
                                               placeholder="Otomatik oluşturulacak" onblur="generateNoteNumber()">
                                        @error('note_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Boş bırakılırsa otomatik numara oluşturulur</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="transaction_type">İşlem Türü <span class="text-danger">*</span></label>
                                        <select class="form-control @error('transaction_type') is-invalid @enderror" 
                                                id="transaction_type" name="transaction_type" required>
                                            <option value="">İşlem türü seçiniz</option>
                                            @foreach($transactionTypes as $key => $label)
                                                <option value="{{ $key }}" {{ old('transaction_type') == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('transaction_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_id" >Müşteri <span class="text-danger">*</span></label>
                                        <select class="form-control @error('customer_id') is-invalid @enderror" 
                                                id="customer_id" name="customer_id" required>
                                            <option value="">Müşteri seçiniz</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->title ?? $customer->code ?? 'Müşteri' }}
                                                    @if($customer->current_balance > 0)
                                                        (Alacağım: ₺{{ number_format($customer->current_balance, 2) }})
                                                    @elseif($customer->current_balance < 0)
                                                        (Borç: ₺{{ number_format(abs($customer->current_balance), 2) }})
                                                    @else
                                                        (Dengeli)
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('customer_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="account_id">Hesap</label>
                                        <select class="form-control @error('account_id') is-invalid @enderror" 
                                                id="account_id" name="account_id">
                                            <option value="">Hesap seçiniz (Opsiyonel)</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                                    {{ $account->name }} (Bakiye: ₺{{ number_format($account->balance, 2) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('account_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount" >Tutar <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₺</span>
                                            </div>
                                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                                   id="amount" name="amount" value="{{ old('amount') }}" 
                                                   step="0.01" min="0.01" required>
                                        </div>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="issue_date" >Keşide Tarihi <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('issue_date') is-invalid @enderror" 
                                               id="issue_date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                                        @error('issue_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="maturity_date" >Vade Tarihi <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('maturity_date') is-invalid @enderror" 
                                               id="maturity_date" name="maturity_date" value="{{ old('maturity_date') }}" required>
                                        @error('maturity_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Durum <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                            <option value="">Durum seçiniz</option>
                                            <option value="AKTIF" {{ old('status') == 'AKTIF' ? 'selected' : '' }}>Aktif</option>
                                            <option value="ODENDI" {{ old('status') == 'ODENDI' ? 'selected' : '' }}>Ödendi</option>
                                            <option value="VADESI_GECTI" {{ old('status') == 'VADESI_GECTI' ? 'selected' : '' }}>Vadesi Geçti</option>
                                            <option value="DEVREDILDI" {{ old('status') == 'DEVREDILDI' ? 'selected' : '' }}>Devredildi</option>
                                            <option value="IPTAL_EDILDI" {{ old('status') == 'IPTAL_EDILDI' ? 'selected' : '' }}>İptal Edildi</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="interest_rate">Faiz Oranı (%)</label>
                                        <input type="number" class="form-control @error('interest_rate') is-invalid @enderror" 
                                               id="interest_rate" name="interest_rate" value="{{ old('interest_rate', 0) }}" 
                                               step="0.01" min="0" max="100">
                                        @error('interest_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="penalty_rate">Ceza Oranı (%)</label>
                                        <input type="number" class="form-control @error('penalty_rate') is-invalid @enderror" 
                                               id="penalty_rate" name="penalty_rate" value="{{ old('penalty_rate', 0) }}" 
                                               step="0.01" min="0" max="100">
                                        @error('penalty_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="description" >Açıklama</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="3" 
                                                  placeholder="Senet hakkında ek bilgiler...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('promissory_notes.index') }}" class="btn btn-secondary">
                                            <i class="fal fa-arrow-left mr-1"></i>Geri Dön
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fal fa-save mr-1"></i>Senet Kaydet
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Set maturity date to 30 days from issue date by default
document.getElementById('issue_date').addEventListener('change', function() {
    const issueDate = new Date(this.value);
    const maturityDate = new Date(issueDate);
    maturityDate.setDate(maturityDate.getDate() + 30);
    
    const maturityDateInput = document.getElementById('maturity_date');
    if (!maturityDateInput.value) {
        maturityDateInput.value = maturityDate.toISOString().split('T')[0];
    }
});

// Generate note number if field is empty
function generateNoteNumber() {
    const noteNumberField = document.getElementById('note_number');
    if (!noteNumberField.value.trim()) {
        // Generate a simple note number based on current timestamp
        const now = new Date();
        const timestamp = now.getTime().toString().slice(-8); // Last 8 digits of timestamp
        const noteNumber = 'SNT-' + timestamp;
        noteNumberField.value = noteNumber;
    }
}
</script>
@include('layouts.footer')
