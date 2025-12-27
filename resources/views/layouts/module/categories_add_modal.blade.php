

<style>
    .swal2-container, .sweet-alert {
  z-index: 20000 !important;
}
</style>
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addCategoryModalLabel">
                    <i class="fal fa-layer-plus me-2"></i>
                    {{ isset($stockType) && $stockType == 1 ? 'Ürün Kategorisi Yönetimi' : 'Hizmet Kategorisi Yönetimi' }}
                </h5>
                <button type="button" class="btn btn-sm text-white" data-bs-dismiss="modal" aria-label="Kapat" style="border: none; background: none;">
            <i class="fal fa-times"></i>
        </button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Sol Taraf - Kategori Listesi -->
                    <div class="col-md-7 border-end">
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="mb-0 text-dark">
                                    <i class="fal fa-list me-2 text-primary"></i>
                                    Mevcut {{ isset($stockType) && $stockType == 1 ? 'Ürün Kategorileri' : 'Hizmet Kategorileri' }}
                                </h6>
                                <span class="badge bg-secondary" id="categoryCount">0 {{ isset($stockType) && $stockType == 1 ? 'Ürün Kategorisi' : 'Hizmet Kategorisi' }}</span>
                            </div>
                            
                            <div class="table-responsive" style="max-height: 450px;">
                                <table id="categoriesTable" class="table table-hover table-sm">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th width="50">Resim</th>
                                            <th>Kategori Adı</th>
                                            <th width="80">Durum</th>
                                            <th width="100">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody id="categoriesTableBody">
                                        <!-- Kategoriler buraya yüklenecek -->
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Boş durum mesajı -->
                            <div id="emptyCategoriesMessage" class="text-center py-5">
                                <i class="fal fa-folder-open fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">Henüz {{ isset($stockType) && $stockType == 1 ? 'ürün kategorisi' : 'hizmet kategorisi' }} bulunmamaktadır</h6>
                                <p class="text-muted small">Sağ taraftaki formu kullanarak yeni {{ isset($stockType) && $stockType == 1 ? 'ürün kategorisi' : 'hizmet kategorisi' }} ekleyebilirsiniz.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sağ Taraf - Kategori Ekleme/Düzenleme Formu -->
                    <div class="col-md-5">
                        <div class="p-4">
                            <div class="mb-4">
                                <h6 class="text-dark mb-0">
                                    <i class="fal fa-plus-circle me-2 text-success"></i>
                                    <span id="formTitle">Yeni {{ isset($stockType) && $stockType == 1 ? 'Ürün Kategorisi' : 'Hizmet Kategorisi' }} Ekle</span>
                                </h6>
                            </div>
                            
                            <form id="categoryForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="categoryId" name="id">
                                <input type="hidden" id="categoryStockType" name="stock_type" value="{{ isset($stockType) ? $stockType : 0 }}">
                                
                                <!-- Kategori Adı -->
                                <div class="mb-3">
                                    <label for="categoryName" class="form-label fw-medium">
                                        <i class="fal fa-tag me-1"></i>{{ isset($stockType) && $stockType == 1 ? 'Ürün Kategorisi' : 'Hizmet Kategorisi' }} Adı <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="categoryName" name="name" 
                                           placeholder="Kategori adını girin" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Kategori Açıklaması -->
                                <div class="mb-3">
                                    <label for="categoryDescription" class="form-label fw-medium">
                                        <i class="fal fa-align-left me-1"></i>{{ isset($stockType) && $stockType == 1 ? 'Ürün Kategorisi' : 'Hizmet Kategorisi' }} Açıklaması
                                    </label>
                                    <textarea class="form-control" id="categoryDescription" name="description" 
                                              rows="3" placeholder="Kategori açıklamasını girin"></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Kategori Resmi -->
                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        <i class="fal fa-image me-1"></i>Kategori Resmi
                                    </label>
                                    <div class="border rounded p-3 text-center bg-light">
                                        <div id="imagePreview" class="mb-3">
                                            <div class="d-flex align-items-center justify-content-center" 
                                                 style="height: 100px; border: 2px dashed #ddd; border-radius: 8px;">
                                                <div class="text-center">
                                                    <i class="fal fa-cloud-upload fa-2x text-muted mb-2"></i>
                                                    <div class="small text-muted">Resim seçin</div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="file" class="form-control" id="categoryImage" name="image" 
                                               accept="image/*">
                                        <small class="text-muted">PNG, JPG, GIF (Maks. 2MB)</small>
                                    </div>
                                </div>

                                <!-- Görünürlük Durumu -->
                                <div class="mb-4">
                                    <label class="form-label fw-medium">
                                        <i class="fal fa-eye me-1"></i>Görünürlük Durumu
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="categoryStatus" 
                                               name="is_active" value="1" checked>
                                        <label class="form-check-label" for="categoryStatus">
                                            <span id="statusLabel">Aktif</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Form Butonları -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fal fa-check me-2"></i>{{ isset($stockType) && $stockType == 1 ? 'Ürün Kategorisi Ekle' : 'Hizmet Kategorisi Ekle' }}
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="resetFormBtn">
                                        <i class="fal fa-undo me-2"></i>Formu Temizle
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fal fa-times me-2"></i>Kapat
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CSS Stilleri -->
<style>
    #addCategoryModal .modal-content {
        border-radius: 15px;
        overflow: hidden;
    }
    
    #addCategoryModal .table thead th {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    #addCategoryModal .table tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }
    
    #addCategoryModal .form-control:focus,
    #addCategoryModal .form-check-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    #addCategoryModal .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
    
    #addCategoryModal #imagePreview img {
        max-width: 100%;
        max-height: 100px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    #addCategoryModal .table-responsive::-webkit-scrollbar {
        width: 6px;
    }
    
    #addCategoryModal .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    
    #addCategoryModal .table-responsive::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    
    #addCategoryModal .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    #addCategoryModal .border-end {
        border-right: 1px solid #dee2e6 !important;
    }
