@include('layouts.header')
@include('layouts.module.warehouse_add_modal')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Depo Yönetimi</li>
        <li class="breadcrumb-item active">Depo Listesi</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    @if(session('success'))
        <script>window.addEventListener('DOMContentLoaded', () => showSuccess('{{ session('success') }}'));</script>
    @endif

    @if(session('error'))
        <script>window.addEventListener('DOMContentLoaded', () => showError('{{ session('error') }}'));</script>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="panel" id="panel-1">
                <div class="panel-hdr">
                    <h2>Depo Yönetimi</h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-primary btn-sm mr-2" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">
                            <i class="fal fa-plus mr-1"></i> Yeni Depo
                        </button>
                        <button class="btn btn-panel" data-action="panel-collapse" title="Daralt"></button>
                        <button class="btn btn-panel" data-action="panel-fullscreen" title="Tam Ekran"></button>
                    </div>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <div class="alert-icon">
                                    <i class="fal fa-info-circle"></i>
                                </div>
                                <div class="flex-1 ml-2">
                                    <span class="h5">Depo Yönetim Paneli</span>
                                    <br>Depo ekle, düzenle veya detaylarını görüntüle.
                                </div>
                            </div>
                        </div>

                        <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-highlight">
                                <tr>
                                    <th class="text-center" style="width: 20px;"><i class="fal fa-warehouse" title="Depo"></i></th>
                                    <th>Depo Bilgileri</th>
                                    <th>İletişim</th>
                                    <th>Durum</th>
                                    <th style="width: 160px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($warehouses as $warehouse)
                                <tr>
                                    <td class="text-center">
                                        <i class="fal fa-warehouse {{ $warehouse->is_active ? 'text-success' : 'text-danger' }}"></i>
                                    </td>
                                    <td>
                                        <div class="warehouse-info">
                                            <div class="warehouse-avatar rounded-circle">
                                                <span class="avatar-initials">{{ strtoupper(substr($warehouse->name,0,2)) }}</span>
                                            </div>
                                            <div>
                                                <div class="warehouse-name">{{ $warehouse->name }}</div>
                                                @if($warehouse->address)
                                                    <small class="text-muted"><i class="fal fa-map-marker-alt mr-1"></i>{{ Str::limit($warehouse->address, 30) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            @if($warehouse->phone)
                                                <div><i class="fal fa-phone-alt text-primary mr-1"></i><a href="tel:{{ $warehouse->phone }}">{{ $warehouse->phone }}</a></div>
                                            @endif
                                            @if($warehouse->manager_name)
                                                <div><i class="fal fa-user text-primary mr-1"></i>{{ $warehouse->manager_name }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="status-indicator">
                                            <span class="status-badge {{ $warehouse->is_active ? 'status-active' : 'status-inactive' }}">
                                                <i class="fal {{ $warehouse->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                                {{ $warehouse->is_active ? 'Aktif' : 'Pasif' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-xs btn-info" onclick="viewWarehouseStocks({{ $warehouse->id }})">
                                                <i class="fal fa-boxes"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-primary edit-warehouse-btn"
                                                data-warehouse-id="{{ $warehouse->id }}"
                                                data-name="{{ $warehouse->name }}"
                                                data-address="{{ $warehouse->address }}"
                                                data-phone="{{ $warehouse->phone }}"
                                                data-manager="{{ $warehouse->manager }}"
                                                data-active="{{ $warehouse->is_active }}">
                                                <i class="fal fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-danger btn-delete-warehouse" 
                                                data-id="{{ $warehouse->id }}" 
                                                data-name="{{ $warehouse->name }}">
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

<!-- Edit Warehouse Modal -->
<div class="modal fade" id="editWarehouseModal" tabindex="-1" aria-labelledby="editWarehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4">
                    <i class="fal fa-warehouse me-2"></i>Depo Düzenle
                </h5>
                <button type="button" class="btn btn-sm text-dark" data-bs-dismiss="modal" aria-label="Kapat" style="border: none; background: none;">
                    <i class="fal fa-times"></i>
                </button>
            </div>
            <form id="editWarehouseForm" action="{{ route('warehouse.update') }}" method="POST">
                @csrf
                <input type="hidden" name="warehouse_id" id="edit_warehouse_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="edit_name">Depo Adı <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fal fa-warehouse-alt"></i>
                                    </span>
                                    <input type="text" class="form-control" id="edit_name" name="name" required>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label" for="edit_address">Adres</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fal fa-map-marker-alt"></i>
                                    </span>
                                    <textarea class="form-control" id="edit_address" name="address" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="edit_phone">Telefon</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fal fa-phone"></i>
                                    </span>
                                    <input type="tel" class="form-control" id="edit_phone" name="phone">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label" for="edit_manager">Depo Sorumlusu</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fal fa-user"></i>
                                    </span>
                                    <select class="custom-select" id="edit_manager" name="manager">
                                        <option value="">Seçiniz...</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label mb-2" for="edit_is_active">
                                    <i class="fal fa-toggle-on me-2 text-primary"></i> Aktiflik Durumu
                                </label>
                                <div class="status-toggle-container">
                                    <div class="status-toggle-wrapper">
                                        <input type="checkbox" class="status-toggle-input" id="edit_is_active" name="is_active" value="1">
                                        <label class="status-toggle-label" for="edit_is_active">
                                            <span class="status-toggle-slider"></span>
                                            <span class="status-toggle-text status-toggle-text-on">Aktif</span>
                                            <span class="status-toggle-text status-toggle-text-off">Pasif</span>
                                        </label>
                                    </div>
                                    <div class="status-description">
                                        <small class="text-muted">
                                            <i class="fal fa-info-circle me-1"></i>
                                            Aktif depolar sistemde görünür ve kullanılabilir
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fal fa-times me-2"></i> İptal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fal fa-save me-2"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Warehouse Modal -->
<div class="modal fade" id="viewWarehouseModal" tabindex="-1" aria-labelledby="viewWarehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title h4 d-flex align-items-center" id="viewWarehouseModalLabel">
                    <i class="fal fa-warehouse me-2"></i>
                    <span id="warehouseName">Depo Detayları</span>
                </h5>
                <button type="button" class="btn btn-sm text-white" data-bs-dismiss="modal" aria-label="Kapat" style="border: none; background: none;">
                    <i class="fal fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <!-- Depo Bilgileri -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fal fa-warehouse text-primary"></i>
                            </div>
                            <div class="info-content">
                                <small class="text-muted text-uppercase fw-medium">Depo Adı</small>
                                <div id="modalWarehouseName" class="fw-semibold text-dark">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fal fa-map-marker-alt text-success"></i>
                            </div>
                            <div class="info-content">
                                <small class="text-muted text-uppercase fw-medium">Adres</small>
                                <div id="modalWarehouseAddress" class="fw-semibold text-dark">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fal fa-phone text-info"></i>
                            </div>
                            <div class="info-content">
                                <small class="text-muted text-uppercase fw-medium">Telefon</small>
                                <div id="modalWarehousePhone" class="fw-semibold text-dark">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fal fa-user-tie text-warning"></i>
                            </div>
                            <div class="info-content">
                                <small class="text-muted text-uppercase fw-medium">Sorumlu</small>
                                <div id="modalWarehouseManager" class="fw-semibold text-dark">-</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Depo İstatistikleri -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="stats-card border-0 shadow-sm">
                            <div class="stats-card-body">
                                <div class="stats-content">
                                    <div class="stats-icon bg-primary-light">
                                        <i class="fal fa-boxes text-primary"></i>
                                    </div>
                                    <div class="stats-info">
                                        <h3 id="totalProducts" class="stats-number text-primary mb-1">0</h3>
                                        <p class="stats-label text-muted mb-0">Toplam Ürün</p>
                                        <small class="stats-trend text-success">
                                            <i class="fal fa-arrow-up"></i> Tüm ürünler
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card border-0 shadow-sm">
                            <div class="stats-card-body">
                                <div class="stats-content">
                                    <div class="stats-icon bg-success-light">
                                        <i class="fal fa-check-circle text-success"></i>
                                    </div>
                                    <div class="stats-info">
                                        <h3 id="activeProducts" class="stats-number text-success mb-1">0</h3>
                                        <p class="stats-label text-muted mb-0">Aktif Ürün</p>
                                        <small class="stats-trend text-success">
                                            <i class="fal fa-check"></i> Satışa hazır
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card border-0 shadow-sm">
                            <div class="stats-card-body">
                                <div class="stats-content">
                                    <div class="stats-icon bg-warning-light">
                                        <i class="fal fa-warehouse text-warning"></i>
                                    </div>
                                    <div class="stats-info">
                                        <h3 id="totalStock" class="stats-number text-warning mb-1">0</h3>
                                        <p class="stats-label text-muted mb-0">Toplam Stok</p>
                                                                                 <small class="stats-trend text-warning">
                                            <i class="fal fa-cubes"></i> Toplam miktar
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card border-0 shadow-sm">
                            <div class="stats-card-body">
                                <div class="stats-content">
                                    <div class="stats-icon bg-info-light">
                                        <i class="fal fa-lira-sign text-info"></i>
                                    </div>
                                    <div class="stats-info">
                                        <h3 id="totalValue" class="stats-number text-info mb-1">₺ 0</h3>
                                        <p class="stats-label text-muted mb-0">Toplam Değer</p>
                                        <small class="stats-trend text-info">
                                            <i class="fal fa-chart-line"></i> Envanter değeri
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Ürün/Hizmet Listesi -->
                <div class="card border-0 shadow-sm">

                    <div class="card-body p-0">
                        <div id="loadingServices" class="text-center py-4" style="display:none;">
                            <i class="fal fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2">Ürünler yükleniyor...</p>
                        </div>
                        <div id="noServicesMessage" class="text-center py-5" style="display:none;">
                            <i class="fal fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Bu depoda henüz ürün/hizmet bulunmuyor</h5>
                            <p class="text-muted">Ürün/hizmet eklemek için Services sayfasını kullanın.</p>
                        </div>
                        <div id="servicesContainer">
                            <div class="table-responsive">
                                <table id="warehouseServicesTable" class="table table-bordered table-hover table-striped w-100">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center" style="width: 40px;">#</th>
                                            <th>Ürün/Hizmet</th>
                                            <th>Kategori</th>
                                            <th>Fiyat</th>
                                            <th>Stok</th>
                                            <th>Birim</th>
                                            <th class="text-center">Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody id="servicesTableBody">
                                        <!-- AJAX ile doldurulacak -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fal fa-times me-2"></i> Kapat
                </button>
                <button type="button" class="btn btn-primary" onclick="exportWarehouseReport()">
                    <i class="fal fa-download me-2"></i> Rapor Al
                </button>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<style>
.dataTables_wrapper .dataTables_filter{float:left;margin-left:0}
.dataTables_wrapper .dataTables_length{float:right}
.dataTables_filter input{width:300px}
.warehouse-avatar{width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:linear-gradient(45deg,#FF9800,#F57C00);margin-right:12px}
.avatar-initials{font-size:16px;font-weight:500;color:white;text-transform:uppercase}
.warehouse-info{display:flex;align-items:center}
.warehouse-name{font-size:14px;font-weight:600;color:#333}
.gap-2{gap:0.5rem}
/* Warehouse Modal Styling */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.info-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.info-item:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.info-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: white;
    margin-right: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.info-content {
    flex: 1;
}

/* Stats Cards */
.stats-card {
    border-radius: 12px;
    transition: all 0.3s ease;
    background: white;
}

.stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.stats-card-body {
    padding: 1.5rem;
}

.stats-content {
    display: flex;
    align-items: center;
}

.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.bg-primary-light { background: rgba(102, 126, 234, 0.1); }
.bg-success-light { background: rgba(40, 167, 69, 0.1); }
.bg-warning-light { background: rgba(255, 193, 7, 0.1); }
.bg-info-light { background: rgba(23, 162, 184, 0.1); }

.stats-number {
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1;
}

.stats-label {
    font-size: 0.875rem;
    font-weight: 500;
}

.stats-trend {
    font-size: 0.75rem;
    font-weight: 500;
}

/* Table Styling */
#warehouseServicesTable {
    border: none;
}

#warehouseServicesTable th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 1rem 0.75rem;
}

#warehouseServicesTable tbody td {
    border: none;
    border-bottom: 1px solid #f1f3f4;
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

#warehouseServicesTable tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.02);
    transition: all 0.2s ease;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.product-avatar {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.product-details {
    flex: 1;
    min-width: 0;
}

.product-name {
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
    line-height: 1.3;
}

.product-code {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* Modern Badge Styling */
.badge {
    font-weight: 500;
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    letter-spacing: 0.3px;
}

/* Modal Header Enhancement */
.modal-header.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

/* Card Enhancements */
.card {
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}

/* Loading State */
#loadingServices {
    padding: 3rem 1rem;
}

#loadingServices .fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Empty State */
#noServicesMessage {
    padding: 3rem 1rem;
}

#noServicesMessage .fa-box-open {
    opacity: 0.5;
}
    padding: 1rem 0.75rem;
}

#warehouseServicesTable td {
    border: none;
    border-bottom: 1px solid #f1f3f4;
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

#warehouseServicesTable tbody tr:hover {
    background: #f8f9fa;
}

.product-info {
    display: flex;
    align-items: center;
}

.product-avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    margin-right: 12px;
    color: white;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

.product-details {
    flex: 1;
}

.product-name {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 2px;
    font-size: 0.925rem;
}

.product-code {
    font-size: 0.75rem;
    color: #718096;
    font-weight: 500;
}

.stock-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stock-badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-weight: 600;
    letter-spacing: 0.25px;
}

