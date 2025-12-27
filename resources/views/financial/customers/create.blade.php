@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">

                <div class="panel-container show">
                    <div class="panel-content">
                        <form method="POST" action="{{ route('customers.store') }}">
                            @csrf
                            
                            <div class="row">
                                <!-- Sol Kolon -->
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">Temel Bilgiler</h5>
                                    
                                    <div class="form-group">
                                        <label for="code">Kod <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" 
                                               id="code" name="code" 
                                               value="{{ old('code', 'POOL-' . strtoupper(Str::random(8))) }}"
                                               placeholder="Müşteri kodu" readonly>
                                        <small class="form-text text-muted">
                                            Kod otomatik ve benzersiz olarak atanır. Dilerseniz kayıt sonrası değiştirebilirsiniz.
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label for="title">Ünvan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" 
                                               id="title" name="title" value="{{ old('title') }}" 
                                               placeholder="Şirket ünvanı">
                                    </div>

                                    <div class="form-group">
                                        <label for="account_type">Hesap Türü <span class="text-danger">*</span></label>
                                        <select class="form-control" 
                                                id="account_type" name="account_type">
                                            <option value="">Seçiniz</option>
                                            <option value="Müşteri" {{ old('account_type') == 'Müşteri' ? 'selected' : '' }}>Müşteri</option>
                                            <option value="Tedarikçi" {{ old('account_type') == 'Tedarikçi' ? 'selected' : '' }}>Tedarikçi</option>
                                            <option value="Ortak" {{ old('account_type') == 'Ortak' ? 'selected' : '' }}>Ortak</option>
                                            <option value="Diğer" {{ old('account_type') == 'Diğer' ? 'selected' : '' }}>Diğer</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">Telefon</label>
                                        <input type="text" class="form-control" 
                                               id="phone" name="phone" value="{{ old('phone') }}" 
                                               placeholder="Telefon numarası">
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" 
                                               id="email" name="email" value="{{ old('email') }}" 
                                               placeholder="Email adresi">
                                        <small class="form-text text-muted">
                                            Email otomatik olarak ünvan'a göre oluşturulur, dilerseniz değiştirebilirsiniz.
                                        </small>
                                    </div>
                                </div>

                                <!-- Sağ Kolon -->
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">Vergi Bilgileri</h5>
                                    
                                    <div class="form-group">
                                        <label for="tax_office">Vergi Dairesi</label>
                                        <input type="text" class="form-control" 
                                               id="tax_office" name="tax_office" value="{{ old('tax_office') }}" 
                                               placeholder="Vergi dairesi">
                                    </div>

                                    <div class="form-group">
                                        <label for="tax_number">Vergi Numarası</label>
                                        <input type="text" class="form-control" 
                                               id="tax_number" name="tax_number" value="{{ old('tax_number') }}" 
                                               placeholder="Vergi numarası">
                                    </div>

                                    <div class="form-group">
                                        <label for="authorized_person">Yetkili Kişi</label>
                                        <input type="text" class="form-control" 
                                               id="authorized_person" name="authorized_person" value="{{ old('authorized_person') }}" 
                                               placeholder="Yetkili kişi adı">
                                    </div>

                                    <h5 class="text-primary mb-3 mt-4">Mali Bilgiler</h5>

                                    <div class="form-group">
                                        <label for="credit_limit">Kredi Limiti</label>
                                        <input type="number" class="form-control" 
                                               id="credit_limit" name="credit_limit" value="{{ old('credit_limit', 0) }}" 
                                               step="0.01" min="0" placeholder="0.00">
                                    </div>

                                    <div class="form-group">
                                        <label for="current_balance">Mevcut Bakiye</label>
                                        <input type="number" class="form-control" 
                                               id="current_balance" name="current_balance" value="{{ old('current_balance', 0) }}" 
                                               step="0.01" placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <!-- Adres -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">Adres Bilgileri</h5>
                                    <div class="form-group">
                                        <label for="address">Adres</label>
                                        <textarea class="form-control" 
                                                  id="address" name="address" rows="3" 
                                                  placeholder="Tam adres bilgisi">{{ old('address') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Butonlar -->
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('customers.index') }}" class="btn btn-secondary mr-2">
                                            <i class="fal fa-times mr-1"></i>İptal
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fal fa-save mr-1"></i>Kaydet
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

@push('scripts')
<script>
    // Sayfa yüklendiğinde kod inputunu sadece ilk yüklemede otomatik olarak doldur.
    document.addEventListener('DOMContentLoaded', function () {
        var codeInput = document.getElementById('code');
        if(codeInput && !codeInput.value) {
            function generatePoolCode() {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let result = '';
                for (let i = 0; i < 8; i++) {
                    result += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                return 'POOL-' + result;
            }
            codeInput.value = generatePoolCode();
        }

        // Email'i otomatik olarak, ünvan'dan (title) türet.
        var titleInput = document.getElementById('title');
        var emailInput = document.getElementById('email');
        var emailInitialized = false;

        function generateEmailFromTitle(title) {
            if (!title) return '';
            // Türkçe karakterleri ve özel karakterleri rahat bir email olarak dönüştür
            let emailPart = title.toLowerCase()
                .replace(/ç/g, 'c')
                .replace(/ğ/g, 'g')
                .replace(/ı/g, 'i')
                .replace(/ö/g, 'o')
                .replace(/ş/g, 's')
                .replace(/ü/g, 'u')
                .replace(/[^a-z0-9]/g, '.')
                .replace(/\.{2,}/g, '.') // çoklu noktayı teke çevir
                .replace(/^\.+|\.+$/g, ''); // baş-son nokta sil
            
            return emailPart + '@brothersorganizasyon.com';
        }

        // Eğer eski bir değer yoksa ve "email" boşsa ilk başta oluştur
        if (titleInput && emailInput && !emailInput.value) {
            titleInput.addEventListener('input', function() {
                // Eğer kullanıcı emaili elle değiştirmediyse, emaili güncelle.
                if (!emailInitialized || emailInput.value === "" || emailInput.dataset.autoGenerated === "true") {
                    let mail = generateEmailFromTitle(titleInput.value);
                    emailInput.value = mail;
                    emailInput.dataset.autoGenerated = "true";
                }
            });
            // Email input'a dokunduğunda kilidi kaldır
            emailInput.addEventListener('input', function(){
                emailInitialized = true;
                emailInput.dataset.autoGenerated = "false";
            });

            // Sayfa yüklenirken varsa ünvan'dan otomatik üret (ama eski value boşsa)
            if (titleInput.value && !emailInput.value) {
                let mail = generateEmailFromTitle(titleInput.value);
                emailInput.value = mail;
                emailInput.dataset.autoGenerated = "true";
            }
        }
    });
</script>
@endpush
