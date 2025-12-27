<style>
    /* Clean & Minimal Warehouse Modal - Scoped Styles */
    #addWarehouseModal {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        /* Bu CSS sadece addWarehouseModal içinde geçerlidir */
    }

    #addWarehouseModal .modal-dialog {
        max-width: 550px;
    }

    #addWarehouseModal .modal-content {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    #addWarehouseModal .modal-header {
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
    }

    #addWarehouseModal .modal-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #334155;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    #addWarehouseModal .modal-title i {
        color: #64748b;
        font-size: 1.25rem;
    }

    #addWarehouseModal .btn-close {
        background: none;
        border: none;
        color: #64748b;
        font-size: 1.125rem;
        padding: 0.5rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    #addWarehouseModal .btn-close:hover {
        background: #e2e8f0;
        color: #334155;
    }

    #addWarehouseModal .modal-body {
        padding: 1.5rem;
        background: white;
    }

    #addWarehouseModal .form-group {
        margin-bottom: 1.25rem;
    }

    #addWarehouseModal .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        display: block;
    }

    #addWarehouseModal .input-group {
        position: relative;
        display: flex;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    #addWarehouseModal .input-group:focus-within {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    #addWarehouseModal .input-group-text {
        background: #f9fafb;
        color: #6b7280;
        border: none;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        border-right: 1px solid #e5e7eb;
    }

    #addWarehouseModal .form-control,
    #addWarehouseModal .custom-select {
        border: none;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        background: white;
        flex: 1;
    }

    #addWarehouseModal .form-control:focus,
    #addWarehouseModal .custom-select:focus {
        outline: none;
        box-shadow: none;
    }

    #addWarehouseModal textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    #addWarehouseModal .custom-select {
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.25rem;
        padding-right: 2.5rem;
    }

    #addWarehouseModal .invalid-feedback {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.75rem;
        color: #dc2626;
    }

    #addWarehouseModal .form-control.is-invalid,
    #addWarehouseModal .custom-select.is-invalid {
        border-color: #fca5a5;
        background-color: #fef2f2;
    }

    #addWarehouseModal .form-control.is-valid,
    #addWarehouseModal .custom-select.is-valid {
        border-color: #86efac;
        background-color: #f0fdf4;
    }

    #addWarehouseModal .input-group.is-invalid {
        border-color: #fca5a5;
    }

    #addWarehouseModal .input-group.is-valid {
        border-color: #86efac;
    }

    #addWarehouseModal .modal-footer {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    #addWarehouseModal .btn {
        padding: 0.625rem 1.25rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.875rem;
        border: 1px solid;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        text-decoration: none;
    }

    #addWarehouseModal .btn-secondary {
        background: white;
        color: #374151;
        border-color: #d1d5db;
    }

    #addWarehouseModal .btn-secondary:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }

    #addWarehouseModal .btn-primary {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    #addWarehouseModal .btn-primary:hover {
        background: #2563eb;
        border-color: #2563eb;
    }

    #addWarehouseModal .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    #addWarehouseModal .btn.loading {
        position: relative;
        color: transparent;
    }

    #addWarehouseModal .btn.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 16px;
        height: 16px;
        margin: -8px 0 0 -8px;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        color: white;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 576px) {
        #addWarehouseModal .modal-dialog {
            margin: 1rem;
        }
        
        #addWarehouseModal .modal-body {
            padding: 1.25rem;
        }
        
        #addWarehouseModal .modal-footer {
            padding: 1rem 1.25rem;
            flex-direction: column;
        }
        
        #addWarehouseModal .btn {
            width: 100%;
            justify-content: center;
        }
    }

    /* Smooth modal animation */
    #addWarehouseModal.fade .modal-dialog {
        transition: transform 0.2s ease-out;
        transform: translateY(-20px);
    }

    #addWarehouseModal.show .modal-dialog {
        transform: translateY(0);
    }
</style>

<div class="modal fade" id="addWarehouseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fal fa-warehouse"></i>
                    Yeni Depo Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat">
                    <i class="fal fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="addWarehouseForm" method="POST" novalidate>
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="name">Depo Adı</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fal fa-warehouse-alt"></i>
                            </span>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Depo adını giriniz" required>
                        </div>
                        <div class="invalid-feedback">Bu alan zorunludur.</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="address">Adres</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fal fa-map-marker-alt"></i>
                            </span>
                            <textarea class="form-control" id="address" name="address" rows="3" placeholder="Depo adresini giriniz" required></textarea>
                        </div>
                        <div class="invalid-feedback">Bu alan zorunludur.</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="phone">Telefon</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fal fa-phone"></i>
                                    </span>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="(555) 123-4567" required>
                                </div>
                                <div class="invalid-feedback">Bu alan zorunludur.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="manager">Depo Sorumlusu</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fal fa-user"></i>
                                    </span>
                                    <select class="form-control custom-select" id="manager" name="manager" required>
                                        <option value="">Sorumlu seçiniz...</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="invalid-feedback">Bu alan zorunludur.</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fal fa-times"></i> İptal
                </button>
                <button type="button" class="btn btn-primary" id="saveWarehouse">
                    <i class="fal fa-save"></i> Kaydet
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('addWarehouseModal');
    const form = document.getElementById('addWarehouseForm');
    const saveBtn = document.getElementById('saveWarehouse');
    
    // Simple form validation
    function validateForm() {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            const value = field.value.trim();
            const inputGroup = field.closest('.input-group');
            
            // Clear previous states
            field.classList.remove('is-valid', 'is-invalid');
            inputGroup.classList.remove('is-valid', 'is-invalid');
            
            if (!value) {
                field.classList.add('is-invalid');
                inputGroup.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.add('is-valid');
                inputGroup.classList.add('is-valid');
            }
        });
        
        return isValid;
    }
    
    // Phone formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 10) {
                value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
            }
            e.target.value = value;
        });
    }
    
    // Save button handler
    saveBtn.addEventListener('click', function() {
        if (!validateForm()) {
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
            return;
        }
        
        // Loading state
        saveBtn.disabled = true;
        saveBtn.classList.add('loading');
        
        // Prepare data
        const formData = new FormData(form);
        
        // CSRF token zaten formda mevcut (@csrf direktifi ile)
        
        // Submit
        fetch('{{ route("warehouse.add") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Server error: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('Depo başarıyla eklendi!', 'success');
                form.reset();
                form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
                    el.classList.remove('is-valid', 'is-invalid');
                });
                
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
                
                if (typeof refreshWarehouseTable === 'function') {
                    refreshWarehouseTable();
                } else {
                    setTimeout(() => window.location.reload(), 1000);
                }
            } else {
                showNotification(data.message || 'Bir hata oluştu!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Sunucu hatası oluştu!', 'error');
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.classList.remove('loading');
        });
    });
    
    // Reset on modal close
    modal.addEventListener('hidden.bs.modal', function() {
        form.reset();
        form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
            el.classList.remove('is-valid', 'is-invalid');
        });
        saveBtn.disabled = false;
        saveBtn.classList.remove('loading');
    });
    
    // Simple notification
    function showNotification(message, type = 'info') {
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: type === 'success' ? 'Başarılı!' : 'Hata!',
                text: message,
                icon: type === 'success' ? 'success' : 'error',
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }
});
</script>
