@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">

    <div class="row">
        <div class="col-xl-8">
            <div id="panel-1" class="panel">

                <div class="panel-container show">
                    <div class="panel-content">
                        <form method="POST" action="{{ route('customers.transactions.update', [$customer->id, $transaction->id]) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date">Tarih <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                               id="date" name="date" value="{{ old('date', $transaction->date) }}" required>
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="account">Hesap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('account') is-invalid @enderror" 
                                               id="account" name="account" value="{{ old('account', $transaction->account) }}" 
                                               placeholder="Kasa, Banka, vb." required>
                                        @error('account')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type">Tip <span class="text-danger">*</span></label>
                                        <select class="form-control @error('type') is-invalid @enderror" 
                                                id="type" name="type" required>
                                            <option value="">Seçiniz</option>
                                            <option value="Gelir" {{ old('type', $transaction->type) == 'Gelir' ? 'selected' : '' }}>Gelir</option>
                                            <option value="Gider" {{ old('type', $transaction->type) == 'Gider' ? 'selected' : '' }}>Gider</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount">Tutar <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                               id="amount" name="amount" value="{{ old('amount', $transaction->amount) }}" 
                                               step="0.01" min="0.01" placeholder="0.00" required>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="description">Açıklama</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="3" 
                                                  placeholder="Hareket açıklaması">{{ old('description', $transaction->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Uyarı -->
                            <div class="alert alert-warning">
                                <i class="fal fa-exclamation-triangle mr-2"></i>
                                <strong>Dikkat:</strong> Bu hareketi düzenlemek müşterinin bakiyesini etkileyecektir. 
                                Eski hareketin etkisi geri alınacak ve yeni değerler uygulanacaktır.
                            </div>

                            <!-- Butonlar -->
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('customers.transactions.index', $customer->id) }}" class="btn btn-secondary mr-2">
                                            <i class="fal fa-times mr-1"></i>İptal
                                        </a>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fal fa-save mr-1"></i>Güncelle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Müşteri Bilgileri -->
        <div class="col-xl-4">
            <div id="panel-2" class="panel">

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="mb-3">
                            <h6 class="text-muted">Müşteri</h6>
                            <h5 class="font-weight-bold">{{ $customer->title ?? $customer->code ?? 'Müşteri' }}</h5>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">Mevcut Bakiye</h6>
                            <h4 class="font-weight-bold {{ $customer->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                ₺{{ number_format($customer->current_balance, 2) }}
                            </h4>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">Kredi Limiti</h6>
                            <h5 class="font-weight-bold text-info">
                                ₺{{ number_format($customer->credit_limit, 2) }}
                            </h5>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">Kullanılabilir Kredi</h6>
                            <h5 class="font-weight-bold text-primary">
                                ₺{{ number_format($customer->credit_limit - $customer->current_balance, 2) }}
                            </h5>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-info">
                                <i class="fal fa-user mr-1"></i>Müşteri Detayı
                            </a>
                            <a href="{{ route('customers.transactions.index', $customer->id) }}" class="btn btn-primary">
                                <i class="fal fa-list mr-1"></i>Tüm Hareketler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')
