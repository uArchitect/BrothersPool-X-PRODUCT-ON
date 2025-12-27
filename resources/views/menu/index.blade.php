@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Restoran Yönetimi</li>
        <li class="breadcrumb-item active">Menü Yönetimi</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>Menü Yönetimi <span class="fw-300"><i>Ürün Listesi</i></span></h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Küçült"></button>
                        <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Tam Ekran"></button>
                    </div>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        <!-- Filter and Add Button -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kategori Filtresi</label>
                                    <select class="custom-select" id="categoryFilter">
                                        <option value="">Tüm Kategoriler</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ str_repeat('— ', $category->level) }}{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#addMenuItemModal">
                                    <i class="fal fa-plus mr-1"></i> Yeni Menü Öğesi
                                </button>
                            </div>
                        </div>

                        <!-- Menu Items Table -->
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <div class="alert-icon">
                                    <i class="fal fa-info-circle"></i>
                                </div>
                                <div class="flex-1 ml-2">
                                    <span class="h5">Menü Yönetim Paneli</span>
                                    <br>Menü öğelerini ekle, düzenle veya durumlarını yönet.
                                </div>
                            </div>
                        </div>

                        <table id="dt-menu-items" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-highlight">
                                <tr>
                                    <th class="text-center" style="width: 20px;"><i class="fal fa-utensils" title="Menü"></i></th>
                                    <th>Görsel</th>
                                    <th>Ürün Bilgileri</th>
                                    <th>Kategori</th>
                                    <th>Fiyat</th>
                                    <th>Hazırlık Süresi</th>
                                    <th>Durum</th>
                                    <th style="width: 120px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($menu_items as $item)
                                <tr>
                                    <td class="text-center">
                                        <i class="fal fa-utensils text-primary"></i>
                                    </td>
                                    <td>
                                        @if($item->image)
                                            <img src="{{ asset('storage/menu_images/' . $item->image) }}" alt="{{ $item->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fal fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold">{{ $item->name }}</span>
                                            @if($item->description)
                                                <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                            @endif
                                            @if($item->allergens)
                                                <small class="text-warning">
                                                    <i class="fal fa-exclamation-triangle mr-1"></i>
                                                    {{ $item->allergens }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-folder text-info mr-2"></i>
                                            <span>{{ $item->category_name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="font-weight-bold text-primary">
                                                {{ number_format($item->price, 2) }} ₺
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-clock text-warning mr-2"></i>
                                            <span>{{ $item->prep_time ?? '-' }} dk</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $item->is_available ? 'success' : 'danger' }}">
                                            <i class="fal fa-{{ $item->is_available ? 'check' : 'times' }} mr-1"></i>
                                            {{ $item->is_available ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-xs btn-primary" onclick="editMenuItem({{ $item->id }})" title="Düzenle">
                                                <i class="fal fa-edit"></i>
                                            </button>
                                            <button class="btn btn-xs btn-{{ $item->is_available ? 'warning' : 'success' }}" onclick="toggleAvailability({{ $item->id }})" title="{{ $item->is_available ? 'Pasif Yap' : 'Aktif Yap' }}">
                                                <i class="fal fa-{{ $item->is_available ? 'eye-slash' : 'eye' }}"></i>
                                            </button>
                                            <button class="btn btn-xs btn-danger" onclick="deleteMenuItem({{ $item->id }})" title="Sil">
                                                <i class="fal fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $menu_items->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add Menu Item Modal -->
<div class="modal fade" id="addMenuItemModal" tabindex="-1" role="dialog" aria-labelledby="addMenuItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMenuItemModalLabel">Yeni Menü Öğesi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addMenuItemForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Hangi kategoride olsun? <span class="text-danger">*</span></label>
                                <select class="custom-select" name="category_id" required>
                                    <option value="">Kategori seçin</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ str_repeat('— ', $category->level) }}{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Fiyat (₺) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Hazırlık Süresi (dk)</label>
                                <input type="number" class="form-control" name="prep_time" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Ürün Görseli</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Alerjenler</label>
                                <input type="text" class="form-control" name="allergens" placeholder="Örn: Gluten, Süt, Yumurta">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Besin Değerleri</label>
                                <input type="text" class="form-control" name="nutrition_info" placeholder="Örn: 250 kcal, 15g protein">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_available" name="is_available" checked>
                            <label class="custom-control-label" for="is_available">Ürün Aktif</label>
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

<!-- Edit Menu Item Modal -->
<div class="modal fade" id="editMenuItemModal" tabindex="-1" role="dialog" aria-labelledby="editMenuItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMenuItemModalLabel">Menü Öğesi Düzenle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editMenuItemForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="menu_item_id" id="edit_menu_item_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="edit_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Hangi kategoride olsun? <span class="text-danger">*</span></label>
                                <select class="custom-select" name="category_id" id="edit_category_id" required>
                                    <option value="">Kategori seçin</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ str_repeat('— ', $category->level) }}{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Fiyat (₺) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="edit_price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Hazırlık Süresi (dk)</label>
                                <input type="number" class="form-control" name="prep_time" id="edit_prep_time" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Ürün Görseli</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                                <small class="form-text text-muted">Mevcut görseli değiştirmek için yeni dosya seçin</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Alerjenler</label>
                                <input type="text" class="form-control" name="allergens" id="edit_allergens" placeholder="Örn: Gluten, Süt, Yumurta">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Besin Değerleri</label>
                                <input type="text" class="form-control" name="nutrition_info" id="edit_nutrition_info" placeholder="Örn: 250 kcal, 15g protein">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit_is_available" name="is_available">
                            <label class="custom-control-label" for="edit_is_available">Ürün Aktif</label>
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

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#dt-menu-items').DataTable({
        responsive: true,
        stateSave: true,
        pageLength: 25,
        order: [[2, 'asc']], // Sort by product name
        language: { url: "{{ asset('media/data/tr.json') }}" },
        columnDefs: [
            { orderable: false, targets: [0, 7] }, // Icon and actions columns
            { searchable: false, targets: [0, 7] }
        ]
    });

    // Category filter
    $('#categoryFilter').on('change', function() {
        var categoryId = $(this).val();
        $('#dt-menu-items').DataTable().column(3).search(categoryId).draw();
    });

    // Add menu item form
    $('#addMenuItemForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('is_available', $('#is_available').is(':checked') ? '1' : '0');
        
        $.ajax({
            url: '{{ route("menu.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message);
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

    // Edit menu item form
    $('#editMenuItemForm').on('submit', function(e) {
        e.preventDefault();
        
        var menuItemId = $('#edit_menu_item_id').val();
        var formData = new FormData(this);
        formData.append('is_available', $('#edit_is_available').is(':checked') ? '1' : '0');
        
        $.ajax({
            url: '/menu/' + menuItemId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message);
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

function editMenuItem(id) {
    $.ajax({
        url: '/menu/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var item = response.data;
                $('#edit_menu_item_id').val(item.id);
                $('#edit_name').val(item.name);
                $('#edit_category_id').val(item.category_id);
                $('#edit_description').val(item.description);
                $('#edit_price').val(item.price);
                $('#edit_prep_time').val(item.prep_time);
                $('#edit_allergens').val(item.allergens);
                $('#edit_nutrition_info').val(item.nutrition_info);
                $('#edit_is_available').prop('checked', item.is_available == 1);
                
                $('#editMenuItemModal').modal('show');
            } else {
                showError(response.message);
            }
        },
        error: function() {
            showError('Menü öğesi bilgileri alınamadı.');
        }
    });
}

function toggleAvailability(id) {
    $.ajax({
        url: '/menu/' + id + '/toggle-availability',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showSuccess(response.message);


                // Update the button and badge in the table row 
                const row = $(`button[onclick='toggleAvailability(${id})']`).closest('tr');
                const badge = row.find('.badge');
                const button = row.find(`button[onclick='toggleAvailability(${id})']`);
                const icon = button.find('i');
                
                if (response.is_available) {
                    badge.removeClass('badge-danger').addClass('badge-success').text('Aktif');
                    button.removeClass('btn-success').addClass('btn-warning').attr('title', 'Pasif Yap');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    badge.removeClass('badge-success').addClass('badge-danger').text('Pasif');
                    button.removeClass('btn-warning').addClass('btn-success').attr('title', 'Aktif Yap');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
                
                // Redraw only the specific row in DataTables
                if (typeof window.menuTable !== 'undefined') {
                    const rowIndex = window.menuTable.row(row).index();
                    window.menuTable.row(rowIndex).draw(false);
                }
            } else {
                showError(response.message);
            }
        },
        error: function() {
            showError('Durum güncellenirken hata oluştu.');
        }
    });
}

function deleteMenuItem(id) {
    if (confirm('Bu menü öğesini silmek istediğinizden emin misiniz?')) {
        $.ajax({
            url: '/menu/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message);
                    // Remove the row from the table
                    const row = $(`button[onclick='deleteMenuItem(${id})']`).closest('tr');
                    row.fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    showError(response.message);
                }
            },
            error: function() {
                showError('Menü öğesi silinirken hata oluştu.');
            }
        });
    }
}
</script>
