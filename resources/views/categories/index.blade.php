@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Restoran Yönetimi</li>
        <li class="breadcrumb-item active">Kategori Yönetimi</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>Menü Kategorileri <span class="fw-300"><i>Yemek kategorilerini düzenle</i></span></h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Küçült"></button>
                        <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Tam Ekran"></button>
                    </div>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        <!-- Add Button and Search -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kategori Ara</label>
                                    <input type="text" class="form-control" id="categorySearch" placeholder="Kategori adı ara...">
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                                    <i class="fal fa-plus mr-1"></i> Yeni Kategori Ekle
                                </button>
                            </div>
                        </div>

                        <!-- Categories Tree -->
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <div class="alert-icon">
                                    <i class="fal fa-info-circle"></i>
                                </div>
                                <div class="flex-1 ml-2">
                                    <span class="h5">Kategori Yönetim Paneli</span>
                                    <br>Kategori ekle, düzenle veya hiyerarşisini yönet.
                                </div>
                            </div>
                        </div>

                        <table id="dt-categories" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-highlight">
                                <tr>
                                    <th class="text-center" style="width: 20px;"><i class="fal fa-grip-vertical" title="Sıralama"></i></th>
                                    <th>Görsel</th>
                                    <th>Kategori Adı</th>
                                    <th>Konum</th>
                                    <th>Açıklama</th>
                                    <th>Durum</th>
                                    <th style="width: 120px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-categories">
                                @foreach($categories as $category)
                                <tr data-level="{{ $category->level }}" data-parent="{{ $category->parent_id }}" data-id="{{ $category->id }}" data-sort="{{ $category->sort_order ?? 0 }}">
                                    <td class="text-center sort-handle" style="cursor: move;">
                                        <i class="fal fa-grip-vertical text-muted"></i>
                                    </td>
                                    <td>
                                        @if($category->image)
                                            <img src="{{ asset('storage/category_images/' . $category->image) }}" alt="{{ $category->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fal fa-folder text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="padding-left: {{ $category->level * 20 }}px;">
                                            @if($category->level > 0)
                                                <i class="fal fa-level-down-alt text-muted mr-1"></i>
                                            @endif
                                            <strong>{{ $category->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @if($category->level == 0)
                                            <span class="badge badge-primary">
                                                <i class="fal fa-folder mr-1"></i>Ana Kategori
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fal fa-folder-open mr-1"></i>Alt Kategori
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{{ Str::limit($category->description, 50) }}</span>
                                            @if($category->created_at)
                                                <small class="text-muted">
                                                    <i class="fal fa-clock mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($category->created_at)->diffForHumans() }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $category->is_active ? 'success' : 'danger' }}">
                                            <i class="fal fa-{{ $category->is_active ? 'check' : 'times' }} mr-1"></i>
                                            {{ $category->is_active ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-xs btn-success" onclick="addSubCategory({{ $category->id }})" title="Bu kategorinin altına yeni kategori ekle">
                                                <i class="fal fa-plus"></i>
                                            </button>
                                            <button class="btn btn-xs btn-primary" onclick="editCategory({{ $category->id }})" title="Kategoriyi düzenle">
                                                <i class="fal fa-edit"></i>
                                            </button>
                                            <button class="btn btn-xs btn-{{ $category->is_active ? 'warning' : 'success' }}" onclick="toggleCategoryStatus({{ $category->id }})" title="{{ $category->is_active ? 'Kategoriyi kapat' : 'Kategoriyi aç' }}">
                                                <i class="fal fa-{{ $category->is_active ? 'eye-slash' : 'eye' }}"></i>
                                            </button>
                                            <button class="btn btn-xs btn-danger" onclick="deleteCategory({{ $category->id }})" title="Kategoriyi sil">
                                                <i class="fal fa-trash"></i>
                                            </button>
                                        </div>
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
</main>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Yeni Kategori Ekle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addCategoryForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Kategori Adı <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Hangi kategorinin altında olsun?</label>
                                <select class="custom-select" name="parent_id" id="parent_id">
                                    <option value="">Ana kategori olarak ekle</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ str_repeat('— ', $category->level) }}{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Kategori Görseli</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" checked>
                            <label class="custom-control-label" for="is_active">Kategoriyi kullanıma aç</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Kategoriyi Düzenle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editCategoryForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="category_id" id="edit_category_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Kategori Adı <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="edit_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Hangi kategorinin altında olsun?</label>
                                <select class="custom-select" name="parent_id" id="edit_parent_id">
                                    <option value="">Ana kategori olarak ayarla</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ str_repeat('— ', $category->level) }}{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Kategori Görseli</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <small class="form-text text-muted">Mevcut görseli değiştirmek için yeni dosya seçin</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit_is_active" name="is_active">
                            <label class="custom-control-label" for="edit_is_active">Kategoriyi kullanıma aç</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('layouts.footer')

<script defer src="{{ asset('js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
.sortable-ghost {
    opacity: 0.4;
    background-color: #f8f9fa;
}

.sortable-chosen {
    background-color: #e3f2fd;
}

.sortable-drag {
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.sort-handle:hover {
    background-color: #f8f9fa;
    border-radius: 3px;
}

#sortable-categories tr {
    transition: all 0.2s ease;
}

#sortable-categories tr:hover {
    background-color: #f8f9fa;
}
</style>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#dt-categories').DataTable({
        responsive: true,
        stateSave: true,
        pageLength: 25,
        order: [[2, 'asc']], // Sort by category name
        language: { url: "{{ asset('media/data/tr.json') }}" },
        columnDefs: [
            { orderable: false, targets: [0, 6] }, // Icon and actions columns
            { searchable: false, targets: [0, 6] }
        ]
    });

    // Initialize SortableJS
    initializeSortable();
    
    // Search functionality
    $('#categorySearch').on('keyup', function() {
        $('#dt-categories').DataTable().search(this.value).draw();
    });

    // Add category form
    $('#addCategoryForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('is_active', $('#is_active').is(':checked') ? '1' : '0');
        
        $.ajax({
            url: '{{ route("categories.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message);
                    $('#addCategoryModal').modal('hide');
                    $('#addCategoryForm')[0].reset();
                    location.reload(); // Reload to show new category
                } else {
                    showError(response.message);
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON?.errors;
                if (errors) {
                    var errorMessage = Object.values(errors).flat().join('\n');
                    showError(errorMessage);
                } else {
                    showError('Bir hata oluştu.');
                }
            }
        });
    });

    // Edit category form
    $('#editCategoryForm').on('submit', function(e) {
        e.preventDefault();
        
        var categoryId = $('#edit_category_id').val();
        var formData = new FormData(this);
        formData.append('is_active', $('#edit_is_active').is(':checked') ? '1' : '0');
        
        $.ajax({
            url: '/categories/' + categoryId,
            type: 'PUT',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message);
                    $('#editCategoryModal').modal('hide');
                    location.reload(); // Reload to show updated category
                } else {
                    showError(response.message);
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON?.errors;
                if (errors) {
                    var errorMessage = Object.values(errors).flat().join('\n');
                    showError(errorMessage);
                } else {
                    showError('Bir hata oluştu.');
                }
            }
        });
    });
});

