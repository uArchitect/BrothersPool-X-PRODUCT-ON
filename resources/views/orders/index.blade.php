@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Restoran Yönetimi</li>
        <li class="breadcrumb-item active">Sipariş Yönetimi</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>Sipariş Yönetimi <span class="fw-300"><i>Aktif Siparişler</i></span></h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Küçült"></button>
                        <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Tam Ekran"></button>
                    </div>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        <!-- Filter and Add Button -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Durum Filtresi</label>
                                    <select class="custom-select" id="statusFilter">
                                        <option value="">Tüm Durumlar</option>
                                        <option value="pending">Bekliyor</option>
                                        <option value="preparing">Hazırlanıyor</option>
                                        <option value="ready">Hazır</option>
                                        <option value="served">Servis Edildi</option>
                                        <option value="completed">Tamamlandı</option>
                                        <option value="cancelled">İptal Edildi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Masa Filtresi</label>
                                    <select class="custom-select" id="tableFilter">
                                        <option value="">Tüm Masalar</option>
                                        @foreach($tables as $table)
                                            <option value="{{ $table->id }}">Masa {{ $table->table_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#addOrderModal">
                                    <i class="fal fa-plus mr-1"></i> Yeni Sipariş
                                </button>
                            </div>
                        </div>

                        <!-- Orders Table -->
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <div class="alert-icon">
                                    <i class="fal fa-info-circle"></i>
                                </div>
                                <div class="flex-1 ml-2">
                                    <span class="h5">Sipariş Yönetim Paneli</span>
                                    <br>Sipariş ekle, düzenle veya durumlarını yönet.
                                </div>
                            </div>
                        </div>

                        <table id="dt-orders" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-highlight">
                                <tr>
                                    <th class="text-center" style="width: 20px;"><i class="fal fa-shopping-cart" title="Sipariş"></i></th>
                                    <th>Sipariş Bilgileri</th>
                                    <th>Masa</th>
                                    <th>Garson</th>
                                    <th>Müşteri</th>
                                    <th>Tutar</th>
                                    <th>Durum</th>
                                    <th>Sipariş Zamanı</th>
                                    <th style="width: 120px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td class="text-center">
                                        <i class="fal fa-shopping-cart text-primary"></i>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold">#{{ $order->id }}</span>
                                            <small class="text-muted">Sipariş No</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-table text-info mr-2"></i>
                                            <span>Masa {{ $order->table_number }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fal fa-user text-success mr-2"></i>
                                            <span>{{ $order->waiter_name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold">{{ $order->customer_name ?? '-' }}</span>
                                            @if($order->customer_phone)
                                                <small class="text-muted">{{ $order->customer_phone }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="font-weight-bold text-primary">
                                                {{ number_format($order->total_amount, 2) }} ₺
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'preparing' => 'info',
                                                'ready' => 'success',
                                                'served' => 'primary',
                                                'completed' => 'secondary',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusTexts = [
                                                'pending' => 'Bekliyor',
                                                'preparing' => 'Hazırlanıyor',
                                                'ready' => 'Hazır',
                                                'served' => 'Servis Edildi',
                                                'completed' => 'Tamamlandı',
                                                'cancelled' => 'İptal Edildi'
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                            <i class="fal fa-{{ $order->status === 'pending' ? 'clock' : ($order->status === 'preparing' ? 'cog' : ($order->status === 'ready' ? 'check' : ($order->status === 'served' ? 'truck' : ($order->status === 'completed' ? 'check-circle' : 'times')))) }} mr-1"></i>
                                            {{ $statusTexts[$order->status] ?? $order->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold">{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}</span>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->format('d.m.Y') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-xs btn-info" onclick="viewOrder({{ $order->id }})" title="Detay">
                                                <i class="fal fa-eye"></i>
                                            </button>
                                            <button class="btn btn-xs btn-success" onclick="updateOrderStatus({{ $order->id }})" title="Durum Güncelle">
                                                <i class="fal fa-edit"></i>
                                            </button>
                                            @if(in_array($order->status, ['pending', 'cancelled']))
                                                <button class="btn btn-xs btn-danger" onclick="deleteOrder({{ $order->id }})" title="Sil">
                                                    <i class="fal fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination - Disabled for fake data -->
                        {{-- <div class="d-flex justify-content-center">
                            {{ $orders->links() }}
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add Order Modal -->
<div class="modal fade" id="addOrderModal" tabindex="-1" role="dialog" aria-labelledby="addOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOrderModalLabel">Yeni Sipariş</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addOrderForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Masa <span class="text-danger">*</span></label>
                                <select class="custom-select" name="table_id" id="order_table_id" required>
                                    <option value="">Masa Seçin</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}">Masa {{ $table->table_number }} ({{ $table->capacity }} kişi)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Garson <span class="text-danger">*</span></label>
                                <select class="custom-select" name="waiter_id" required>
                                    <option value="">Garson Seçin</option>
                                    @foreach($waiters as $waiter)
                                        <option value="{{ $waiter->id }}">{{ $waiter->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Sipariş Türü <span class="text-danger">*</span></label>
                                <select class="custom-select" name="order_type" required>
                                    <option value="dine_in">Restoranda Yeme</option>
                                    <option value="takeaway">Paket Servis</option>
                                    <option value="delivery">Teslimat</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Müşteri Adı</label>
                                <input type="text" class="form-control" name="customer_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Müşteri Telefonu</label>
                                <input type="text" class="form-control" name="customer_phone">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Sipariş Notları</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>

                    <!-- Menu Items Selection -->
                    <div class="form-group">
                        <label class="form-label">Menü Öğeleri <span class="text-danger">*</span></label>
                        <div id="menuItemsContainer">
                            <div class="menu-item-row row mb-2">
                                <div class="col-md-4">
                                    <select class="custom-select menu-item-select" name="order_items[0][menu_item_id]" required>
                                        <option value="">Menü Öğesi Seçin</option>
                                        <!-- Menu items will be loaded via AJAX -->
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control" name="order_items[0][quantity]" min="1" value="1" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control menu-item-price" name="order_items[0][price]" step="0.01" min="0" required readonly>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="order_items[0][notes]" placeholder="Notlar">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm remove-menu-item" style="display: none;">
                                        <i class="fal fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addMenuItem">
                            <i class="fal fa-plus"></i> Menü Öğesi Ekle
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Toplam Tutar</label>
                                <input type="text" class="form-control" id="totalAmount" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Sipariş Oluştur</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Order Modal -->
<div class="modal fade" id="viewOrderModal" tabindex="-1" role="dialog" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewOrderModalLabel">Sipariş Detayı</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="orderDetails">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Sipariş Durumu Güncelle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="updateStatusForm">
                @csrf
                <input type="hidden" name="order_id" id="status_order_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Yeni Durum</label>
                        <select class="custom-select" name="status" id="new_status" required>
                            <option value="pending">Bekliyor</option>
                            <option value="preparing">Hazırlanıyor</option>
                            <option value="ready">Hazır</option>
                            <option value="served">Servis Edildi</option>
                            <option value="completed">Tamamlandı</option>
                            <option value="cancelled">İptal Edildi</option>
                        </select>
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
    $('#dt-orders').DataTable({
        responsive: true,
        stateSave: true,
        pageLength: 25,
        order: [[7, 'desc']], // Sort by order time descending
        language: { url: "{{ asset('media/data/tr.json') }}" },
        columnDefs: [
            { orderable: false, targets: [0, 8] }, // Icon and actions columns
            { searchable: false, targets: [0, 8] }
        ]
    });

    // Status filter
    $('#statusFilter').on('change', function() {
        var status = $(this).val();
        $('#dt-orders').DataTable().column(6).search(status).draw();
    });

    // Table filter
    $('#tableFilter').on('change', function() {
        var tableId = $(this).val();
        $('#dt-orders').DataTable().column(2).search(tableId).draw();
    });

    // Load menu items when modal opens
    $('#addOrderModal').on('show.bs.modal', function() {
        loadMenuItems();
    });

    // Add menu item
    $('#addMenuItem').on('click', function() {
        var index = $('.menu-item-row').length;
        var newRow = `
            <div class="menu-item-row row mb-2">
                <div class="col-md-4">
                    <select class="form-control menu-item-select" name="order_items[${index}][menu_item_id]" required>
                        <option value="">Menü Öğesi Seçin</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="order_items[${index}][quantity]" min="1" value="1" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control menu-item-price" name="order_items[${index}][price]" step="0.01" min="0" required readonly>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="order_items[${index}][notes]" placeholder="Notlar">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-menu-item">
                        <i class="fal fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#menuItemsContainer').append(newRow);
        loadMenuItems();
        updateRemoveButtons();
    });

    // Remove menu item
    $(document).on('click', '.remove-menu-item', function() {
        $(this).closest('.menu-item-row').remove();
        updateRemoveButtons();
        calculateTotal();
    });

    // Menu item selection change
    $(document).on('change', '.menu-item-select', function() {
        var price = $(this).find('option:selected').data('price');
        $(this).closest('.menu-item-row').find('.menu-item-price').val(price || 0);
        calculateTotal();
    });

    // Quantity change
    $(document).on('input', 'input[name*="[quantity]"]', function() {
        calculateTotal();
    });

    // Add order form
    $('#addOrderForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("orders.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                        showSuccess(response.message);
                    $('#addOrderModal').modal('hide');
                    $('#addOrderForm')[0].reset();
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

    // Update status form
    $('#updateStatusForm').on('submit', function(e) {
        e.preventDefault();
        
        var orderId = $('#status_order_id').val();
        var status = $('#new_status').val();
        
        $.ajax({
            url: '/orders/' + orderId + '/update-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message);
                    // Update the status badge in the table row
                    const row = $(`button[onclick='updateOrderStatus(${orderId})']`).closest('tr');
                    const badge = row.find('.badge');
                    const statusTexts = {
                        'pending': 'Bekliyor',
                        'preparing': 'Hazırlanıyor',
                        'ready': 'Hazır',
                        'served': 'Servis Edildi',
                        'completed': 'Tamamlandı',
                        'cancelled': 'İptal Edildi'
                    };
                    const statusColors = {
                        'pending': 'warning',
                        'preparing': 'info',
                        'ready': 'success',
                        'served': 'primary',
                        'completed': 'secondary',
                        'cancelled': 'danger'
                    };
                    
                    badge.removeClass().addClass(`badge badge-${statusColors[status]}`).text(statusTexts[status]);
                    
                    // Redraw only the specific row in DataTables
                    if (typeof window.ordersTable !== 'undefined') {
                        const rowIndex = window.ordersTable.row(row).index();
                        window.ordersTable.row(rowIndex).draw(false);
                    }
                    
                    $('#updateStatusModal').modal('hide');
                } else {
                    showError(response.message);
                }
            },
            error: function() {
                showError('Durum güncellenirken hata oluştu.');
            }
        });
    });
});

function loadMenuItems() {
    $.ajax({
        url: '/menu/api/items?available_only=1',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var options = '<option value="">Menü Öğesi Seçin</option>';
                response.data.forEach(function(item) {
                    options += `<option value="${item.id}" data-price="${item.price}">${item.name} - ${item.price} ₺</option>`;
                });
                $('.menu-item-select').html(options);
            }
        }
    });
}

function updateRemoveButtons() {
    $('.remove-menu-item').each(function() {
        if ($('.menu-item-row').length > 1) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

function calculateTotal() {
    var total = 0;
    $('.menu-item-row').each(function() {
        var quantity = parseFloat($(this).find('input[name*="[quantity]"]').val()) || 0;
        var price = parseFloat($(this).find('.menu-item-price').val()) || 0;
        total += quantity * price;
    });
    $('#totalAmount').val(total.toFixed(2) + ' ₺');
}

function getOrderSourceText(source) {
    const sourceTexts = {
        'dine_in': 'Restoranda Yeme',
        'takeaway': 'Paket Servis',
        'delivery': 'Teslimat',
        'online': 'Online Sipariş',
        'phone': 'Telefon Siparişi',
        'walk_in': 'Gelip Al',
        'reservation': 'Rezervasyon'
    };
    return sourceTexts[source] || source || '-';
}

function viewOrder(id) {
    $.ajax({
        url: '/orders/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var order = response.data;
                var html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Sipariş Bilgileri</h6>
                            <p><strong>Sipariş No:</strong> #${order.id}</p>
                            <p><strong>Masa:</strong> ${order.table_number}</p>
                            <p><strong>Garson:</strong> ${order.waiter_name}</p>
                            <p><strong>Durum:</strong> <span class="badge badge-primary">${order.status}</span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Müşteri Bilgileri</h6>
                            <p><strong>Ad:</strong> ${order.customer_name || '-'}</p>
                            <p><strong>Telefon:</strong> ${order.customer_phone || '-'}</p>
                            <p><strong>Sipariş Türü:</strong> ${getOrderSourceText(order.order_source)}</p>
                        </div>
                    </div>
                    <hr>
                    <h6>Sipariş Öğeleri</h6>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Ürün</th>
                                <th>Miktar</th>
                                <th>Fiyat</th>
                                <th>Toplam</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                order.items.forEach(function(item) {
                    const unitPrice = parseFloat(item.unit_price) || 0;
                    const quantity = parseInt(item.quantity) || 0;
                    const totalPrice = unitPrice * quantity;
                    
                    html += `
                        <tr>
                            <td>${item.item_name || '-'}</td>
                            <td>${quantity}</td>
                            <td>${unitPrice.toFixed(2)} ₺</td>
                            <td>${totalPrice.toFixed(2)} ₺</td>
                        </tr>
                    `;
                });
                
                html += `
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Toplam</th>
                                <th>${order.total_amount} ₺</th>
                            </tr>
                        </tfoot>
                    </table>
                `;
                
                $('#orderDetails').html(html);
                $('#viewOrderModal').modal('show');
            } else {
                showError(response.message);
            }
        },
        error: function() {
            showError('Sipariş bilgileri alınamadı.');
        }
    });
}

function updateOrderStatus(id) {
    $('#status_order_id').val(id);
    $('#updateStatusModal').modal('show');
}

function deleteOrder(id) {
    if (confirm('Bu siparişi silmek istediğinizden emin misiniz?')) {
        $.ajax({
            url: '/orders/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message);
                    // Remove the row from the table
                    const row = $(`button[onclick='deleteOrder(${id})']`).closest('tr');
                    row.fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    showError(response.message);
                }
            },
            error: function() {
                showError('Sipariş silinirken hata oluştu.');
            }
        });
    }
}
</script>
