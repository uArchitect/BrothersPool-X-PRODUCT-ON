<div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4">
                    <i class="fal fa-plus-circle me-2"></i>
                    {{ (isset($isProduct) && $isProduct) ? 'Ürün Oluştur' : 'Hizmet Oluştur' }}
                </h5>
                <button type="button" class="btn btn-sm text-white" data-bs-dismiss="modal" aria-label="Kapat" style="border: none; background: none;">
            <i class="fal fa-times"></i>
        </button>
            </div>
            <form action="{{ route('services.add') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Sol Kolon: Temel Bilgiler ve Stok (ürünse) -->
                        <div class="col-md-6">
                            <div class="panel mb-3">
                                <div class="panel-hdr">
                                    <h2><i class="fal fa-tags mr-2"></i>Temel Bilgiler</h2>
                                </div>
                                <div class="panel-container show">
                                    <div class="panel-content">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                            <select class="custom-select" name="category_id" required>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label">Ad <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label">Açıklama</label>
                                            <textarea class="form-control" name="description" rows="3"></textarea>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{ (isset($isProduct) && $isProduct) ? 'Ürün Kodu' : 'Hizmet Kodu' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fal fa-barcode"></i></span>
                                                <input type="text" class="form-control" name="code" id="productCodeInput">
                                                <button type="button" class="btn btn-outline-secondary" id="generateProductCodeBtn" tabindex="-1" title="Otomatik Kod Üret">
                                                    <i class="fal fa-random"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Sağ Kolon: Fiyatlandırma ve Diğer Ayarlar -->
                        <div class="col-md-6">
                            <div class="panel mb-3">
                                <div class="panel-hdr">
                                    <h2><i class="fal fa-money-bill mr-2"></i>Fiyatlandırma</h2>
                                </div>
                                <div class="panel-container show">
                                    <div class="panel-content">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">₺</span>
                                            <input type="number" class="form-control" name="price" step="0.01" id="productPrice" placeholder="Fiyatı Giriniz" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label">Vergi Oranı (%)</label>
                                            <select class="custom-select" name="tax_rate" id="taxRate" required>
                                                @foreach ($tax_rates as $tax_rate)
                                                    <option value="{{ $tax_rate->rate }}">%{{ $tax_rate->rate }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Vergi Tutarı</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₺</span>
                                                <input type="number" class="form-control" name="tax_amount" id="taxAmount" step="0.01" value="0.00" readonly>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Toplam Fiyat</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₺</span>
                                                <input type="number" class="form-control" name="total_price" step="0.01" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if(isset($isProduct) && $isProduct)
                    <!-- Stok Bilgileri - Ayrı Card (12 Kolon) -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0"><i class="fal fa-box mr-2"></i>Stok Bilgileri</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" class="form-check-input" id="isStock" name="is_stock" checked>
                                        <label class="form-check-label" for="isStock">Stoklu Ürün</label>
                                    </div>
                                    <div id="stockManagement" class="mt-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Stok Miktarı</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fal fa-box"></i></span>
                                                        <input type="number" class="form-control" name="stock">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Birim</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fal fa-box"></i></span>
                                                        <select class="custom-select" name="unit_id">
                                                            @foreach($units as $unit)
                                                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Depo Seçimi <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fal fa-warehouse"></i></span>
                                                        <select class="custom-select" name="warehouse_id" required>
                                                            <option value="">Depo Seçiniz</option>
                                                            @foreach($warehouses as $warehouse)
                                                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <small class="form-text text-muted">Stoklu ürünler için depo seçimi zorunludur.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fal fa-times me-2"></i> Kapat
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fal fa-check me-2"></i> Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const taxRateElement = document.getElementById('taxRate');
    const productPriceElement = document.getElementById('productPrice');
    const taxAmountElement = document.getElementById('taxAmount');
    const totalPriceElement = document.querySelector('input[name="total_price"]');

    function calculateTaxAndTotal() {
        const price = parseFloat(productPriceElement.value);
        const taxRate = parseFloat(taxRateElement.value);

        if (!isNaN(price) && !isNaN(taxRate)) {
            const taxAmount = (price * taxRate) / 100;
            taxAmountElement.value = taxAmount.toFixed(2);

            const totalPrice = price + taxAmount;
            totalPriceElement.value = totalPrice.toFixed(2);
        } else {
            taxAmountElement.value = '0.00';
            totalPriceElement.value = '0.00';
        }
    }

    if (taxRateElement && productPriceElement) {
        taxRateElement.addEventListener('change', function() {
            calculateTaxAndTotal();
        });
        productPriceElement.addEventListener('input', calculateTaxAndTotal);
        calculateTaxAndTotal();
    }

    // Stok yönetimi için checkbox kontrolü (sadece ürünlerde var)
    const isStockCheckbox = document.getElementById('isStock');
    const stockManagement = document.getElementById('stockManagement');
    const warehouseSelect = document.querySelector('select[name="warehouse_id"]');
    if (isStockCheckbox && stockManagement && warehouseSelect) {
        function toggleStockManagement() {
            if (isStockCheckbox.checked) {
                stockManagement.style.display = 'block';
                warehouseSelect.setAttribute('required', 'required');
            } else {
                stockManagement.style.display = 'none';
                warehouseSelect.removeAttribute('required');
                warehouseSelect.value = '';
            }
        }
        isStockCheckbox.addEventListener('change', toggleStockManagement);
        toggleStockManagement();
        // Form submit validation
        const form = document.querySelector('#addServiceModal form');
        form.addEventListener('submit', function(e) {
            if (isStockCheckbox.checked && !warehouseSelect.value) {
                e.preventDefault();
                alert('Stoklu ürünler için depo seçimi zorunludur!');
                warehouseSelect.focus();
                return false;
            }
        });
    }

    // Ürün kodu otomatik üretme
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

    // Yeni kategori ekleme fonksiyonu (başka modallardan çağrılabilir)
    window.addCategoryToServiceSelect = function(category) {
        // category: {id, name}
        var select = document.querySelector('select[name="category_id"]');
        if (select && !select.querySelector('option[value="' + category.id + '"]')) {
            var option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            select.appendChild(option);
            // İsterseniz yeni eklenen kategoriyi otomatik seçili yapabilirsiniz:
            select.value = category.id;
        }
    };
});
</script>

<style>
    .form-control:focus,
    .custom-select:focus {
        box-shadow: none;
        border-color: #0d6efd;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .modal-content {
        border-radius: 15px;
    }

    .btn {
        padding: 8px 20px;
        border-radius: 8px;
    }

    .form-control,
    .custom-select {
        padding: 10px 15px;
        border-radius: 8px;
    }
</style>
