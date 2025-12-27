@include('layouts.header')

<style>
.wage-field {
    display: none;
}

.required-field::after {
    content: " *";
    color: #dc3545;
}

.nav-tabs .nav-link {
    color: #495057;
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    isolation: isolate;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs .nav-link i {
    margin-right: 0.5rem;
}

.tab-content {
    padding: 20px;
    background: #fff;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.25rem 0.25rem;
}
</style>

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-container show">
                    <div class="panel-content">
                        
                        <!-- Başlık -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="text-primary mb-1"><i class="fal fa-user-edit mr-2"></i>Personel Düzenle</h4>
                                <p class="text-muted mb-0">Personel bilgilerini tab'lar üzerinden düzenleyin</p>
                            </div>
                            <div>
                                <a href="{{ route('employees') }}" class="btn btn-sm btn-secondary">
                                    <i class="fal fa-arrow-left mr-1"></i> Geri Dön
                                </a>
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fal fa-exclamation-circle mr-2"></i>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        <!-- Form -->
                        <form action="{{ route('employees.update') }}" method="POST" enctype="multipart/form-data" id="employeeForm">
                            @csrf
                            <input type="hidden" name="id" value="{{ $employee->id }}">
                            
                            <!-- Tab Navigation -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab-basic" role="tab">
                                        <i class="fal fa-user"></i>
                                        <span class="hidden-sm-down ml-1">Temel Bilgiler</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-identity" role="tab">
                                        <i class="fal fa-id-card"></i>
                                        <span class="hidden-sm-down ml-1">Kimlik Bilgileri</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-position" role="tab">
                                        <i class="fal fa-briefcase"></i>
                                        <span class="hidden-sm-down ml-1">Grup ve Görev</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-payment" role="tab">
                                        <i class="fal fa-money-bill-wave"></i>
                                        <span class="hidden-sm-down ml-1">Ödeme Bilgileri</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-bank" role="tab">
                                        <i class="fal fa-university"></i>
                                        <span class="hidden-sm-down ml-1">Banka Bilgileri</span>
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                
                                <!-- Tab 1: Temel Bilgiler -->
                                <div class="tab-pane fade show active" id="tab-basic" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name" class="form-label required-field">Ad Soyad</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-user"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                           id="name" name="name" value="{{ old('name', $employee->name) }}" 
                                                           placeholder="Ad Soyad" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone" class="form-label required-field">Telefon</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-phone"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                           id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" 
                                                           placeholder="5XXXXXXXXX" maxlength="11" required>
                                                    @error('phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="hire_date" class="form-label required-field">İşe Giriş Tarihi</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                                           id="hire_date" name="hire_date" 
                                                           value="{{ old('hire_date', $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('Y-m-d') : date('Y-m-d')) }}" 
                                                           max="{{ date('Y-m-d') }}" required>
                                                    @error('hire_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="address" class="form-label">Adres</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-map-marker-alt"></i>
                                                        </span>
                                                    </div>
                                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                                              id="address" name="address" rows="3" 
                                                              placeholder="Adres bilgisi">{{ old('address', $employee->address) }}</textarea>
                                                    @error('address')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 2: Kimlik Bilgileri -->
                                <div class="tab-pane fade" id="tab-identity" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tc_no" class="form-label required-field">TC Kimlik No</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-id-card"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control @error('tc_no') is-invalid @enderror" 
                                                           id="tc_no" name="tc_no" value="{{ old('tc_no', $employee->tc_no) }}" 
                                                           placeholder="11 haneli TC kimlik numarası" maxlength="11" required>
                                                    @error('tc_no')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="sgk_no" class="form-label required-field">SGK No</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-shield-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control @error('sgk_no') is-invalid @enderror" 
                                                           id="sgk_no" name="sgk_no" value="{{ old('sgk_no', $employee->sgk_no) }}" 
                                                           placeholder="SGK numarası" required>
                                                    @error('sgk_no')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 3: Grup ve Görev -->
                                <div class="tab-pane fade" id="tab-position" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="group_id" class="form-label required-field">Grup (Departman)</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-users"></i>
                                                        </span>
                                                    </div>
                                                    <select class="form-control @error('group_id') is-invalid @enderror" 
                                                            id="group_id" name="group_id" required>
                                                        <option value="">Grup Seçin</option>
                                                        @foreach($employee_groups as $group)
                                                            <option value="{{ $group->id }}" 
                                                                {{ old('group_id', $employee->group_id) == $group->id ? 'selected' : '' }}>
                                                                {{ $group->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('group_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="position_id" class="form-label required-field">Görev (Pozisyon)</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-user-tag"></i>
                                                        </span>
                                                    </div>
                                                    <select class="form-control @error('position_id') is-invalid @enderror" 
                                                            id="position_id" name="position_id" required>
                                                        <option value="">Önce grup seçin</option>
                                                    </select>
                                                    @error('position_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Mevcut Görevler Gösterimi -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="existing-positions"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 4: Ödeme Bilgileri -->
                                <div class="tab-pane fade" id="tab-payment" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label required-field">Ödeme Periyodu</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-calendar-check"></i>
                                                        </span>
                                                    </div>
                                                    <div class="form-control" style="border: none; padding: 0.5rem 0.75rem;">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="payment_frequency" 
                                                                   id="frequency_monthly" value="monthly" 
                                                                   {{ old('payment_frequency', $employee->payment_frequency ?? 'monthly') == 'monthly' ? 'checked' : '' }} required>
                                                            <label class="form-check-label" for="frequency_monthly">Aylık</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="payment_frequency" 
                                                                   id="frequency_weekly" value="weekly" 
                                                                   {{ old('payment_frequency', $employee->payment_frequency) == 'weekly' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="frequency_weekly">Haftalık</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="payment_frequency" 
                                                                   id="frequency_daily" value="daily" 
                                                                   {{ old('payment_frequency', $employee->payment_frequency) == 'daily' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="frequency_daily">Günlük</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="payment_frequency" 
                                                                   id="frequency_hourly" value="hourly" 
                                                                   {{ old('payment_frequency', $employee->payment_frequency) == 'hourly' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="frequency_hourly">Saatlik</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('payment_frequency')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="working_days_per_month" class="form-label required-field">Aylık Çalışma Günü</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-calendar-day"></i>
                                                        </span>
                                                    </div>
                                                    <input type="number" class="form-control @error('working_days_per_month') is-invalid @enderror" 
                                                           id="working_days_per_month" name="working_days_per_month" 
                                                           value="{{ old('working_days_per_month', $employee->working_days_per_month ?? 30) }}" 
                                                           min="1" max="31" required>
                                                    @error('working_days_per_month')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Ücret Alanları -->
                                    <div class="row">
                                        <div class="col-md-6 wage-field" id="monthly_salary_field">
                                            <div class="form-group">
                                                <label for="monthly_salary" class="form-label required-field">Aylık Maaş (₺)</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-lira-sign"></i>
                                                        </span>
                                                    </div>
                                                    <input type="number" class="form-control @error('monthly_salary') is-invalid @enderror" 
                                                           id="monthly_salary" name="monthly_salary" 
                                                           value="{{ old('monthly_salary', $employee->monthly_salary) }}" 
                                                           step="0.01" min="0" placeholder="0.00">
                                                    @error('monthly_salary')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 wage-field" id="weekly_wage_field">
                                            <div class="form-group">
                                                <label for="weekly_wage" class="form-label required-field">Haftalık Ücret (₺)</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-lira-sign"></i>
                                                        </span>
                                                    </div>
                                                    <input type="number" class="form-control @error('weekly_wage') is-invalid @enderror" 
                                                           id="weekly_wage" name="weekly_wage" 
                                                           value="{{ old('weekly_wage', $employee->weekly_wage) }}" 
                                                           step="0.01" min="0" placeholder="0.00">
                                                    @error('weekly_wage')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 wage-field" id="daily_wage_field">
                                            <div class="form-group">
                                                <label for="daily_wage" class="form-label required-field">Günlük Ücret (₺)</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-lira-sign"></i>
                                                        </span>
                                                    </div>
                                                    <input type="number" class="form-control @error('daily_wage') is-invalid @enderror" 
                                                           id="daily_wage" name="daily_wage" 
                                                           value="{{ old('daily_wage', $employee->daily_wage) }}" 
                                                           step="0.01" min="0" placeholder="0.00">
                                                    @error('daily_wage')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 wage-field" id="hourly_wage_field">
                                            <div class="form-group">
                                                <label for="hourly_wage" class="form-label required-field">Saatlik Ücret (₺)</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-lira-sign"></i>
                                                        </span>
                                                    </div>
                                                    <input type="number" class="form-control @error('hourly_wage') is-invalid @enderror" 
                                                           id="hourly_wage" name="hourly_wage" 
                                                           value="{{ old('hourly_wage', $employee->hourly_wage) }}" 
                                                           step="0.01" min="0" placeholder="0.00">
                                                    @error('hourly_wage')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 5: Banka Bilgileri -->
                                <div class="tab-pane fade" id="tab-bank" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="iban" class="form-label required-field">IBAN</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-university"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control @error('iban') is-invalid @enderror" 
                                                           id="iban" name="iban" 
                                                           value="{{ old('iban', $employee->iban) }}" 
                                                           placeholder="IBAN veya hesap numarası" 
                                                           maxlength="255" required>
                                                    @error('iban')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <small class="form-text text-muted">IBAN veya hesap numarası</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="bank_name" class="form-label">Banka Adı</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fal fa-building"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                                           id="bank_name" name="bank_name" 
                                                           value="{{ old('bank_name', $employee->bank_name) }}" 
                                                           placeholder="Banka adı">
                                                    @error('bank_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Butonlar -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fal fa-save mr-2"></i> Güncelle
                                </button>
                                <a href="{{ route('employees') }}" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fal fa-times mr-2"></i> İptal
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Form validasyon hatalarını Türkçe SweetAlert ile göster
(function() {
    // Alan adlarının Türkçe karşılıkları
    const fieldNames = {
        'name': 'Ad Soyad',
        'phone': 'Telefon',
        'tc_no': 'TC Kimlik No',
        'sgk_no': 'SGK No',
        'hire_date': 'İşe Giriş Tarihi',
        'group_id': 'Grup (Departman)',
        'position_id': 'Görev (Pozisyon)',
        'payment_frequency': 'Ödeme Periyodu',
        'working_days_per_month': 'Aylık Çalışma Günü',
        'monthly_salary': 'Aylık Maaş',
        'weekly_wage': 'Haftalık Ücret',
        'daily_wage': 'Günlük Ücret',
        'hourly_wage': 'Saatlik Ücret',
        'iban': 'IBAN',
        'bank_name': 'Banka Adı',
        'address': 'Adres'
    };

    function getFieldLabel(fieldName) {
        return fieldNames[fieldName] || fieldName;
    }

    function showValidationError(fieldName, message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Form Doğrulama Hatası',
                html: '<strong>' + getFieldLabel(fieldName) + '</strong> alanı için:<br>' + message,
                confirmButtonText: 'Tamam',
                confirmButtonColor: '#dc3545'
            });
        } else {
            alert('Form Doğrulama Hatası: ' + getFieldLabel(fieldName) + ' - ' + message);
        }
    }

    function isFieldVisible(field) {
        if (!field || !field.offsetParent) {
            return false;
        }
        
        if (field.hasAttribute('hidden')) {
            return false;
        }
        
        const style = window.getComputedStyle(field);
        if (style.display === 'none' || style.visibility === 'hidden') {
            return false;
        }
        
        let parent = field.parentElement;
        while (parent && parent !== document.body) {
            const parentStyle = window.getComputedStyle(parent);
            if (parentStyle.display === 'none' || parentStyle.visibility === 'hidden') {
                return false;
            }
            parent = parent.parentElement;
        }
        
        const wageField = field.closest('.wage-field');
        if (wageField) {
            const wageStyle = window.getComputedStyle(wageField);
            if (wageStyle.display === 'none') {
                return false;
            }
        }
        
        const tabPane = field.closest('.tab-pane');
        if (tabPane) {
            if (!tabPane.classList.contains('show') && !tabPane.classList.contains('active')) {
                return false;
            }
        }
        
        return true;
    }

    function checkInvalidFields(form) {
        const invalidFields = [];
        const allFields = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        allFields.forEach(function(field) {
            const isVisible = isFieldVisible(field);
            
            if (!field.validity.valid || (!isVisible && field.hasAttribute('required'))) {
                let message = '';
                
                if (!isVisible) {
                    if (field.name === 'position_id') {
                        message = 'Görev seçimi yapılmamış. Lütfen önce "Grup ve Görev" sekmesinde bir grup seçin ve ardından görev seçin.';
                    } else if (field.name === 'monthly_salary' || field.name === 'weekly_wage' || 
                               field.name === 'daily_wage' || field.name === 'hourly_wage') {
                        message = 'Ödeme periyodu seçildikten sonra ilgili ücret alanını doldurmalısınız. Lütfen "Ödeme Bilgileri" sekmesinde ödeme periyodunu seçin.';
                    } else {
                        message = 'Bu alan görünür değil veya gizli. Lütfen ilgili sekmede bu alanı doldurun.';
                    }
                } else if (field.validity.valueMissing) {
                    if (field.tagName === 'SELECT' && field.value === '') {
                        if (field.name === 'position_id') {
                            message = 'Görev seçimi yapılmamış. Lütfen bir görev seçin.';
                        } else if (field.name === 'group_id') {
                            message = 'Grup seçimi yapılmamış. Lütfen bir grup seçin.';
                        } else {
                            message = 'Bu alan zorunludur ve doldurulmalıdır.';
                        }
                    } else {
                        message = 'Bu alan zorunludur ve doldurulmalıdır.';
                    }
                } else if (field.validity.patternMismatch) {
                    if (field.name === 'tc_no') {
                        message = 'TC Kimlik No 11 haneli olmalıdır ve sadece rakam içermelidir.';
                    } else if (field.name === 'phone') {
                        message = 'Telefon numarası 10 veya 11 haneli olmalıdır ve sadece rakam içermelidir.';
                    } else if (field.name === 'iban') {
                        message = 'IBAN alanı zorunludur.';
                    } else {
                        message = 'Bu alan için geçersiz format. Lütfen doğru formatta girin.';
                    }
                } else if (field.validity.typeMismatch) {
                    if (field.type === 'date') {
                        message = 'Geçerli bir tarih seçiniz.';
                    } else {
                        message = 'Bu alan için geçersiz veri tipi. Lütfen doğru formatta girin.';
                    }
                } else {
                    message = 'Bu alan için geçersiz değer.';
                }
                
                invalidFields.push({
                    field: field,
                    name: field.name,
                    message: message
                });
            }
        });
        
        return invalidFields;
    }

    function scrollToField(field) {
        const tabPane = field.closest('.tab-pane');
        if (tabPane) {
            const tabId = tabPane.getAttribute('id');
            const tabLink = document.querySelector('a[href="#' + tabId + '"]');
            if (tabLink) {
                tabLink.click();
                setTimeout(function() {
                    field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    field.focus();
                }, 300);
            } else {
                field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                field.focus();
            }
        } else {
            field.scrollIntoView({ behavior: 'smooth', block: 'center' });
            field.focus();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('employeeForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const invalidFields = checkInvalidFields(form);
                    
                    if (invalidFields.length > 0) {
                        const firstInvalid = invalidFields[0];
                        const field = firstInvalid.field;
                        const tabPane = field.closest('.tab-pane');
                        if (tabPane && !tabPane.classList.contains('show')) {
                            const tabId = tabPane.getAttribute('id');
                            const tabLink = document.querySelector('a[href="#' + tabId + '"]');
                            if (tabLink) {
                                tabLink.click();
                            }
                        }
                        
                        setTimeout(function() {
                            showValidationError(firstInvalid.name, firstInvalid.message);
                            
                            setTimeout(function() {
                                scrollToField(field);
                                field.classList.add('is-invalid');
                            }, 500);
                        }, 100);
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Form Doğrulama Hatası',
                                text: 'Lütfen tüm zorunlu alanları doldurun.',
                                confirmButtonText: 'Tamam',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    }
                    
                    form.classList.add('was-validated');
                    return false;
                }
            });
        }
    });
})();

// jQuery yüklenene kadar bekle
(function() {
    function initEmployeeForm() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initEmployeeForm, 100);
            return;
        }
        
        jQuery(document).ready(function($) {
            var currentPositionId = '{{ old("position_id", $employee->position_id ?? 0) }}';
            
            // Grup seçildiğinde görevleri yükle
            $('#group_id').on('change', function() {
                var groupId = $(this).val();
                var positionSelect = $('#position_id');
                var existingPositionsDiv = $('#existing-positions');
                
                positionSelect.html('<option value="">Yükleniyor...</option>');
                existingPositionsDiv.html('');
                
                if (groupId) {
                    $.ajax({
                        url: '{{ route("employees.positions.by.group") }}',
                        data: { group_id: groupId },
                        success: function(positions) {
                            positionSelect.empty();
                            positionSelect.append('<option value="">Görev Seçin</option>');
                            
                            positions.forEach(function(position) {
                                var selected = position.id == currentPositionId ? 'selected' : '';
                                positionSelect.append(
                                    '<option value="' + position.id + '" ' + selected + '>' + 
                                    position.name + '</option>'
                                );
                            });
                            
                            if (positions.length > 0) {
                                var html = '<div class="alert alert-info mt-3 mb-0">';
                                html += '<h6 class="mb-2"><i class="fal fa-info-circle mr-2"></i>Bu Gruba Ait Mevcut Görevler:</h6>';
                                html += '<div class="row">';
                                
                                positions.forEach(function(position) {
                                    var isCurrent = position.id == currentPositionId;
                                    var badgeClass = isCurrent ? 'badge-success' : 'badge-primary';
                                    var currentBadge = isCurrent ? ' <small>(Mevcut)</small>' : '';
                                    
                                    html += '<div class="col-md-4 mb-2">';
                                    html += '<span class="badge ' + badgeClass + ' p-2 position-badge" style="cursor: pointer; transition: all 0.3s;" data-position-id="' + position.id + '" data-position-name="' + position.name + '">';
                                    html += '<i class="fal fa-user-tag mr-1"></i>' + position.name + currentBadge;
                                    html += '</span>';
                                    html += '</div>';
                                });
                                
                                html += '</div>';
                                html += '<small class="text-muted d-block mt-2">Yukarıdaki görevlerden birine tıklayarak seçebilir veya yukarıdaki listeden seçebilirsiniz.</small>';
                                html += '</div>';
                                
                                existingPositionsDiv.html(html);
                                
                                $('.position-badge').on('click', function() {
                                    var positionId = $(this).data('position-id');
                                    $('#position_id').val(positionId).trigger('change');
                                    $('.position-badge').removeClass('badge-success').addClass('badge-primary');
                                    $(this).removeClass('badge-primary').addClass('badge-success');
                                    $(this).css('transform', 'scale(1.1)');
                                    setTimeout(function() {
                                        $('.position-badge').css('transform', 'scale(1)');
                                    }, 200);
                                });
                            } else {
                                existingPositionsDiv.html('<div class="alert alert-warning mt-3 mb-0"><i class="fal fa-exclamation-triangle mr-2"></i>Bu gruba ait henüz görev eklenmemiş. Önce görev ekleyin.</div>');
                            }
                        },
                        error: function() {
                            positionSelect.html('<option value="">Hata oluştu</option>');
                            existingPositionsDiv.html('<div class="alert alert-danger mt-3 mb-0"><i class="fal fa-exclamation-circle mr-2"></i>Görevler yüklenirken hata oluştu.</div>');
                        }
                    });
                } else {
                    positionSelect.html('<option value="">Önce grup seçin</option>');
                    existingPositionsDiv.html('');
                }
            });
            
            // Select'te görev seçildiğinde badge'i vurgula
            $('#position_id').on('change', function() {
                var selectedId = $(this).val();
                $('.position-badge').each(function() {
                    if ($(this).data('position-id') == selectedId) {
                        $(this).removeClass('badge-primary').addClass('badge-success');
                    } else {
                        $(this).removeClass('badge-success').addClass('badge-primary');
                    }
                });
            });
            
            // İlk yüklemede mevcut grup için görevleri yükle
            if ($('#group_id').val()) {
                $('#group_id').trigger('change');
            }
            
            // Ödeme periyodu değiştiğinde ücret alanını göster/gizle
            $('input[name="payment_frequency"]').on('change', function() {
                var frequency = $(this).val();
                
                $('.wage-field').hide().find('input').removeAttr('required');
                
                switch(frequency) {
                    case 'monthly':
                        $('#monthly_salary_field').show()
                            .find('input').attr('required', 'required');
                        break;
                    case 'weekly':
                        $('#weekly_wage_field').show()
                            .find('input').attr('required', 'required');
                        break;
                    case 'daily':
                        $('#daily_wage_field').show()
                            .find('input').attr('required', 'required');
                        break;
                    case 'hourly':
                        $('#hourly_wage_field').show()
                            .find('input').attr('required', 'required');
                        break;
                }
            });
            
            // Sayfa yüklendiğinde mevcut periyodu göster
            var currentFrequency = $('input[name="payment_frequency"]:checked').val() || 'monthly';
            $('input[name="payment_frequency"][value="' + currentFrequency + '"]').trigger('change');
            
            // TC Kimlik No sadece rakam
            $('#tc_no').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEmployeeForm);
    } else {
        initEmployeeForm();
    }
})();
</script>
