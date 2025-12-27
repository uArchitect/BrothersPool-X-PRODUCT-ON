@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">

                <div class="panel-container show">
                    <div class="panel-content">
                        <form method="POST" action="{{ route('customers.update', $customer->id) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <!-- Sol Kolon -->
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">Temel Bilgiler</h5>
                                    
                                    <div class="form-group">
                                        <label for="code">Kod</label>
                                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                               id="code" name="code" value="{{ old('code', $customer->code) }}" 
                                               placeholder="Müşteri kodu">
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="title">Ünvan</label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ old('title', $customer->title) }}" 
                                               placeholder="Şirket ünvanı">
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="account_type">Hesap Türü</label>
                                        <select class="form-control @error('account_type') is-invalid @enderror" 
                                                id="account_type" name="account_type">
                                            <option value="">Seçiniz</option>
                                            <option value="Müşteri" {{ old('account_type', $customer->account_type) == 'Müşteri' ? 'selected' : '' }}>Müşteri</option>
                                            <option value="Tedarikçi" {{ old('account_type', $customer->account_type) == 'Tedarikçi' ? 'selected' : '' }}>Tedarikçi</option>
                                            <option value="Ortak" {{ old('account_type', $customer->account_type) == 'Ortak' ? 'selected' : '' }}>Ortak</option>
                                            <option value="Diğer" {{ old('account_type', $customer->account_type) == 'Diğer' ? 'selected' : '' }}>Diğer</option>
                                        </select>
                                        @error('account_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">Telefon</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" 
                                               placeholder="Telefon numarası">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $customer->email) }}" 
                                               placeholder="Email adresi">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Sağ Kolon -->
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">Vergi Bilgileri</h5>
                                    
                                    <div class="form-group">
                                        <label for="tax_office">Vergi Dairesi</label>
                                        <input type="text" class="form-control @error('tax_office') is-invalid @enderror" 
                                               id="tax_office" name="tax_office" value="{{ old('tax_office', $customer->tax_office) }}" 
                                               placeholder="Vergi dairesi">
                                        @error('tax_office')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="tax_number">Vergi Numarası</label>
                                        <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                                               id="tax_number" name="tax_number" value="{{ old('tax_number', $customer->tax_number) }}" 
                                               placeholder="Vergi numarası">
                                        @error('tax_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="authorized_person">Yetkili Kişi</label>
                                        <input type="text" class="form-control @error('authorized_person') is-invalid @enderror" 
                                               id="authorized_person" name="authorized_person" value="{{ old('authorized_person', $customer->authorized_person) }}" 
                                               placeholder="Yetkili kişi adı">
                                        @error('authorized_person')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <h5 class="text-primary mb-3 mt-4">Mali Bilgiler</h5>

                                    <div class="form-group">
                                        <label for="credit_limit">Kredi Limiti</label>
                                        <input type="number" class="form-control @error('credit_limit') is-invalid @enderror" 
                                               id="credit_limit" name="credit_limit" value="{{ old('credit_limit', $customer->credit_limit) }}" 
                                               step="0.01" min="0" placeholder="0.00">
                                        @error('credit_limit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="current_balance">Mevcut Bakiye</label>
                                        <input type="number" class="form-control @error('current_balance') is-invalid @enderror" 
                                               id="current_balance" name="current_balance" value="{{ old('current_balance', $customer->current_balance) }}" 
                                               step="0.01" placeholder="0.00">
                                        <small class="form-text text-muted">
                                            <i class="fal fa-info-circle mr-1"></i>
                                            Bu değeri değiştirmek dikkatli olunmalıdır. Hareketler üzerinden bakiye güncellenmesi önerilir.
                                        </small>
                                        @error('current_balance')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Adres -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">Adres Bilgileri</h5>
                                    <div class="form-group">
                                        <label for="address">Adres</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" name="address" rows="3" 
                                                  placeholder="Tam adres bilgisi">{{ old('address', $customer->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Butonlar -->
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-secondary mr-2">
                                            <i class="fal fa-times mr-1"></i>İptal
                                        </a>
                                        <button type="submit" class="btn btn-primary">
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
    </div>
</main>

@include('layouts.footer')
