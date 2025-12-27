@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Restoran Yönetimi</li>
        <li class="breadcrumb-item active">Masa Yönetimi</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>Masa Yönetimi <span class="fw-300"><i>Restoran Masaları</i></span></h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Küçült"></button>
                        <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Tam Ekran"></button>
                    </div>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        <!-- Add Button -->
                        <div class="row mb-3">
                            <div class="col-md-12 text-right">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#addTableModal">
                                    <i class="fal fa-plus mr-1"></i> Yeni Masa
                                </button>
                            </div>
                        </div>

                        <!-- Tables Grid -->
                        <div class="row" id="tablesGrid">
                            @foreach($tables as $table)
                                <div class="col-md-3 mb-3">
                                    <div class="card border-left-primary shadow h-100" data-table-id="{{ $table->id }}">
                                        <div class="card-body text-center">
                                            <div class="mb-2">
                                                @if($table->status == 'available')
                                                    <i class="fal fa-circle text-success fa-2x"></i>
                                                @elseif($table->status == 'occupied')
                                                    <i class="fal fa-circle text-danger fa-2x"></i>
                                                @elseif($table->status == 'reserved')
                                                    <i class="fal fa-circle text-warning fa-2x"></i>
                                                @else
                                                    <i class="fal fa-circle text-secondary fa-2x"></i>
                                                @endif
                                            </div>
                                            <h5 class="card-title">Masa {{ $table->table_number }}</h5>
                                            <p class="card-text">
                                                <small class="text-muted">Kapasite: {{ $table->capacity }} kişi</small><br>
                                                @if($table->location)
                                                    <small class="text-muted">{{ $table->location }}</small>
                                                @endif
                                            </p>
                                            <div class="mt-3">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-primary" onclick="editTable({{ $table->id }})" title="Düzenle">
                                                        <i class="fal fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-{{ $table->is_active ? 'warning' : 'success' }}" onclick="toggleTableStatus({{ $table->id }})" title="{{ $table->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                                        <i class="fal fa-{{ $table->is_active ? 'eye-slash' : 'eye' }}"></i>
                                                    </button>
                                                    <button class="btn btn-danger" onclick="deleteTable({{ $table->id }})" title="Sil">
                                                        <i class="fal fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add Table Modal -->
<div class="modal fade" id="addTableModal" tabindex="-1" role="dialog" aria-labelledby="addTableModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTableModalLabel">Yeni Masa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addTableForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Masa Numarası <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="table_number" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Kapasite <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="capacity" min="1" max="20" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Konum</label>
                        <input type="text" class="form-control" name="location" placeholder="Örn: Salon, Teras, VIP">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" checked>
                            <label class="custom-control-label" for="is_active">Masa Aktif</label>
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

<!-- Edit Table Modal -->
<div class="modal fade" id="editTableModal" tabindex="-1" role="dialog" aria-labelledby="editTableModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTableModalLabel">Masa Düzenle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editTableForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="table_id" id="edit_table_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Masa Numarası <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="table_number" id="edit_table_number" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Kapasite <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="capacity" id="edit_capacity" min="1" max="20" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Konum</label>
                        <input type="text" class="form-control" name="location" id="edit_location" placeholder="Örn: Salon, Teras, VIP">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Masa Durumu</label>
                        <select class="custom-select" name="status" id="edit_status">
                            <option value="available">Müsait</option>
                            <option value="occupied">Dolu</option>
                            <option value="reserved">Rezerve</option>
                            <option value="maintenance">Bakım</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit_is_active" name="is_active">
                            <label class="custom-control-label" for="edit_is_active">Masa Aktif</label>
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

