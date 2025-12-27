@foreach($employees as $employee)
<div class="modal fade" id="editUserModal{{ $employee->id }}" tabindex="-1" role="dialog" aria-labelledby="editUserModal{{ $employee->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModal{{ $employee->id }}">Personel Düzenle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('employees.update') }}" method="POST" enctype="multipart/form-data" class="editEmployeeForm">
                @csrf
                <input type="hidden" name="id" value="{{ $employee->id }}">
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="employeeEditTab{{ $employee->id }}" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="tab-basic-tab-{{ $employee->id }}" data-toggle="tab" href="#tab-basic-{{ $employee->id }}" role="tab" aria-controls="tab-basic-{{ $employee->id }}" aria-selected="true">Temel Bilgiler</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="tab-commission-tab-{{ $employee->id }}" data-toggle="tab" href="#tab-commission-{{ $employee->id }}" role="tab" aria-controls="tab-commission-{{ $employee->id }}" aria-selected="false">Prim Tanımlama</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="employeeEditTabContent{{ $employee->id }}">
                        <div class="tab-pane fade show active" id="tab-basic-{{ $employee->id }}" role="tabpanel" aria-labelledby="tab-basic-tab-{{ $employee->id }}">
                            <div class="container">
                                <!-- Avatar Preview -->
                                <div class="row mb-4">
                                    <div class="col-12 text-center">
                                        <div class="avatar-preview mb-3">
                                            @if($employee->avatar)
                                                <img id="avatarPreview{{ $employee->id }}" src="{{ asset($employee->avatar) }}" alt="Avatar Preview" class="avatar-circle">
                                            @else
                                                <img id="avatarPreview{{ $employee->id }}" src="" alt="Avatar Preview" class="avatar-circle d-none">
                                            @endif
                                            <div class="avatar-placeholder {{ $employee->avatar ? 'd-none' : '' }}" id="avatarPlaceholder{{ $employee->id }}">
                                                <i class="fal fa-user-circle fa-5x text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Row 1: Basic Info -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Çalışan Adı <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fal fa-user"></i></span>
                                                <input type="text" class="form-control" id="name" name="name" value="{{ $employee->name }}" required placeholder="Ad ve Soyadınızı girin">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="skills" class="form-label">Uzmanlık Alanı <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fal fa-star"></i></span>
                                                <select class="custom-select" name="skills" id="skills{{ $employee->id }}" required>
                                                    <option value="">Uzmanlık alanı seçiniz</option>
                                                    @if(isset($specialties) && $specialties->count() > 0)
                                                        @foreach ($specialties as $specialty)
                                                            <option value="{{ $specialty->id }}" {{ $employee->skills == $specialty->id ? 'selected' : '' }}>{{ $specialty->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Row 2: Contact Info -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Telefon <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fal fa-phone"></i></span>
                                                <input type="number" class="form-control" id="phone" name="phone" value="{{ $employee->phone }}" required placeholder="Telefon numarasını girin" maxlength="11">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">E-posta</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fal fa-envelope"></i></span>
                                                <input type="email" class="form-control" id="email" name="email" value="{{ $employee->email }}" placeholder="E-posta adresinizi girin">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Row 3: Avatar and Hiring -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="avatar" class="form-label">Avatar</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fal fa-user-circle"></i></span>
                                                <input type="file" class="form-control" name="avatar" id="avatarInput{{ $employee->id }}" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="hire_date" class="form-label">İşe Giriş Tarihi</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fal fa-calendar-alt"></i></span>
                                                <input type="date" class="form-control" id="hire_date" name="hire_date" value="{{ $employee->hire_date }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Row 4: Aktif Durumu -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="is_active" class="form-label">Aktif Durumu</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fal fa-toggle-on"></i></span>
                                                <select class="custom-select" id="is_active" name="is_active">
                                                    <option value="1" {{ $employee->is_active ? 'selected' : '' }}>Aktif</option>
                                                    <option value="0" {{ !$employee->is_active ? 'selected' : '' }}>Pasif</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="table_id" class="form-label">Oda / Çalışma Masası</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fal fa-toggle-on"></i></span>
                                                <select class="custom-select" id="table_id" name="table_id">
                                                    <option value="">Oda / Çalışma Masası seçiniz</option>
                                                    @foreach($tables as $table)
                                                        <option value="{{ $table->id }}" {{ $employee->table_id == $table->id ? 'selected' : '' }}>{{ $table->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-commission-{{ $employee->id }}" role="tabpanel" aria-labelledby="tab-commission-tab-{{ $employee->id }}">
                            <div class="container">
                                <div class="alert alert-info mb-3">Aşağıda listelenen hizmetler için çalışana prim tanımlayabilirsiniz.</div>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Hizmet Adı</th>
                                                <th class="text-center">Prim Verilsin mi?</th>
                                                <th class="text-center">Prim Oranı (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($services as $service)
                                                @continue(empty($service->is_stock) ? false : $service->is_stock != 0)

                                                @php
                                                    $commission = false;
                                                    $rate = '';
                                                    // $employee->commissions artık bir Collection ve her eleman bir obje
                                                    if (!empty($employee->commissions) && $employee->commissions instanceof \Illuminate\Support\Collection) {
                                                        $commissionObj = $employee->commissions->firstWhere('service_id', $service->id);
                                                        if ($commissionObj) {
                                                            $commission = true;
                                                            $rate = $commissionObj->commission_rate;
                                                        }
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>{{ $service->name }}</td>
                                                    <td class="text-center">
                                                        <div class="form-check form-switch d-flex justify-content-center">
                                                            <input class="form-check-input commission-switch"
                                                                type="checkbox"
                                                                id="commissionSwitch_{{ $employee->id }}_{{ $service->id }}"
                                                                name="commission[{{ $service->id }}][enabled]"
                                                                {{ $commission ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="number"
                                                            class="form-control commission-rate-input"
                                                            name="commission[{{ $service->id }}][rate]"
                                                            id="commissionRate_{{ $employee->id }}_{{ $service->id }}"
                                                            min="0" max="100" step="0.01" placeholder="%"
                                                            value="{{ $rate }}"
                                                            {{ $commission ? '' : 'disabled' }}
                                                            style="max-width:120px; margin:0 auto;">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-column align-items-stretch">
                    <div id="skillsAlert{{ $employee->id }}" class="alert alert-danger d-none mb-2" role="alert">
                        Lütfen uzmanlık alanı seçiniz.
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
    // Safe DOM ready function
    function safeReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    safeReady(function() {
        try {
            // Avatar preview functionality for all employees
            document.querySelectorAll('[id^="avatarInput"]').forEach(avatarInput => {
                avatarInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const employeeId = this.id.replace('avatarInput', '');
                    
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const avatarPreview = document.getElementById(`avatarPreview${employeeId}`);
                            const avatarPlaceholder = document.getElementById(`avatarPlaceholder${employeeId}`);
                            if (avatarPreview && avatarPlaceholder) {
                                avatarPreview.src = e.target.result;
                                avatarPreview.classList.remove('d-none');
                                avatarPlaceholder.classList.add('d-none');
                            }
                        };
                        reader.readAsDataURL(file);
                    } else {
                        const avatarPreview = document.getElementById(`avatarPreview${employeeId}`);
                        const avatarPlaceholder = document.getElementById(`avatarPlaceholder${employeeId}`);
                        if (avatarPreview && avatarPlaceholder) {
                            avatarPreview.classList.add('d-none');
                            avatarPlaceholder.classList.remove('d-none');
                        }
                    }
                });
            });

            // Uzmanlık alanı kontrolü ve alert gösterimi
            document.querySelectorAll('.editEmployeeForm').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const employeeId = this.closest('.modal').id.replace('editUserModal', '');
                    const skillsSelect = document.getElementById(`skills${employeeId}`);
                    const skillsAlert = document.getElementById(`skillsAlert${employeeId}`);
                    
                    // Validation flag
                    let hasError = false;
                    
                    // Skills validation
                    if (skillsSelect && skillsAlert) {
                        if (!skillsSelect.value) {
                            skillsAlert.classList.remove('d-none');
                            hasError = true;
                        } else {
                            skillsAlert.classList.add('d-none');
                        }
                    }
                    
                    // Name validation
                    const nameInput = this.querySelector('input[name="name"]');
                    if (nameInput && !nameInput.value.trim()) {
                        alert('Lütfen çalışan adını girin.');
                        nameInput.focus();
                        hasError = true;
                    }
                    
                    // Phone validation
                    const phoneInput = this.querySelector('input[name="phone"]');
                    if (phoneInput && !phoneInput.value.trim()) {
                        alert('Lütfen telefon numarasını girin.');
                        phoneInput.focus();
                        hasError = true;
                    }
                    
                    // Prevent form submission if there are errors
                    if (hasError) {
                        e.preventDefault();
                        return false;
                    }
                    
                    // CSRF Token kontrolü
                    const csrfToken = this.querySelector('input[name="_token"]');
                    if (!csrfToken || !csrfToken.value) {
                        console.error('CSRF token bulunamadı!');
                        alert('Güvenlik hatası: Sayfa yenilenmelidir.');
                        e.preventDefault();
                        return false;
                    }
                    
                    // Route kontrolü
                    if (!this.action) {
                        console.error('Form action URL bulunamadı!');
                        alert('Form ayarlarında hata var.');
                        e.preventDefault();
                        return false;
                    }
                    
                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Kaydediliyor...';
                        
                        // Re-enable after 5 seconds in case of any issues
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = 'Kaydet';
                        }, 5000);
                    }
                    
                    // Debug: Log form submission
                    console.log('Form submission başlatıldı:', {
                        employeeId: employeeId,
                        formAction: this.action,
                        formData: new FormData(this)
                    });
                    
                    // Add error handling for form submission
                    setTimeout(() => {
                        // Check if we're still on the same page after submission
                        if (submitBtn && submitBtn.disabled) {
                            console.warn('Form submission tamamlanmadı - olası network hatası');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = 'Kaydet';
                            alert('Form gönderilirken bir hata oluştu. Lütfen tekrar deneyin.');
                        }
                    }, 10000); // 10 saniye sonra kontrol et
                });
            });

            // Seçim değiştiğinde alerti gizle
            document.querySelectorAll('[id^="skills"]').forEach(skillsSelect => {
                skillsSelect.addEventListener('change', function() {
                    const employeeId = this.id.replace('skills', '');
                    const skillsAlert = document.getElementById(`skillsAlert${employeeId}`);
                    if (this.value && skillsAlert) {
                        skillsAlert.classList.add('d-none');
                    }
                });
            });

            // Tüm edit modalları için tek seferde delegasyon (saf JS)
            document.body.addEventListener('change', function(e) {
                if (e.target.classList.contains('commission-switch')) {
                    var parts = e.target.id.split('_');
                    var empId = parts[1];
                    var serviceId = parts[2];
                    var rateInput = document.getElementById('commissionRate_' + empId + '_' + serviceId);
                    if (rateInput) {
                        if (e.target.checked) {
                            rateInput.disabled = false;
                            rateInput.focus();
                        } else {
                            rateInput.disabled = true;
                            rateInput.value = '';
                        }
                    }
                }
            });

        } catch (error) {
            console.warn('Employee edit modal initialization error:', error);
        }
    });
</script>

<style>
.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    color: #6c757d;
}

.input-group-text i {
    width: 16px;
    text-align: center;
}

.custom-select {
    border: 1px solid #ced4da;
}

.input-group .custom-select {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.input-group-text {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.avatar-preview {
    position: relative;
}

.avatar-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #dee2e6;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.avatar-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 120px;
    height: 120px;
    margin: 0 auto;
    border-radius: 50%;
    border: 2px dashed #dee2e6;
    background-color: #f8f9fa;
}
</style>