function initializeSortable() {
    // Initialize SortableJS for categories
    var sortable = Sortable.create(document.getElementById('sortable-categories'), {
        handle: '.sort-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        onEnd: function(evt) {
            updateCategoryOrder();
        }
    });
}

function updateCategoryOrder() {
    var categories = [];
    $('#sortable-categories tr').each(function(index) {
        var categoryId = $(this).data('id');
        var level = $(this).data('level');
        var parentId = $(this).data('parent');
        
        categories.push({
            id: categoryId,
            sort_order: index,
            level: level,
            parent_id: parentId
        });
    });
    
    // Send update request
    $.ajax({
        url: '/categories/update-sort-order',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            categories: categories
        },
        success: function(response) {
            if (response.success) {
                showSuccess('Sıralama güncellendi');
            } else {
                showError('Sıralama güncellenirken hata oluştu');
            }
        },
        error: function() {
            showError('Sıralama güncellenirken hata oluştu');
        }
    });
}

function editCategory(id) {
    $.ajax({
        url: '/categories/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var category = response.data;
                $('#edit_category_id').val(category.id);
                $('#edit_name').val(category.name);
                $('#edit_parent_id').val(category.parent_id || '');
                $('#edit_description').val(category.description);
                $('#edit_is_active').prop('checked', category.is_active == 1);
                
                $('#editCategoryModal').modal('show');
            } else {
                showError(response.message);
            }
        },
        error: function() {
            showError('Kategori bilgileri alınamadı.');
        }
    });
}

function addSubCategory(parentId) {
    $('#parent_id').val(parentId);
    $('#addCategoryModal').modal('show');
}

function toggleCategoryStatus(id) {
    $.ajax({
        url: '/categories/' + id + '/toggle-status',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showSuccess(response.message);
                // Update the button and badge in the table row
                const row = $(`button[onclick='toggleCategoryStatus(${id})']`).closest('tr');
                const badge = row.find('.badge');
                const button = row.find(`button[onclick='toggleCategoryStatus(${id})']`);
                const icon = button.find('i');
                
                if (response.is_active) {
                    badge.removeClass('badge-danger').addClass('badge-success').text('Aktif');
                    button.removeClass('btn-success').addClass('btn-warning').attr('title', 'Pasif Yap');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    badge.removeClass('badge-success').addClass('badge-danger').text('Pasif');
                    button.removeClass('btn-warning').addClass('btn-success').attr('title', 'Aktif Yap');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
                
                // Reload page to show updated tree structure
                location.reload();
            } else {
                showError(response.message);
            }
        },
        error: function() {
            showError('Durum güncellenirken hata oluştu.');
        }
    });
}

function deleteCategory(id) {
    if (confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')) {
        $.ajax({
            url: '/categories/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message);
                    location.reload(); // Reload to show updated tree structure
                } else {
                    showError(response.message);
                }
            },
            error: function() {
                showError('Kategori silinirken hata oluştu.');
            }
        });
    }
}
</script>
