<style>
    /* Stock Movements Modal - Modern Design */
    #stockMovementsModal .modal-dialog {
        max-width: 800px;
    }

    #stockMovementsModal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }

    #stockMovementsModal .modal-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    #stockMovementsModal .modal-title i {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    #stockMovementsModal .btn-close {
        background: rgba(255, 255, 255, 0.2);
        border: 0;
        color: white;
        border-radius: 0.25rem;
        padding: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    #stockMovementsModal .btn-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
    }

    #stockMovementsModal .modal-body {
        padding: 1.5rem;
        background: #f8f9fa;
        max-height: 70vh;
        overflow-y: auto;
    }

    /* SmartPanel Product Info */
    #stockMovementsModal .product-info-panel {
        margin-bottom: 1.5rem;
    }

    #stockMovementsModal .product-info-panel .panel-hdr {
        background: #fff;
        border-bottom: 1px solid #e3e6f0;
        padding: 0.75rem 1rem;
        border-radius: 0.35rem 0.35rem 0 0;
    }

    #stockMovementsModal .product-info-panel .panel-hdr h2 {
        font-size: 0.9rem;
        font-weight: 600;
        color: #5a5c69;
        margin: 0;
    }

    #stockMovementsModal .product-info-panel .panel-container {
        background: #fff;
        border-radius: 0 0 0.35rem 0.35rem;
        padding: 1rem;
    }

    #stockMovementsModal .product-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    #stockMovementsModal .info-item {
        text-align: center;
        padding: 1rem;
        border-radius: 0.35rem;
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        transition: all 0.2s ease;
    }

    #stockMovementsModal .info-item:hover {
        background: #eaecf4;
        border-color: #bac8f3;
    }

    #stockMovementsModal .info-label {
        font-size: 0.75rem;
        color: #858796;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    #stockMovementsModal .info-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #5a5c69;
        line-height: 1.2;
    }

    #stockMovementsModal .info-value.stock {
        color: #1cc88a;
    }

    /* SmartPanel Movements Table */
    #stockMovementsModal .movements-panel {
        margin-bottom: 1.5rem;
    }

    #stockMovementsModal .movements-panel .panel-hdr {
        background: #fff;
        border-bottom: 1px solid #e3e6f0;
        padding: 0.75rem 1rem;
        border-radius: 0.35rem 0.35rem 0 0;
    }

    #stockMovementsModal .movements-panel .panel-hdr h2 {
        font-size: 0.9rem;
        font-weight: 600;
        color: #5a5c69;
        margin: 0;
    }

    #stockMovementsModal .movements-panel .panel-container {
        background: #fff;
        border-radius: 0 0 0.35rem 0.35rem;
        overflow: hidden;
        max-height: 40vh;
        overflow-y: auto;
    }

    #stockMovementsModal .movements-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
        font-size: 0.875rem;
    }

    #stockMovementsModal .movements-table thead th {
        background: #f8f9fc;
        color: #5a5c69;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 0.75rem 0.5rem;
        border-bottom: 1px solid #e3e6f0;
        text-align: left;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    #stockMovementsModal .movements-table tbody tr {
        border-bottom: 1px solid #f8f9fc;
        transition: background-color 0.15s ease;
    }

    #stockMovementsModal .movements-table tbody tr:hover {
        background-color: #f8f9fc;
    }

    #stockMovementsModal .movements-table tbody td {
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
        color: #5a5c69;
        border-bottom: 1px solid #f8f9fc;
    }

    #stockMovementsModal .movement-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid transparent;
    }

    #stockMovementsModal .movement-type-badge.in,
    #stockMovementsModal .movement-type-badge.success {
        background: #d1ecf1;
        color: #0c5460;
        border-color: #bee5eb;
    }

    #stockMovementsModal .movement-type-badge.out,
    #stockMovementsModal .movement-type-badge.danger {
        background: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }

    #stockMovementsModal .movement-type-badge.adjustment,
    #stockMovementsModal .movement-type-badge.info {
        background: #d1ecf1;
        color: #0c5460;
        border-color: #bee5eb;
    }

    #stockMovementsModal .movement-type-badge.warning {
        background: #fff3cd;
        color: #856404;
        border-color: #ffeaa7;
    }

    #stockMovementsModal .movement-type-badge.primary {
        background: #cce7ff;
        color: #004085;
        border-color: #b3d9ff;
    }

    #stockMovementsModal .movement-type-badge.secondary {
        background: #e2e3e5;
        color: #383d41;
        border-color: #d6d8db;
    }

    #stockMovementsModal .quantity-change {
        font-weight: 600;
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        display: inline-block;
        min-width: 50px;
        text-align: center;
    }

    #stockMovementsModal .quantity-change.positive {
        color: #155724;
        background: #d4edda;
        border: 1px solid #c3e6cb;
    }

    #stockMovementsModal .quantity-change.negative {
        color: #721c24;
        background: #f8d7da;
        border: 1px solid #f5c6cb;
    }

    #stockMovementsModal .empty-state {
        text-align: center;
        padding: 2rem 1rem;
        color: #858796;
    }

    #stockMovementsModal .empty-state-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 1rem;
        background: #f8f9fc;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #d1d3e2;
        border: 2px solid #e3e6f0;
    }

    #stockMovementsModal .empty-state h3 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
        color: #5a5c69;
    }

    #stockMovementsModal .empty-state p {
        font-size: 0.875rem;
        margin: 0;
        color: #858796;
    }

    #stockMovementsModal .modal-footer {
        background: #fff;
        border-top: 1px solid #e3e6f0;
        padding: 1rem 1.5rem;
        border-radius: 0 0 0.5rem 0.5rem;
    }

    #stockMovementsModal .btn {
        padding: 0.375rem 0.75rem;
        border-radius: 0.35rem;
        font-weight: 600;
        font-size: 0.875rem;
        border: 1px solid;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }

    #stockMovementsModal .btn-secondary {
        background: #858796;
        color: white;
        border-color: #858796;
    }

    #stockMovementsModal .btn-secondary:hover {
        background: #717384;
        border-color: #6b6d7d;
    }

    /* Loading State */
    #stockMovementsModal .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 0.25rem;
        height: 0.75rem;
        margin: 0.5rem 0;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Totals Summary - SmartPanel Style */
    #stockMovementsModal .totals-panel {
        margin-top: 1.5rem;
    }

    #stockMovementsModal .totals-panel .panel-hdr {
        background: #fff;
        border-bottom: 1px solid #e3e6f0;
        padding: 0.75rem 1rem;
        border-radius: 0.35rem 0.35rem 0 0;
    }

    #stockMovementsModal .totals-panel .panel-hdr h2 {
        font-size: 0.9rem;
        font-weight: 600;
        color: #5a5c69;
        margin: 0;
    }

    #stockMovementsModal .totals-panel .panel-container {
        background: #fff;
        border-radius: 0 0 0.35rem 0.35rem;
        padding: 1rem;
    }

    #stockMovementsModal .totals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }

    #stockMovementsModal .total-item {
        text-align: center;
        padding: 1rem;
        border-radius: 0.35rem;
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        transition: all 0.2s ease;
    }

    #stockMovementsModal .total-item:hover {
        background: #eaecf4;
        border-color: #bac8f3;
    }

    #stockMovementsModal .total-label {
        font-size: 0.75rem;
        color: #858796;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    #stockMovementsModal .total-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #5a5c69;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
    }

    #stockMovementsModal .total-value.text-success {
        color: #1cc88a;
    }

    #stockMovementsModal .total-value.text-danger {
        color: #e74a3b;
    }

    #stockMovementsModal .total-value.text-primary {
        color: #4e73df;
    }

    /* Custom scrollbar */
    #stockMovementsModal .movements-panel .panel-container::-webkit-scrollbar {
        width: 6px;
    }

    #stockMovementsModal .movements-panel .panel-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    #stockMovementsModal .movements-panel .panel-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    #stockMovementsModal .movements-panel .panel-container::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        #stockMovementsModal .modal-dialog {
            max-width: 95vw;
        }
        
        #stockMovementsModal .product-info-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        #stockMovementsModal .totals-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        #stockMovementsModal .modal-dialog {
            margin: 0.5rem;
            max-width: none;
        }
        
        #stockMovementsModal .modal-body {
            padding: 1rem;
        }
        
        #stockMovementsModal .product-info-grid {
            grid-template-columns: 1fr;
        }
        
        #stockMovementsModal .totals-grid {
            grid-template-columns: 1fr;
        }
        
        #stockMovementsModal .movements-table {
            font-size: 0.8rem;
        }
        
        #stockMovementsModal .movements-table thead th,
        #stockMovementsModal .movements-table tbody td {
            padding: 0.5rem 0.25rem;
        }
    }

    /* Help Tooltip Styles */
    #stockMovementsModal .help-tooltip {
        display: inline-block;
        width: 16px;
        height: 16px;
        background: #e3e6f0;
        border-radius: 50%;
        text-align: center;
        line-height: 16px;
        font-size: 10px;
        color: #858796;
        cursor: help;
        margin-left: 0.25rem;
        transition: all 0.2s ease;
    }

    #stockMovementsModal .help-tooltip:hover {
        background: #4e73df;
        color: white;
    }

    #stockMovementsModal .explanation-text {
        font-size: 0.75rem;
        color: #858796;
        font-style: italic;
        margin-top: 0.25rem;
    }

    #stockMovementsModal .status-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }

    #stockMovementsModal .status-indicator.good {
        background: #1cc88a;
    }

    #stockMovementsModal .status-indicator.warning {
        background: #f6c23e;
    }

    #stockMovementsModal .status-indicator.danger {
        background: #e74a3b;
    }

    /* Stock Loading Modal Styles */
    #stockLoadingModal .modal-dialog {
        max-width: 600px;
    }

    #stockLoadingModal .form-group {
        margin-bottom: 1rem;
    }

    #stockLoadingModal .form-label {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 0.5rem;
    }

    #stockLoadingModal .form-control {
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        transition: border-color 0.15s ease-in-out;
    }

    #stockLoadingModal .form-control:focus {
        border-color: #bac8f3;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    #stockLoadingModal .btn-primary {
        background: #4e73df;
        border-color: #4e73df;
        color: white;
    }

    #stockLoadingModal .btn-primary:hover {
        background: #2e59d9;
        border-color: #2653d4;
    }

    #stockLoadingModal .product-info {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    #stockLoadingModal .product-info h6 {
        color: #5a5c69;
        margin-bottom: 0.5rem;
    }

    #stockLoadingModal .product-info p {
        color: #858796;
        margin-bottom: 0.25rem;
    }

    #stockLoadingModal .alert {
        border-radius: 0.35rem;
        margin-bottom: 1rem;
    }
