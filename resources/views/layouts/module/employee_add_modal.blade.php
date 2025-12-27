
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Çalışan Ekle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('employees.add') }}" method="POST" enctype="multipart/form-data" id="addEmployeeForm" class="addEmployeeForm">
                @csrf
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="employeeAddTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="tab-basic-tab" data-toggle="tab" href="#tab-basic" role="tab" aria-controls="tab-basic" aria-selected="true">Temel Bilgiler</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="tab-commission-tab" data-toggle="tab" href="#tab-commission" role="tab" aria-controls="tab-commission" aria-selected="false">Prim Tanımlama</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="employeeAddTabContent">
                        <div class="tab-pane fade show active" id="tab-basic" role="tabpanel" aria-labelledby="tab-basic-tab">
                            <div class="container">
                                <!-- Avatar Preview -->
                                <div class="row mb-4">
                                    <div class="col-12 text-center">
                                        <div class="avatar-preview mb-3">
                                            <img id="avatarPreview" src="" alt="Avatar Preview" class="avatar-circle d-none">
                                            <div class="avatar-placeholder" id="avatarPlaceholder">
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
                                                <input type="text" class="form-control" id="name" name="name" required placeholder="Ad ve Soyadınızı girin">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="skills" class="form-label">Uzmanlık Alanı <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fal fa-star"></i></span>
                                                <input type="text" class="form-control" name="skills" id="skills" placeholder="Uzmanlık alanını giriniz" required>
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
                                                <input type="number" class="form-control" id="phone" name="phone" required placeholder="Telefon numarasını girin" maxlength="11">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">E-posta</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fal fa-envelope"></i></span>
                                                <input type="email" class="form-control" id="email" name="email" placeholder="E-posta adresinizi girin">
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
                                                <input type="file" class="form-control" name="avatar" id="avatarInput" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="hire_date" class="form-label">İşe Giriş Tarihi <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fal fa-calendar-alt"></i></span>
                                                <input type="date" class="form-control" id="hire_date" name="hire_date" required>
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
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Pasif</option>
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
                                                        <option value="{{ $table->id }}">{{ $table->table_number }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-commission" role="tabpanel" aria-labelledby="tab-commission-tab">
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
                                                @if($service->is_stock == 0)
                                                <tr>
                                                    <td>{{ $service->name }}</td>
                                                    <td class="text-center">
                                                        <div class="form-check form-switch d-flex justify-content-center">
                                                            <input class="form-check-input commission-switch" type="checkbox" id="commissionSwitch_{{ $service->id }}" name="commission[{{ $service->id }}][enabled]">
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="number" class="form-control commission-rate-input" name="commission[{{ $service->id }}][rate]" id="commissionRate_{{ $service->id }}" min="0" max="100" step="0.01" placeholder="%" disabled style="max-width:120px; margin:0 auto;">
                                                    </td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-column align-items-stretch">
                    <div id="skillsAlert" class="alert alert-danger d-none mb-2" role="alert">
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
            // Form reset function
            function resetEmployeeForm(form) {
                try {
                    form.reset();
                    
                    // Reset Select2 if available
                    if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
                        form.querySelectorAll('select').forEach(select => {
                            if ($(select).hasClass('select2-hidden-accessible')) {
                                $(select).val(null).trigger('change');
                            }
                        });
                    }
                    
                    // Reset avatar preview
                    const avatarPreview = form.querySelector('#avatarPreview');
                    const avatarPlaceholder = form.querySelector('#avatarPlaceholder');
                    if (avatarPreview && avatarPlaceholder) {
                        avatarPreview.classList.add('d-none');
                        avatarPlaceholder.classList.remove('d-none');
                        avatarPreview.src = '';
                    }
                } catch (error) {
                    console.warn('Form reset error:', error);
                }
            }

            // Add employee to DataTable
            function addEmployeeToTable(employee) {
                try {
                    const employeesTable = document.getElementById('dt-basic-example');
                    if (!employeesTable) return;
                    
                    // Format dates
                    const hireDate = new Date(employee.hire_date).toLocaleDateString('tr-TR');
                    const paidAmount = parseFloat(employee.paid_amount || 0).toFixed(2);
                    const unpaidAmount = parseFloat(employee.unpaid_amount || 0).toFixed(2);
                    
                    // If DataTable exists and is properly initialized
                    if (typeof $ !== 'undefined' && $.fn && $.fn.DataTable && 
                        typeof $.fn.DataTable.isDataTable === 'function' && 
                        $.fn.DataTable.isDataTable('#dt-basic-example')) {
                        try {
                            const table = $('#dt-basic-example').DataTable();
                            const newRowData = [
                                employee.name,
                                employee.specialty_name || '-',
                                employee.phone,
                                hireDate,
                                `${paidAmount} TL`,
                                `${unpaidAmount} TL`,
                                `<button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editUserModal${employee.id}"
                                    title="Çalışanı Düzenle">
                                    <i class="fal fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#historyModal${employee.id}"
                                    title="İşlem Geçmişi">
                                    <i class="fal fa-history"></i>
                                </button>
                                <button class="btn btn-danger btn-sm delete-employee-btn"
                                    data-id="${employee.id}" title="Çalışanı Sil">
                                    <i class="fal fa-trash"></i>
                                </button>`
                            ];
                            
                            const newRow = table.row.add(newRowData).draw();
                            const rowNode = newRow.node();
                            rowNode.setAttribute('data-id', employee.id);
                            
                            console.log('Employee successfully added to DataTable');
                            return;
                        } catch (e) {
                            console.warn('DataTable add error:', e);
                        }
                    }
                    
                    // If no DataTable, add to regular table
                    const tbody = employeesTable.querySelector('tbody');
                    if (tbody) {
                        const newRow = document.createElement('tr');
                        newRow.setAttribute('data-id', employee.id);
                        newRow.innerHTML = `
                            <td>${employee.name}</td>
                            <td>${employee.specialty_name || '-'}</td>
                            <td>${employee.phone}</td>
                            <td>${hireDate}</td>
                            <td>${paidAmount} TL</td>
                            <td>${unpaidAmount} TL</td>
                            <td>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editUserModal${employee.id}"
                                    title="Çalışanı Düzenle">
                                    <i class="fal fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#historyModal${employee.id}"
                                    title="İşlem Geçmişi">
                                    <i class="fal fa-history"></i>
                                </button>
                                <button class="btn btn-danger btn-sm delete-employee-btn"
                                    data-id="${employee.id}" title="Çalışanı Sil">
                                    <i class="fal fa-trash"></i>
                                </button>
                            </td>
                        `;
                        
                        tbody.appendChild(newRow);
                        console.log('Employee added to regular table');
                    }
                } catch (error) {
                    console.error('Table update error:', error);
                }
            }

            // Avatar preview functionality
            const avatarInput = document.getElementById('avatarInput');
            if (avatarInput) {
                avatarInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const avatarPreview = document.getElementById('avatarPreview');
                            const avatarPlaceholder = document.getElementById('avatarPlaceholder');
                            if (avatarPreview && avatarPlaceholder) {
                                avatarPreview.src = e.target.result;
                                avatarPreview.classList.remove('d-none');
                                avatarPlaceholder.classList.add('d-none');
                            }
                        };
                        reader.readAsDataURL(file);
                    } else {
                        const avatarPreview = document.getElementById('avatarPreview');
                        const avatarPlaceholder = document.getElementById('avatarPlaceholder');
                        if (avatarPreview && avatarPlaceholder) {
                            avatarPreview.classList.add('d-none');
                            avatarPlaceholder.classList.remove('d-none');
                        }
                    }
                });
            }

            // AJAX ile çalışan ekleme
            const addEmployeeForm = document.getElementById('addEmployeeForm');
            if (addEmployeeForm) {
                // Klasik submit kullanılacak, herhangi bir JS engellemesi yok
            }

        } catch (error) {
            console.warn('Employee add modal initialization error:', error);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Tablar arası geçiş için (Bootstrap tabları için gerekirse)
        var tabLinks = document.querySelectorAll('a[data-toggle="tab"]');
        tabLinks.forEach(function(link) {
            link.addEventListener('shown.bs.tab', function(e) {
                // Gerekirse tab değişiminde bir şey yapılabilir
            });
        });

        // Prim switchleri için saf JS ile delegasyon
        var addEmployeeModal = document.getElementById('addEmployeeModal');
        if (addEmployeeModal) {
            addEmployeeModal.addEventListener('change', function(e) {
                if (e.target.classList.contains('commission-switch')) {
                    var serviceId = e.target.id.replace('commissionSwitch_', '');
                    var rateInput = document.getElementById('commissionRate_' + serviceId);
                    if (e.target.checked) {
                        rateInput.disabled = false;
                        rateInput.focus();
                    } else {
                        rateInput.disabled = true;
                        rateInput.value = '';
                    }
                }
            });
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