<script>
$(document).ready(function() {
    // Add table form
    $('#addTableForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('is_active', $('#is_active').is(':checked') ? '1' : '0');
        
        $.ajax({
            url: '{{ route("tables.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message);
                    // Add new table to the grid
                    addTableToGrid(response.data);
                    $('#addTableModal').modal('hide');
                    $('#addTableForm')[0].reset();
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

    // Edit table form
    $('#editTableForm').on('submit', function(e) {
        e.preventDefault();
        
        var tableId = $('#edit_table_id').val();
        var formData = new FormData(this);
        formData.append('is_active', $('#edit_is_active').is(':checked') ? '1' : '0');
        
        $.ajax({
            url: '/tables/' + tableId,
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
                    // Update the table in the grid
                    updateTableInGrid(response.data);
                    $('#editTableModal').modal('hide');
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

function editTable(id) {
    $.ajax({
        url: '/tables/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var table = response.data;
                $('#edit_table_id').val(table.id);
                $('#edit_table_number').val(table.table_number);
                $('#edit_capacity').val(table.capacity);
                $('#edit_location').val(table.location);
                $('#edit_description').val(table.description);
                $('#edit_status').val(table.status);
                $('#edit_is_active').prop('checked', table.is_active == 1);
                
                $('#editTableModal').modal('show');
            } else {
                showError(response.message);
            }
        },
        error: function() {
            showError('Masa bilgileri alınamadı.');
        }
    });
}

function toggleTableStatus(id) {
    $.ajax({
        url: '/tables/' + id + '/toggle-status',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showSuccess(response.message);
                // Update the button in the table card
                const card = $(`[data-table-id="${id}"]`);
                const button = card.find(`button[onclick='toggleTableStatus(${id})']`);
                const icon = button.find('i');
                
                if (response.is_active) {
                    button.removeClass('btn-success').addClass('btn-warning').attr('title', 'Pasif Yap');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    button.removeClass('btn-warning').addClass('btn-success').attr('title', 'Aktif Yap');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
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

function deleteTable(id) {
    if (confirm('Bu masayı silmek istediğinizden emin misiniz?')) {
        $.ajax({
            url: '/tables/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message);
                    // Remove the table card from the grid
                    const card = $(`[data-table-id="${id}"]`).closest('.col-md-3');
                    card.fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    showError(response.message);
                }
            },
            error: function() {
                showError('Masa silinirken hata oluştu.');
            }
        });
    }
}

// Helper functions for updating the table grid
function addTableToGrid(table) {
    const statusIcon = getStatusIcon(table.status);
    const statusColor = getStatusColor(table.status);
    const isActive = table.is_active == 1;
    
    const tableCard = `
        <div class="col-md-3 mb-3">
            <div class="card border-left-primary shadow h-100" data-table-id="${table.id}">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fal fa-circle ${statusColor} fa-2x"></i>
                    </div>
                    <h5 class="card-title">Masa ${table.table_number}</h5>
                    <p class="card-text">
                        <small class="text-muted">Kapasite: ${table.capacity} kişi</small><br>
                        ${table.location ? `<small class="text-muted">${table.location}</small>` : ''}
                    </p>
                    <div class="mt-3">
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-primary" onclick="editTable(${table.id})" title="Düzenle">
                                <i class="fal fa-edit"></i>
                            </button>
                            <button class="btn btn-${isActive ? 'warning' : 'success'}" onclick="toggleTableStatus(${table.id})" title="${isActive ? 'Pasif Yap' : 'Aktif Yap'}">
                                <i class="fal fa-${isActive ? 'eye-slash' : 'eye'}"></i>
                            </button>
                            <button class="btn btn-danger" onclick="deleteTable(${table.id})" title="Sil">
                                <i class="fal fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#tablesGrid').append(tableCard);
}

function updateTableInGrid(table) {
    const card = $(`[data-table-id="${table.id}"]`);
    const statusIcon = getStatusIcon(table.status);
    const statusColor = getStatusColor(table.status);
    const isActive = table.is_active == 1;
    
    // Update the card content
    card.find('.card-title').text(`Masa ${table.table_number}`);
    card.find('.card-text small').first().text(`Kapasite: ${table.capacity} kişi`);
    card.find('.fal.fa-circle').removeClass().addClass(`fal fa-circle ${statusColor} fa-2x`);
    
    // Update the toggle button
    const button = card.find(`button[onclick='toggleTableStatus(${table.id})']`);
    const icon = button.find('i');
    
    if (isActive) {
        button.removeClass('btn-success').addClass('btn-warning').attr('title', 'Pasif Yap');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        button.removeClass('btn-warning').addClass('btn-success').attr('title', 'Aktif Yap');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
}

function getStatusIcon(status) {
    switch(status) {
        case 'available': return 'fa-circle text-success';
        case 'occupied': return 'fa-circle text-danger';
        case 'reserved': return 'fa-circle text-warning';
        default: return 'fa-circle text-secondary';
    }
}

function getStatusColor(status) {
    switch(status) {
        case 'available': return 'text-success';
        case 'occupied': return 'text-danger';
        case 'reserved': return 'text-warning';
        default: return 'text-secondary';
    }
}
</script>