</style>

<div class="modal fade" id="stockMovementsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fal fa-chart-line"></i>
                    <span id="productNameTitle">Ürün Stok Geçmişi</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat">
                    <i class="fal fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <!-- Product Info SmartPanel -->
                <div class="panel product-info-panel" data-panel-collapsed="false" data-panel-fullscreen="false" data-panel-close="false" data-panel-locked="false" data-panel-refresh="false" data-panel-reset="false" data-panel-color="false" data-panel-sortable="false">
                    <div class="panel-hdr">
                        <h2><i class="fal fa-box me-1"></i> Ürün Detayları</h2>
                    </div>
                    <div class="panel-container show">
                        <div class="panel-content">
                            <div class="product-info-grid">
                                <div class="info-item">
                                    <div class="info-label">
                                        Ürün Adı
                                        <span class="help-tooltip" title="Bu ürünün tam adı">?</span>
                                    </div>
                                    <div class="info-value" id="productName">-</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        Şu Anki Stok
                                        <span class="help-tooltip" title="Şu anda depoda bulunan miktar">?</span>
                                    </div>
                                    <div class="info-value stock" id="currentStock">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Movements Table SmartPanel -->
                <div class="panel movements-panel" data-panel-collapsed="false" data-panel-fullscreen="true" data-panel-close="false" data-panel-locked="false" data-panel-refresh="true" data-panel-reset="false" data-panel-color="false" data-panel-sortable="false">
                    <div class="panel-hdr">
                        <h2><i class="fal fa-list me-1"></i> Stok Hareket Geçmişi</h2>
                    </div>
                    <div class="panel-container show">
                        <div class="panel-content">
                            <div id="movementsTableContainer">
                                <!-- Loading skeleton -->
                                <div class="p-3">
                                    <div class="loading-skeleton" style="height: 1.5rem; margin-bottom: 0.5rem;"></div>
                                    <div class="loading-skeleton" style="height: 0.75rem; width: 80%;"></div>
                                    <div class="loading-skeleton" style="height: 0.75rem; width: 60%;"></div>
                                    <div class="loading-skeleton" style="height: 0.75rem; width: 90%;"></div>
                                    <div class="loading-skeleton" style="height: 0.75rem; width: 70%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Totals Summary SmartPanel -->
                <div class="panel totals-panel" data-panel-collapsed="false" data-panel-fullscreen="false" data-panel-close="false" data-panel-locked="false" data-panel-refresh="false" data-panel-reset="false" data-panel-color="false" data-panel-sortable="false">
                    <div class="panel-hdr">
                        <h2><i class="fal fa-calculator me-1"></i> Stok Özeti</h2>
                    </div>
                    <div class="panel-container show">
                        <div class="panel-content">
                            <div id="totalsContainer">
                                <!-- Totals will be inserted here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="addStockBtn" style="display: none;">
                    <i class="fal fa-plus"></i> Stok Yükle
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fal fa-times"></i> Kapat
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stock Loading Modal -->
<div class="modal fade" id="stockLoadingModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fal fa-plus-circle"></i>
                    Stok Yükleme
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat">
                    <i class="fal fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <!-- Product Info -->
                <div class="product-info">
                    <h6><i class="fal fa-box me-1"></i> Ürün Bilgileri</h6>
                    <p><strong>Ürün Adı:</strong> <span id="loadingProductName">-</span></p>
                    <p><strong>Mevcut Stok:</strong> <span id="loadingCurrentStock">-</span></p>
                    <p><strong>Depo:</strong> <span id="loadingWarehouseName">-</span></p>
                </div>

                <!-- Stock Loading Form -->
                <form id="stockLoadingForm">
                    <input type="hidden" id="loadingProductId" name="menu_item_id">
                    <input type="hidden" id="loadingWarehouseId" name="warehouse_id">
                    
                    <div class="form-group">
                        <label for="movementType" class="form-label">
                            İşlem Türü <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="movementType" name="type" required>
                            <option value="">İşlem türü seçin</option>
                            <option value="in">Stok Girişi</option>
                            <option value="purchase">Alış</option>
                            <option value="return">İade</option>
                            <option value="transfer">Transfer Girişi</option>
                            <option value="adjustment">Stok Düzeltme</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantity" class="form-label">
                            Miktar <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="quantity" name="quantity" 
                               min="0.01" step="0.01" required placeholder="Miktar girin">
                    </div>



                    <div class="form-group">
                        <label for="notes" class="form-label">
                            Açıklama
                        </label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Stok girişi hakkında açıklama..."></textarea>
                    </div>

                    <div id="loadingAlert" class="alert" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fal fa-times"></i> İptal
                </button>
                <button type="button" class="btn btn-primary" id="saveStockMovement">
                    <i class="fal fa-save"></i> Stok Yükle
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('stockMovementsModal');
    
    window.openStockMovementsModal = function(productId, productName) {
        // Global değişkenleri sakla
        window.currentProductId = productId;
        window.currentProductName = productName;
        
        // Modal başlığını güncelle
        document.getElementById('productNameTitle').textContent = `${productName} - Stok Geçmişi`;
        
        // Loading state göster
        document.getElementById('movementsTableContainer').innerHTML = `
            <div class="p-3">
                <div class="loading-skeleton" style="height: 1.5rem; margin-bottom: 0.5rem;"></div>
                <div class="loading-skeleton" style="height: 0.75rem; width: 80%;"></div>
                <div class="loading-skeleton" style="height: 0.75rem; width: 60%;"></div>
                <div class="loading-skeleton" style="height: 0.75rem; width: 90%;"></div>
                <div class="loading-skeleton" style="height: 0.75rem; width: 70%;"></div>
            </div>
        `;
        
        // Modal'ı aç
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        // Modal kapatıldığında backdrop'u temizle
        modal.addEventListener('hidden.bs.modal', function() {
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';
        });
        
        // AJAX ile stok hareketlerini çek
        fetch(`/ajax/getStockMovements/${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.currentMovementTotals = data.totals;
                    renderStockMovements(data.product, data.movements);
                } else {
                    showError(data.message || 'Stok geçmişi yüklenirken hata oluştu');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Stok geçmişi yüklenirken hata oluştu');
            });
    };
    
    function renderStockMovements(product, movements) {
        // Product bilgilerini güncelle
        document.getElementById('productName').textContent = product.name || '-';
        
        // Tablodaki verilere göre stok hesaplaması
        const positiveTypes = ['in', 'purchase', 'return', 'transfer'];
        const totalOut = movements.filter(m => !positiveTypes.includes(m.type)).reduce((sum, m) => sum + m.quantity, 0);
        const totalIn = movements.filter(m => positiveTypes.includes(m.type)).reduce((sum, m) => sum + m.quantity, 0);
        const calculatedStock = totalIn - totalOut;
        
        // Hesaplanan stoğu göster
        document.getElementById('currentStock').textContent = calculatedStock + ' adet';
        
        // Movements tablosunu oluştur
        let tableHtml = '';
        
        if (!movements || movements.length === 0) {
            tableHtml = `
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fal fa-inbox"></i>
                    </div>
                    <h3>Henüz Stok Hareketi Yok</h3>
                    <p>Bu ürün için henüz stok girişi veya çıkışı yapılmamış.</p>
                </div>
            `;
        } else {
            tableHtml = `
                <table class="movements-table">
                    <thead>
                        <tr>
                            <th>İşlem Türü</th>
                            <th>Miktar</th>
                            <th>Açıklama</th>
                            <th>Tarih</th>
                            <th>İşlemi Yapan</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            movements.forEach(movement => {
                const movementType = getMovementTypeBadge(movement.type, movement.type_label, movement.type_class, movement.type_icon);
                
                // Hareket türüne göre pozitif/negatif belirle
                const isPositive = ['in', 'purchase', 'return', 'transfer'].includes(movement.type);
                const quantityClass = isPositive ? 'positive' : 'negative';
                
                // Fix double negative signs issue
                let quantityText;
                if (isPositive) {
                    quantityText = `+${movement.quantity}`;
                } else {
                    // Remove any existing negative signs and add only one
                    const cleanQuantity = movement.quantity.toString().replace(/^-+/, '');
                    quantityText = `-${cleanQuantity}`;
                }
                
                tableHtml += `
                    <tr>
                        <td>${movementType}</td>
                        <td>
                            <span class="quantity-change ${quantityClass}">
                                ${quantityText}
                            </span>
                        </td>
                        <td>${movement.note || 'Açıklama yok'}</td>
                        <td>${movement.date || '-'}</td>
                        <td>${movement.user_name || 'Sistem'}</td>
                    </tr>
                `;
            });
            
            tableHtml += `
                    </tbody>
                </table>
            `;
        }
        
        document.getElementById('movementsTableContainer').innerHTML = tableHtml;
        
        // Totals bilgilerini göster
        if (window.currentMovementTotals) {
            addTotalsFooter(window.currentMovementTotals);
        }
        
        // Stok yükleme butonunu aktif hale getir
        const addStockBtn = document.getElementById('addStockBtn');
        addStockBtn.style.display = 'inline-block';
        addStockBtn.onclick = function() {
            const currentStock = calculatedStock;
            const warehouseId = product.warehouse_id;
            const warehouseName = product.warehouse_name || 'Depo bilgisi yok';
            window.openStockLoadingModal(product.id, product.name, currentStock, warehouseId, warehouseName);
        };
    }
    
    function getMovementTypeBadge(type, label, cssClass, icon) {
        // Hareket türlerini daha anlaşılır hale getir
        const typeLabels = {
            'in': 'Stok Girişi',
            'out': 'Stok Çıkışı',
            'adjustment': 'Stok Düzeltme',
            'transfer': 'Transfer',
            'sale': 'Satış',
            'purchase': 'Alış',
            'return': 'İade',
            'damage': 'Hasar',
            'expiry': 'Son Kullanma'
        };
        
        const displayLabel = typeLabels[type] || label || 'Diğer';
        
        return `<span class="movement-type-badge ${type}">
            <i class="fal ${icon}"></i> ${displayLabel}
        </span>`;
    }
    
    function addTotalsFooter(totals) {
        const totalsHtml = `
            <div class="totals-grid">
                <div class="total-item">
                    <div class="total-label">
                        Toplam Giriş
                        <span class="help-tooltip" title="Bu ürün için yapılan toplam stok girişi">?</span>
                    </div>
                    <div class="total-value text-success">
                        <i class="fal fa-arrow-up"></i>
                        <strong>+${totals.total_in}</strong>
                    </div>
                    <div class="explanation-text">Alış, iade, transfer girişi</div>
                </div>
                <div class="total-item">
                    <div class="total-label">
                        Toplam Çıkış
                        <span class="help-tooltip" title="Bu ürün için yapılan toplam stok çıkışı">?</span>
                    </div>
                    <div class="total-value text-danger">
                        <i class="fal fa-arrow-down"></i>
                        <strong>-${totals.total_out}</strong>
                    </div>
                    <div class="explanation-text">Satış, hasar, transfer çıkışı</div>
                </div>
                <div class="total-item">
                    <div class="total-label">
                        Net Değişim
                        <span class="help-tooltip" title="Girişler - Çıkışlar = Net değişim">?</span>
                    </div>
                    <div class="total-value ${totals.net_change >= 0 ? 'text-success' : 'text-danger'}">
                        <i class="fal fa-exchange-alt"></i>
                        <strong>${totals.net_change >= 0 ? '+' : ''}${totals.net_change}</strong>
                    </div>
                    <div class="explanation-text">Toplam artış/azalış</div>
                </div>
                <div class="total-item">
                    <div class="total-label">
                        Sistem Stoğu
                        <span class="help-tooltip" title="Sistemde kayıtlı güncel stok miktarı">?</span>
                    </div>
                    <div class="total-value text-primary">
                        <i class="fal fa-box"></i>
                        <strong>${totals.current_stock}</strong>
                    </div>
                    <div class="explanation-text">Hareketlerden hesaplanan stok</div>
                </div>
            </div>
        `;
        
        document.getElementById('totalsContainer').innerHTML = totalsHtml;
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleString('tr-TR', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Stock Loading Modal Functions
    window.openStockLoadingModal = function(productId, productName, currentStock, warehouseId, warehouseName) {
        // Modal içeriğini doldur
        document.getElementById('loadingProductName').textContent = productName || '-';
        document.getElementById('loadingCurrentStock').textContent = (currentStock || 0) + ' adet';
        document.getElementById('loadingWarehouseName').textContent = warehouseName || '-';
        document.getElementById('loadingProductId').value = productId;
        document.getElementById('loadingWarehouseId').value = warehouseId || '';
        
        // Formu temizle
        document.getElementById('stockLoadingForm').reset();
        document.getElementById('loadingAlert').style.display = 'none';
        
        // Modal'ı aç
        const bsModal = new bootstrap.Modal(document.getElementById('stockLoadingModal'));
        bsModal.show();
    };

    // Stok yükleme işlemi
    document.getElementById('saveStockMovement').addEventListener('click', function() {
        const form = document.getElementById('stockLoadingForm');
        const formData = new FormData(form);
        const alertDiv = document.getElementById('loadingAlert');
        
        // Form validasyonu
        if (!formData.get('type') || !formData.get('quantity')) {
            showLoadingAlert('Lütfen tüm zorunlu alanları doldurun.', 'danger');
            return;
        }
        
        if (parseFloat(formData.get('quantity')) <= 0) {
            showLoadingAlert('Miktar 0\'dan büyük olmalıdır.', 'danger');
            return;
        }
        
        // Loading state
        const saveBtn = document.getElementById('saveStockMovement');
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fal fa-spinner fa-spin"></i> Yükleniyor...';
        saveBtn.disabled = true;
        
        // AJAX isteği
        fetch('/ajax/addStockMovement', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                menu_item_id: formData.get('menu_item_id'),
                warehouse_id: formData.get('warehouse_id'),
                type: formData.get('type'),
                quantity: formData.get('quantity'),
                notes: formData.get('notes')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showLoadingAlert('Stok başarıyla yüklendi!', 'success');
                form.reset();
                
                        // 2 saniye sonra modal'ı kapat
        setTimeout(() => {
            const bsModal = bootstrap.Modal.getInstance(document.getElementById('stockLoadingModal'));
            bsModal.hide();
            
            // Backdrop'u temizle
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            
            // Body'den modal-open class'ını kaldır
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';
            
            // Stok hareketleri modal'ını yenile (eğer açıksa)
            if (window.currentProductId) {
                window.openStockMovementsModal(window.currentProductId, window.currentProductName);
            }
        }, 2000);
            } else {
                showLoadingAlert(data.message || 'Stok yüklenirken hata oluştu.', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showLoadingAlert('Stok yüklenirken hata oluştu.', 'danger');
        })
        .finally(() => {
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        });
    });

    function showLoadingAlert(message, type) {
        const alertDiv = document.getElementById('loadingAlert');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        alertDiv.style.display = 'block';
        
        // Alert'i otomatik gizle
        setTimeout(() => {
            alertDiv.style.display = 'none';
        }, 5000);
    }

    // Modal kapatıldığında formu temizle
    document.getElementById('stockLoadingModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('stockLoadingForm').reset();
        document.getElementById('loadingAlert').style.display = 'none';
        
        // Backdrop'u temizle
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        
        // Body'den modal-open class'ını kaldır
        document.body.classList.remove('modal-open');
        document.body.style.paddingRight = '';
    });
});
</script> 