.stock-number {
    font-weight: 700;
    color: #2d3748;
}

.price-display {
    font-weight: 700;
    font-size: 0.925rem;
    color: #2d3748;
}

.status-badge {
    padding: 0.375rem 0.875rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.25px;
}

/* Loading and Empty States */
#loadingServices {
    padding: 3rem 1rem !important;
}

#noServicesMessage {
    padding: 3rem 1rem !important;
}
</style>

<script defer src="{{ asset('js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // DataTable initialization
    const initDataTable = () => {
        if ($.fn.DataTable.isDataTable('#dt-basic-example')) {
            $('#dt-basic-example').DataTable().destroy();
        }
        
        $('#dt-basic-example').DataTable({
            responsive: true,
            stateSave: false, // StateSave'i kapatıyoruz
            pageLength: 25,
            order: [[1, 'asc']],
            language: { url: "{{ asset('media/data/tr.json') }}" },
            columnDefs: [
                { orderable: false, targets: [0, 4] },
                { searchable: false, targets: [0, 4] }
            ],
            destroy: true // Auto destroy özelliği
        });
    };

    // Initialize DataTable
    initDataTable();

    // Edit warehouse modal
    $(document).off('click', '.edit-warehouse-btn').on('click', '.edit-warehouse-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const warehouseId = $(this).data('warehouse-id');
        const name = $(this).data('name');
        const address = $(this).data('address');
        const phone = $(this).data('phone');
        const manager = $(this).data('manager');
        const isActive = $(this).data('active');

        // Modal'ı doldur
        $('#edit_warehouse_id').val(warehouseId);
        $('#edit_name').val(name || '');
        $('#edit_address').val(address || '');
        $('#edit_phone').val(phone || '');
        $('#edit_manager').val(manager || '');
        $('#edit_is_active').prop('checked', isActive == '1');

        // Modal'ı aç
        $('#editWarehouseModal').modal('show');
    });

    // Delete warehouse
    $(document).off('click', '.btn-delete-warehouse').on('click', '.btn-delete-warehouse', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Emin misiniz?',
            html: `<strong>${name}</strong> isimli depoyu silmek istiyor musunuz?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet, Sil',
            cancelButtonText: 'İptal',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = `/warehouse/delete/${id}`;
            }
        });
    });

    // Modal cleanup on hide
    $('#viewWarehouseModal').on('hidden.bs.modal', function () {
        if ($.fn.DataTable.isDataTable('#warehouseServicesTable')) {
            $('#warehouseServicesTable').DataTable().destroy();
        }
        $('#servicesTableBody').empty();
    });

    $('#editWarehouseModal').on('hidden.bs.modal', function () {
        $('#editWarehouseForm')[0].reset();
    });
});

// Cache DOM elements for better performance
const warehouseModal = {
    modal: null,
    elements: {},
    formatter: new Intl.NumberFormat('tr-TR'),
    priceFormatter: new Intl.NumberFormat('tr-TR', {minimumFractionDigits: 2, maximumFractionDigits: 2}),
    
    init() {
        this.modal = document.getElementById('viewWarehouseModal');
        this.elements = {
            loadingServices: document.getElementById('loadingServices'),
            servicesContainer: document.getElementById('servicesContainer'),
            noServicesMessage: document.getElementById('noServicesMessage'),
            warehouseName: document.getElementById('warehouseName'),
            modalWarehouseName: document.getElementById('modalWarehouseName'),
            modalWarehouseAddress: document.getElementById('modalWarehouseAddress'),
            modalWarehousePhone: document.getElementById('modalWarehousePhone'),
            modalWarehouseManager: document.getElementById('modalWarehouseManager'),
            totalProducts: document.getElementById('totalProducts'),
            activeProducts: document.getElementById('activeProducts'),
            totalStock: document.getElementById('totalStock'),
            totalValue: document.getElementById('totalValue'),
            servicesTableBody: document.getElementById('servicesTableBody'),
            warehouseServicesTable: document.getElementById('warehouseServicesTable')
        };
    },

    show() {
        $('#viewWarehouseModal').modal('show');
    },

    hide() {
        $('#viewWarehouseModal').modal('hide');
    },

    showLoading() {
        this.elements.loadingServices.style.display = 'block';
        this.elements.servicesContainer.style.display = 'none';
        this.elements.noServicesMessage.style.display = 'none';
    },

    hideLoading() {
        this.elements.loadingServices.style.display = 'none';
    },

    resetData() {
        this.elements.warehouseName.textContent = 'Depo Detayları';
        this.elements.modalWarehouseName.textContent = '-';
        this.elements.modalWarehouseAddress.textContent = '-';
        this.elements.modalWarehousePhone.textContent = '-';
        this.elements.modalWarehouseManager.textContent = '-';
        this.elements.totalProducts.textContent = '0';
        this.elements.activeProducts.textContent = '0';
        this.elements.totalStock.textContent = '0';
        this.elements.totalValue.textContent = '₺0';
        this.elements.servicesTableBody.innerHTML = '';
    },

    destroyDataTable() {
        if ($.fn.DataTable.isDataTable('#warehouseServicesTable')) {
            $('#warehouseServicesTable').DataTable().destroy();
        }
    },

    fillWarehouseInfo(warehouse) {
        this.elements.warehouseName.textContent = warehouse.name;
        this.elements.modalWarehouseName.textContent = warehouse.name;
        this.elements.modalWarehouseAddress.textContent = warehouse.address || '-';
        this.elements.modalWarehousePhone.textContent = warehouse.phone || '-';
        this.elements.modalWarehouseManager.textContent = warehouse.manager_name || '-';
    },

    fillStatistics(stats) {
        this.elements.totalProducts.textContent = stats.total_products;
        this.elements.activeProducts.textContent = stats.active_products;
        this.elements.totalStock.textContent = this.formatter.format(stats.total_stock || 0);
        this.elements.totalValue.textContent = `₺${this.formatter.format(stats.total_value)}`;
    },

    generateStockBadge(service) {
        if (service.is_stock != 1) {
            return '<span class="badge bg-light text-dark border fw-medium">Stok takipsiz</span>';
        }
        
        const stockCount = parseInt(service.stock) || 0;
        const stockText = `${stockCount} adet`;
        
        if (stockCount > 50) return `<span class="badge bg-success fw-medium">${stockText}</span>`;
        if (stockCount > 10) return `<span class="badge bg-warning fw-medium">${stockText}</span>`;
        if (stockCount > 0) return `<span class="badge bg-danger fw-medium">${stockText}</span>`;
        return '<span class="badge bg-dark fw-medium">Stok yok</span>';
    },

    generateStatusBadge(isActive) {
        return isActive == 1 
            ? '<span class="badge bg-success fw-medium"><i class="fal fa-check me-1"></i>Aktif</span>'
            : '<span class="badge bg-danger fw-medium"><i class="fal fa-times me-1"></i>Pasif</span>';
    },

    fillServicesTable(services) {
        if (services.length === 0) {
            this.elements.noServicesMessage.style.display = 'block';
            return;
        }

        // Use DocumentFragment for better performance
        const fragment = document.createDocumentFragment();
        const tableRows = services.map((service, index) => {
            const initials = service.name.substring(0, 2).toUpperCase();
            const codeSection = service.code ? `<div class="product-code">Kod: ${service.code}</div>` : '';
            const price = this.priceFormatter.format(service.total_price || service.price);
            
            return `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td>
                        <div class="product-info">
                            <div class="product-avatar">${initials}</div>
                            <div class="product-details">
                                <div class="product-name">${service.name}</div>
                                ${codeSection}
                            </div>
                        </div>
                    </td>
                    <td>${service.category_name || '-'}</td>
                    <td><span class="fw-semibold text-success">₺${price}</span></td>
                    <td>${this.generateStockBadge(service)}</td>
                    <td>${service.unit_name || '-'}</td>
                    <td class="text-center">${this.generateStatusBadge(service.is_active)}</td>
                </tr>
            `;
        }).join('');

        this.elements.servicesTableBody.innerHTML = tableRows;
        this.elements.servicesContainer.style.display = 'block';

        // Initialize DataTable asynchronously
        requestAnimationFrame(() => {
            if (!$.fn.DataTable.isDataTable('#warehouseServicesTable')) {
                $('#warehouseServicesTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    order: [[1, 'asc']],
                    language: { url: "{{ asset('media/data/tr.json') }}" },
                    columnDefs: [
                        { orderable: false, targets: [0, 6] },
                        { searchable: false, targets: [0, 6] }
                    ],
                    destroy: true,
                    stateSave: false,
                    deferRender: true
                });
            }
        });
    }
};

// Initialize cache once DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    warehouseModal.init();
});

// Optimized warehouse stocks function
window.viewWarehouseStocks = function(warehouseId) {
    warehouseModal.show();
    warehouseModal.showLoading();
    warehouseModal.resetData();
    warehouseModal.destroyDataTable();
    
    // Use fetch API for better performance
    fetch(`/warehouse/details/${warehouseId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        signal: AbortSignal.timeout(10000)
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            warehouseModal.fillWarehouseInfo(data.warehouse);
            warehouseModal.fillStatistics(data.statistics);
            warehouseModal.fillServicesTable(data.services);
        } else {
            showError(data.error || 'Depo detayları yüklenirken hata oluştu');
            warehouseModal.hide();
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        showError('Depo detayları yüklenirken bir hata oluştu');
        warehouseModal.hide();
    })
    .finally(() => {
        warehouseModal.hideLoading();
    });
};

// Rapor alma fonksiyonu
window.exportWarehouseReport = function() {
    showSuccess('Rapor alma ozelligi yakinda eklenecek');
};

// Success and Error functions for flash messages
window.showSuccess = function(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Basarili!',
            text: message,
            timer: 3000,
            showConfirmButton: false,
            allowOutsideClick: true
        });
    } else {
        alert('Basarili: ' + message);
    }
};

window.showError = function(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Hata!',
            text: message,
            timer: 5000,
            showConfirmButton: false,
            allowOutsideClick: true
        });
    } else {
        alert('Hata: ' + message);
    }
};
</script>