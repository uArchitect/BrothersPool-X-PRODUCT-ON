@foreach($customers as $customer)
<div class="modal fade" id="editCustomerModal{{ $customer->id }}" tabindex="-1" role="dialog" aria-labelledby="editCustomerModalLabel{{ $customer->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editCustomerModalLabel{{ $customer->id }}">
                    <i class="fal fa-user-edit mr-2"></i>{{ $customer->first_name }} {{ $customer->last_name }} - Müşteri Düzenle
                </h5>
                <button type="button" class="btn text-white" data-bs-dismiss="modal" aria-label="Kapat">
                    <i class="fal fa-times fa-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="editCustomerForm{{ $customer->id }}" action="{{ route('customers.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $customer->id }}">

                    <div class="row g-3">
                        <!-- Temel Bilgiler -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fal fa-user-circle me-2"></i>Temel Bilgiler</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Ad <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="first_name" value="{{ $customer->first_name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Soyad <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="last_name" value="{{ $customer->last_name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Cinsiyet</label>
                                        <select class="custom-select" name="gender">
                                            <option value="">Seçiniz</option>
                                            <option value="male" {{ $customer->gender == 'male' ? 'selected' : '' }}>Erkek</option>
                                            <option value="female" {{ $customer->gender == 'female' ? 'selected' : '' }}>Kadın</option>
                                            <option value="other" {{ $customer->gender == 'other' ? 'selected' : '' }}>Diğer</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Doğum Tarihi</label>
                                        <input type="date" class="form-control" name="date_of_birth" value="{{ $customer->date_of_birth }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">TC Kimlik No</label>
                                        <input type="text" class="form-control" name="identity" value="{{ $customer->identity }}" maxlength="11">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Vergi No</label>
                                        <input type="text" class="form-control" name="tax_number" value="{{ $customer->tax_number }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- İletişim Bilgileri -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fal fa-phone me-2"></i>İletişim Bilgileri</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Telefon <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" name="phone" value="{{ $customer->phone }}" required maxlength="20">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">E-posta</label>
                                        <input type="email" class="form-control" name="email" value="{{ $customer->email }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Adres Satırı 1</label>
                                        <textarea class="form-control" name="address_line1" rows="2">{{ $customer->address_line1 }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Adres Satırı 2</label>
                                        <textarea class="form-control" name="address_line2" rows="2">{{ $customer->address_line2 }}</textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Şehir</label>
                                            <input type="text" class="form-control" name="city" value="{{ $customer->city }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">İlçe/Eyalet</label>
                                            <input type="text" class="form-control" name="state" value="{{ $customer->state }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Posta Kodu</label>
                                            <input type="text" class="form-control" name="postal_code" value="{{ $customer->postal_code }}" maxlength="10">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Ülke</label>
                                            <input type="text" class="form-control" name="country" value="{{ $customer->country }}" placeholder="Türkiye">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ek Bilgiler -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fal fa-info-circle me-2"></i>Ek Bilgiler</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">VIP Durumu</label>
                                                <select class="custom-select" name="is_vip">
                                                    <option value="0" {{ $customer->is_vip == 0 ? 'selected' : '' }}>Hayır</option>
                                                    <option value="1" {{ $customer->is_vip == 1 ? 'selected' : '' }}>Evet</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Para Puan</label>
                                                <input type="number" class="form-control" name="parapuan" value="{{ $customer->parapuan }}" min="0">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Toplam Ziyaret</label>
                                                <input type="number" class="form-control" value="{{ $customer->total_visits }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Toplam Harcama</label>
                                                <input type="text" class="form-control" value="{{ number_format($customer->total_spent, 2, ',', '.') }} ₺" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Son Ziyaret</label>
                                                <input type="text" class="form-control" value="{{ $customer->last_visit ? date('d.m.Y', strtotime($customer->last_visit)) : '-' }}" readonly>
                                            </div>
                                            <!-- Alerji Bilgileri -->
                                            <div class="mb-3">
                                                <label class="form-label">Alerji Durumu</label>
                                                <select class="custom-select" name="allergy">
                                                    <option value="0" {{ $customer->allergy == 0 ? 'selected' : '' }}>Yok</option>
                                                    <option value="1" {{ $customer->allergy == 1 ? 'selected' : '' }}>Var</option>
                                                </select>
                                            </div>
                                            <div class="mb-3" id="allergyNoteContainer{{ $customer->id }}" style="display: {{ $customer->allergy == 1 ? 'block' : 'none' }};">
                                                <label class="form-label">Alerji Notu</label>
                                                <textarea class="form-control" name="allergy_note" rows="3" placeholder="Varsa alerji detaylarını buraya yazın...">{{ $customer->allergy_note }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Notlar</label>
                                                <textarea class="form-control" name="notes" rows="12">{{ $customer->notes }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer içinde -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fal fa-times"></i> Kapat
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fal fa-save"></i> Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
/* Sadece edit customer modal'ları için CSS */
[id^="editCustomerModal"] .modal-dialog {
    max-width: 1000px;
}

[id^="editCustomerModal"] .modal-content {
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

[id^="editCustomerModal"] .modal-header {
    border-radius: 0.5rem 0.5rem 0 0;
    padding: 1rem 1.5rem;
}

[id^="editCustomerModal"] .modal-body {
    padding: 1.5rem;
}

[id^="editCustomerModal"] .card {
    border: 1px solid rgba(0,0,0,.125);
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

[id^="editCustomerModal"] .card-header {
    background: linear-gradient(to right, #f8f9fa, #ffffff);
    border-bottom: 1px solid rgba(0,0,0,.125);
    padding: 0.75rem 1rem;
}

[id^="editCustomerModal"] .form-control, 
[id^="editCustomerModal"] .custom-select {
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    border: 1px solid #ced4da;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

[id^="editCustomerModal"] .form-control:focus, 
[id^="editCustomerModal"] .custom-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

[id^="editCustomerModal"] .btn {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-weight: 500;
    transition: all 0.2s;
}

[id^="editCustomerModal"] .btn:hover {
    transform: translateY(-1px);
}

[id^="editCustomerModal"] .btn-close-white {
    filter: brightness(0) invert(1);
}

/* SweetAlert Özelleştirmeleri - sadece edit modal'dan açılanlar için */
[id^="editCustomerModal"] ~ .swal2-container .swal2-popup {
    font-size: 1rem !important;
    font-family: inherit !important;
}

[id^="editCustomerModal"] ~ .swal2-container {
    z-index: 9999 !important;
}

[id^="editCustomerModal"] ~ .swal2-container .swal2-styled.swal2-confirm {
    background-color: #0d6efd !important;
}

[id^="editCustomerModal"] ~ .swal2-container .swal2-styled.swal2-cancel {
    background-color: #6c757d !important;
}

/* Form validation - sadece edit modal'ında */
[id^="editCustomerModal"] .form-control.is-invalid, 
[id^="editCustomerModal"] .custom-select.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

[id^="editCustomerModal"] .invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // SweetAlert varsayılan ayarları
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
});

// jQuery yüklenmesini bekle ve kontrol et
function waitForjQuery(callback) {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        callback();
    } else {
        setTimeout(() => waitForjQuery(callback), 100);
    }
}

// jQuery hazır olduğunda çalıştır
waitForjQuery(function() {
    $(document).ready(function() {
        // Her müşteri için alerji durumu kontrolü
        @foreach($customers as $customer)
            $(`#editCustomerModal{{ $customer->id }} select[name="allergy"]`).on('change', function() {
                if ($(this).val() === '1') {
                    $(`#allergyNoteContainer{{ $customer->id }}`).slideDown();
                } else {
                    $(`#allergyNoteContainer{{ $customer->id }}`).slideUp();
                    $(`#allergyNoteContainer{{ $customer->id }} textarea`).val('');
                }
            });
        @endforeach
    });
});
</script>
