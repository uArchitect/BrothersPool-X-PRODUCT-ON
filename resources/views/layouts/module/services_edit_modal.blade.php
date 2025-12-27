@foreach($services as $service)
<div class="modal fade static-modal" id="editServiceModal{{ $service->id }}" tabindex="-1" aria-labelledby="editServiceModalLabel{{ $service->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4"><i class="fal fa-edit me-2"></i>{{ (isset($isProduct) && $isProduct) ? 'Ürün Düzenle' : 'Hizmet Düzenle' }}</h5>
                <button type="button" class="btn btn-sm text-white" data-bs-dismiss="modal" aria-label="Kapat" style="border: none; background: none;">
            <i class="fal fa-times"></i>
        </button>
            </div>
            <form action="{{ route('services.update') }}" method="POST" enctype="multipart/form-data" class="edit-service-form">
                @csrf
                <input type="hidden" name="id" value="{{ $service->id }}">
                <div class="modal-body">
                    <div class="row">
                        <!-- Sol Kolon: Temel Bilgiler ve Stok (sadece ürünlerde) -->
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
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ $category->id == $service->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label">Ad <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" value="{{ $service->name }}" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label">Açıklama</label>
                                            <textarea class="form-control" name="description" rows="3">{{ $service->description }}</textarea>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{ (isset($isProduct) && $isProduct) ? 'Ürün Kodu' : 'Hizmet Kodu' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fal fa-barcode"></i></span>
                                                <input type="text" class="form-control" name="code" value="{{ $service->code }}" id="productCodeInputEdit{{ $service->id }}">
                                                <button type="button" class="btn btn-outline-secondary generateProductCodeBtnEdit" data-target="productCodeInputEdit{{ $service->id }}" tabindex="-1" title="Otomatik Kod Üret">
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
                                            <input type="number" class="form-control" name="price" step="0.01" id="productPriceEdit{{ $service->id }}" value="{{ $service->price }}" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label">Vergi Oranı (%)</label>
                                            <select class="custom-select" name="tax_rate" id="taxRateEdit{{ $service->id }}" required>
                                                @foreach ($tax_rates as $tax_rate)
                                                    <option value="{{ $tax_rate->rate }}" {{ $service->tax_rate == $tax_rate->rate ? 'selected' : '' }}>%{{ $tax_rate->rate }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Vergi Tutarı</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₺</span>
                                                <input type="number" class="form-control" name="tax_amount" id="taxAmountEdit{{ $service->id }}" step="0.01" value="{{ $service->tax_amount }}" readonly>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Toplam Fiyat</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₺</span>
                                                <input type="number" class="form-control" name="total_price" id="totalPriceEdit{{ $service->id }}" step="0.01" value="{{ $service->total_price }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($service->is_stock)
                    <!-- Stok Bilgileri - Ayrı Card (12 Kolon) -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0"><i class="fal fa-box mr-2"></i>Stok Bilgileri</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" class="form-check-input" id="isStock{{ $service->id }}" name="is_stock" checked>
                                        <label class="form-check-label" for="isStock{{ $service->id }}">Stoklu Ürün</label>
                                    </div>
                                    <div id="stockManagement{{ $service->id }}" class="mt-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Stok Miktarı</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fal fa-box"></i></span>
                                                        <input type="number" class="form-control" name="stock" value="{{ $service->stock ?? 0 }}">
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
                                                                <option value="{{ $unit->id }}" {{ $service->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
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
                                                                <option value="{{ $warehouse->id }}" {{ $service->warehouse_id == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
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
                        <i class="fal fa-check me-2"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const taxRateElement{{ $service->id }} = document.getElementById('taxRateEdit{{ $service->id }}');
        const productPriceElement{{ $service->id }} = document.getElementById('productPriceEdit{{ $service->id }}');
        const taxAmountElement{{ $service->id }} = document.getElementById('taxAmountEdit{{ $service->id }}');
        const totalPriceElement{{ $service->id }} = document.getElementById('totalPriceEdit{{ $service->id }}');

        function calculateTaxAndTotal{{ $service->id }}() {
            const price = parseFloat(productPriceElement{{ $service->id }}.value);
            const taxRate = parseFloat(taxRateElement{{ $service->id }}.value);

            if (!isNaN(price) && !isNaN(taxRate)) {
                const taxAmount = (price * taxRate) / 100;
                const totalPrice = price + taxAmount;
                
                taxAmountElement{{ $service->id }}.value = taxAmount.toFixed(2);
                totalPriceElement{{ $service->id }}.value = totalPrice.toFixed(2);
            } else {
                taxAmountElement{{ $service->id }}.value = '0.00';
                totalPriceElement{{ $service->id }}.value = '0.00';
            }
        }

        taxRateElement{{ $service->id }}.addEventListener('change', calculateTaxAndTotal{{ $service->id }});
        productPriceElement{{ $service->id }}.addEventListener('input', calculateTaxAndTotal{{ $service->id }});

        // Stok yönetimi için checkbox kontrolü
        const isStockCheckbox{{ $service->id }} = document.getElementById('isStock{{ $service->id }}');
        const stockManagement{{ $service->id }} = document.getElementById('stockManagement{{ $service->id }}');
        const warehouseSelect{{ $service->id }} = document.querySelector('#editServiceModal{{ $service->id }} select[name="warehouse_id"]');

        function toggleStockManagement{{ $service->id }}() {
            if (isStockCheckbox{{ $service->id }}.checked) {
                stockManagement{{ $service->id }}.style.display = 'block';
                // Depo seçimini zorunlu yap
                if (warehouseSelect{{ $service->id }}) {
                    warehouseSelect{{ $service->id }}.setAttribute('required', 'required');
                }
            } else {
                stockManagement{{ $service->id }}.style.display = 'none';
                // Depo seçimini zorunlu yapma
                if (warehouseSelect{{ $service->id }}) {
                    warehouseSelect{{ $service->id }}.removeAttribute('required');
                    warehouseSelect{{ $service->id }}.value = '';
                }
            }
        }

        isStockCheckbox{{ $service->id }}.addEventListener('change', toggleStockManagement{{ $service->id }});
        
        // Sayfa yüklendiğinde de kontrol et
        toggleStockManagement{{ $service->id }}();

        // Form submit validation - Removed to prevent double submission
        // Validation will be handled by the main services.blade.php file

        document.querySelectorAll('.generateProductCodeBtnEdit').forEach(function(btn) {
            btn.addEventListener('click', function() {
                let code = '';
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                for (let i = 0; i < 10; i++) {
                    code += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                const inputId = btn.getAttribute('data-target');
                const input = document.getElementById(inputId);
                if (input) input.value = code;
            });
        });

        // Yeni kategori ekleme fonksiyonu (başka modallardan çağrılabilir)
        window.addCategoryToServiceEditSelect = function(category) {
            // category: {id, name}
            document.querySelectorAll('select[name="category_id"]').forEach(function(select) {
                if (!select.querySelector('option[value="' + category.id + '"]')) {
                    var option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    select.appendChild(option);
                }
            });
        };
    });

    // Kapat butonu için olay dinleyicisi
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.closest('.modal').id;
            $(`#${modalId}`).modal('hide');
        });
    });
</script>
@endforeach

<style>
    .form-control:focus,
    .form-select:focus {
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
    .form-select {
        padding: 10px 15px;
        border-radius: 8px;
    }
</style>