</style>

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
            const categoryForm = document.getElementById('categoryForm');
            const categoryId = document.getElementById('categoryId');
            const formTitle = document.getElementById('formTitle');
            const submitBtn = document.getElementById('submitBtn');
            const resetFormBtn = document.getElementById('resetFormBtn');
            const categoryStatus = document.getElementById('categoryStatus');
            const statusLabel = document.getElementById('statusLabel');
            const imageInput = document.getElementById('categoryImage');
            const imagePreview = document.getElementById('imagePreview');
            let isEditMode = false;

            // Modal açıldığında kategorileri yükle
            const categoryModal = document.getElementById('addCategoryModal');
            if (categoryModal) {
                categoryModal.addEventListener('shown.bs.modal', function() {
                    loadCategories();
                });
                // Modal kapatıldığında tabloyu yenile
                categoryModal.addEventListener('hidden.bs.modal', function() {
                    setTimeout(function() { window.location.reload(); }, 200);
                });
            }

            // Form submit işlemi
            if (categoryForm) {
                categoryForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Kategori açıklaması zorunlu değil, bu yüzden herhangi bir kontrol veya zorunluluk kaldırıldı

                    const formData = new FormData(this);
                    const originalText = submitBtn.innerHTML;

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fal fa-spinner fa-spin me-2"></i>Kaydediliyor...';

                    const url = isEditMode ? "{{ route('categories.updateAjax') }}" : "{{ route('categories.addAjax') }}";

                    if (typeof axios !== 'undefined') {
                        axios.post(url, formData)
                            .then(res => {
                                if (res.data.success) {
                                    showSuccess(res.data.message);

                                    if (isEditMode) {
                                        updateCategoryInTable(res.data.category);
                                    } else {
                                        addCategoryToTable(res.data.category);
                                        // Hizmet ekleme modalındaki select'e de ekle
                                        if (window.addCategoryToServiceSelect) {
                                            window.addCategoryToServiceSelect({
                                                id: res.data.category.id,
                                                name: res.data.category.name
                                            });
                                        }
                                        // Düzenleme modalı için de ekle
                                        if (window.addCategoryToServiceEditSelect) {
                                            window.addCategoryToServiceEditSelect({
                                                id: res.data.category.id,
                                                name: res.data.category.name
                                            });
                                        }
                                    }

                                    resetForm();
                                    updateCategoryCount();
                                }
                            })
                            .catch(err => {
                                let msg = 'Kategori kaydedilirken bir hata oluştu';
                                if (err.response?.data?.errors) {
                                    // Kategori açıklaması zorunlu değil, hata mesajlarından açıklama alanını çıkar
                                    const errors = Object.assign({}, err.response.data.errors);
                                    if (errors.description) {
                                        delete errors.description;
                                    }
                                    msg = Object.values(errors).flat().join('<br>');
                                    if (!msg) msg = 'Kategori kaydedilirken bir hata oluştu';
                                } else if (err.response?.data?.message) {
                                    msg = err.response.data.message;
                                }
                                showError(msg);
                            })
                            .finally(() => {
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = originalText;
                                }
                            });
                    } else {
                        showError('Axios kütüphanesi yüklenemedi. Lütfen sayfayı yenileyin.');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    }
                });
            }

            // Form reset
            if (resetFormBtn) {
                resetFormBtn.addEventListener('click', resetForm);
            }

            // Status switch
            if (categoryStatus) {
                categoryStatus.addEventListener('change', function() {
                    statusLabel.textContent = this.checked ? 'Aktif' : 'Pasif';
                });
            }

            // Image preview
            if (imageInput) {
                imageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.innerHTML = `<img src="${e.target.result}" alt="Önizleme" style="max-width: 100%; max-height: 100px; border-radius: 8px;">`;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Edit category
            document.addEventListener('click', function(e) {
                if (e.target.closest('.edit-category-btn')) {
                    const btn = e.target.closest('.edit-category-btn');
                    const categoryId = btn.dataset.id;
                    editCategory(categoryId);
                }
            });

            // Delete category
            document.addEventListener('click', function(e) {
                if (e.target.closest('.delete-category-btn')) {
                    const btn = e.target.closest('.delete-category-btn');
                    const categoryId = btn.dataset.id;
                    // Her iki sayfa için de direkt sil
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fal fa-spinner fa-spin"></i>';
                    if (typeof axios !== 'undefined') {
                        axios.post("{{ route('categories.deleteAjax') }}", {
                            id: categoryId,
                            _token: document.querySelector('meta[name=\'csrf-token\']').content
                        })
                        .then(res => {
                            if (res.data.success) {
                                showSuccess(res.data.message);
                                removeCategoryFromTable(categoryId);
                                updateCategoryCount();
                                checkEmptyState();
                            }
                        })
                        .catch(err => {
                            showError('Kategori silinirken bir hata oluştu');
                        })
                        .finally(() => {
                            if (btn) {
                                btn.disabled = false;
                                btn.innerHTML = '<i class=\'fal fa-trash\'></i>';
                            }
                        });
                    } else {
                        showError('Axios kütüphanesi yüklenemedi. Lütfen sayfayı yenileyin.');
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = '<i class=\'fal fa-trash\'></i>';
                        }
                    }
                }
            });

            function loadCategories() {
                if (typeof axios !== 'undefined') {
                    axios.get("{{ route('categories.getAjax') }}")
                        .then(res => {
                            const categories = res.data.categories || [];
                            const tbody = document.getElementById('categoriesTableBody');
                            if (tbody) {
                                tbody.innerHTML = '';
                                // stockType'a göre filtrele
                                const filtered = categories.filter(cat => cat.stock_type == (typeof stockType !== 'undefined' ? stockType : {{ isset($stockType) ? $stockType : 0 }}));
                                filtered.forEach(category => {
                                    addCategoryToTable(category);
                                });
                                updateCategoryCount();
                                checkEmptyState();
                            }
                        })
                        .catch(err => {
                            console.error('Kategoriler yüklenirken hata:', err);
                        });
                }
            }

            function resetForm() {
                if (categoryForm) {
                    categoryForm.reset();
                    categoryId.value = '';
                    isEditMode = false;
                    formTitle.textContent = 'Yeni {{ isset($stockType) && $stockType == 1 ? 'Ürün Kategorisi' : 'Hizmet Kategorisi' }} Ekle';
                    submitBtn.innerHTML = '<i class="fal fa-check me-2"></i>{{ isset($stockType) && $stockType == 1 ? 'Ürün Kategorisi Ekle' : 'Hizmet Kategorisi Ekle' }}';
                    statusLabel.textContent = 'Aktif';
                    categoryStatus.checked = true;
                    imagePreview.innerHTML = `
                        <div class="d-flex align-items-center justify-content-center" 
                             style="height: 100px; border: 2px dashed #ddd; border-radius: 8px;">
                            <div class="text-center">
                                <i class="fal fa-cloud-upload fa-2x text-muted mb-2"></i>
                                <div class="small text-muted">Resim seçin veya sürükleyin</div>
                            </div>
                        </div>
                    `;
                    // Clear validation states
                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                }
            }

            function editCategory(id) {
                if (typeof axios !== 'undefined') {
                    axios.get(`{{ route('categories.getAjax') }}/${id}`)
                        .then(res => {
                            const category = res.data.category;

                            isEditMode = true;
                            categoryId.value = category.id;
                            document.getElementById('categoryName').value = category.name;
                            // Açıklama zorunlu değil, boş bırakılabilir
                            document.getElementById('categoryDescription').value = category.description || '';
                            categoryStatus.checked = category.is_active == 1;
                            statusLabel.textContent = category.is_active == 1 ? 'Aktif' : 'Pasif';

                            if (category.image) {
                                imagePreview.innerHTML = `<img src="/uploads/Categories/${category.image}" alt="Mevcut Resim" style="max-width: 100%; max-height: 100px; border-radius: 8px;">`;
                            }

                            formTitle.textContent = 'Kategori Düzenle';
                            submitBtn.innerHTML = '<i class="fal fa-check me-2"></i>Kategori Güncelle';
                        })
                        .catch(err => {
                            showError('Kategori bilgileri alınırken hata oluştu');
                        });
                }
            }

            function addCategoryToTable(category) {
                const tbody = document.getElementById('categoriesTableBody');
                if (tbody) {
                    const row = createCategoryRow(category);
                    tbody.appendChild(row);
                    checkEmptyState();
                }
            }

            function updateCategoryInTable(category) {
                const row = document.querySelector(`tr[data-id="${category.id}"]`);
                if (row) {
                    const newRow = createCategoryRow(category);
                    row.replaceWith(newRow);
                }
            }

            function removeCategoryFromTable(id) {
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) {
                    row.remove();
                }
            }

            function createCategoryRow(category) {
                const tr = document.createElement('tr');
                tr.setAttribute('data-id', category.id);
                tr.innerHTML = `
                    <td>
                        ${category.image ? 
                            `<img src="/uploads/Categories/${category.image}" class="rounded-circle" width="35" height="35" style="object-fit: cover;">` :
                            `<div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                <i class="fal fa-image text-muted"></i>
                            </div>`
                        }
                    </td>
                    <td>
                        <div>
                            <div class="fw-medium">${category.name}</div>
                            <small class="text-muted">${category.description && category.description.length > 30 ? category.description.substring(0, 30) + '...' : (category.description || '')}</small>
                        </div>
                    </td>
                    <td>
                        <span class="badge ${category.is_active == 1 ? 'bg-success' : 'bg-danger'} rounded-pill">
                            ${category.is_active == 1 ? 'Aktif' : 'Pasif'}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary btn-sm edit-category-btn" 
                                    data-id="${category.id}" title="Düzenle">
                                <i class="fal fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm delete-category-btn" 
                                    data-id="${category.id}" title="Sil">
                                <i class="fal fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                return tr;
            }

            function updateCategoryCount() {
                const count = document.querySelectorAll('#categoriesTableBody tr').length;
                const countElement = document.getElementById('categoryCount');
                if (countElement) {
                    countElement.textContent = `${count} {{ isset($stockType) && $stockType == 1 ? 'Ürün Kategorisi' : 'Hizmet Kategorisi' }}`;
                }
            }

            function checkEmptyState() {
                const tbody = document.getElementById('categoriesTableBody');
                const emptyMessage = document.getElementById('emptyCategoriesMessage');

                if (tbody && emptyMessage) {
                    if (tbody.children.length === 0) {
                        emptyMessage.classList.remove('d-none');
                    } else {
                        emptyMessage.classList.add('d-none');
                    }
                }
            }

            const generateBtn = document.getElementById('generateProductCodeBtn');
            const codeInput = document.getElementById('productCodeInput');
            if (generateBtn && codeInput) {
                generateBtn.addEventListener('click', function() {
                    let code = '';
                    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    for (let i = 0; i < 10; i++) {
                        code += chars.charAt(Math.floor(Math.random() * chars.length));
                    }
                    codeInput.value = code;
                });
            }

        } catch (error) {
            console.warn('Categories modal initialization error:', error);
        }
    });
</script> 
