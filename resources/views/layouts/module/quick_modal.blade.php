<!---Hızlı Satış Ekle ---->
<div class="modal fade" id="quickModalQuickSale" tabindex="-1" aria-labelledby="quickModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title" id="saleModalLabel">
                    <i class="fal fa-cart-plus me-2"></i> Hızlı Satış Ekle
                </h5>
                        <button type="button" class="btn btn-sm text-white" data-bs-dismiss="modal" aria-label="Kapat" style="border: none; background: none;">
            <i class="fal fa-times"></i>
        </button>
            </div>
            <form id="saleForm" action="{{ route('quick_sale.add') }}" method="post">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="subtotal" id="hiddenSubtotal">
                    <input type="hidden" name="discount_total" id="hiddenDiscountTotal">
                    <input type="hidden" name="total" id="hiddenTotal">
                    <input type="hidden" name="new_customer_value" id="newCustomerValue">
                    <!-- Müşteri Seçimi -->
                    <div class="mb-4" id="customerSelect">
                        <label class="form-label fw-bold mb-2">
                            Müşteri Seç
                        </label>
                        <div class="position-relative">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fal fa-search text-primary"></i>
                                </span>
                                <input type="text" id="searchCustomer"
                                    class="form-control form-control-lg border-start-0 ps-0"
                                    placeholder="Müşteri ara veya yeni müşteri ekle..." autocomplete="off">
                                <input type="hidden" id="selectedCustomerId" name="customer_id">
                            </div>
                            <div id="customerList"
                                class="customer-results shadow-lg rounded-3 border-0 position-absolute w-100 bg-white mt-1"
                                style="display: none; z-index: 1050;">
                                <div class="results-container p-3">
                                    <!-- Arama sonuçları buraya gelecek -->
                                </div>
                                <div class="p-3 border-top bg-light">
                                    <button type="button" id="newCustomerBtn"
                                        class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
                                        <i class="fal fa-plus"></i>
                                        <span>Yeni Müşteri Ekle</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row newCustomerInformation" style="display: none;">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Müşteri Adı
                            </label>
                            <input type="text" class="form-control" name="customer_name" id="customerName">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Müşteri Soyadı
                            </label>
                            <input type="text" class="form-control" name="customer_last_name" id="customerLastName">
                        </div>
                        <div class="col-12 mb-4">
                            <label class="form-label fw-bold">
                                Müşteri Telefon
                            </label>
                            <input type="tel" class="form-control" name="customer_phone" id="customerPhone">
                        </div>
                    </div>


                    <!-- Ürün Seçimi -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Ürün Seç
                        </label>
                        <div id="productContainer">
                            <div class="product-slot d-flex align-items-center gap-3 mb-2">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fal fa-box text-primary"></i>
                                    </span>
                                    <select class="custom-select custom-select-lg border-start-0 ps-0 product-select"
                                        name="product_id[]">
                                        <option value="">Ürün Seçiniz</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-danger btn-lg px-4 remove-product">
                                    <i class="fal fa-minus"></i>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Ödeme Bilgileri -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    Ödeme Yöntemi
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fal fa-credit-card text-primary"></i>
                                    </span>
                                    <select class="custom-select custom-select-lg border-start-0 ps-0"
                                        name="payment_method">
                                        @foreach($payment_methods as $method)
                                            <option value="{{ $method['id'] }}">{{ $method['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    İndirim Türü
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fal fa-percent text-primary"></i>
                                    </span>
                                    <select class="custom-select custom-select-lg border-start-0 ps-0"
                                        name="discount_type" id="discountType">
                                        <option value="no_discount">İndirim Yok</option>
                                        <option value="percentage">Yüzde İndirim</option>
                                        <option value="fixed">Sabit İndirim</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="discountInputContainer" style="display: none;">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">
                                    İndirim Miktarı
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fal fa-tag text-primary"></i>
                                    </span>
                                    <input type="number" class="form-control form-control-lg border-start-0 ps-0"
                                        name="discount_amount" id="discountAmount" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Toplam Tutar -->
                    <div class="mb-3">
                        <div class="total-amount-container">
                            <div class="d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Ara Toplam</span>
                                    <span class="fw-bold">₺<span id="subtotalAmount">0.00</span></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2" id="discountRow"
                                    style="display: none !important;">
                                    <span class="text-muted">İndirim</span>
                                    <span class="fw-bold text-danger">-₺<span
                                            id="discountAmountDisplay">0.00</span></span>
                                </div>
                                <div class="border-top pt-2 mt-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fs-5 fw-bold text-dark">Genel Toplam</span>
                                            <div class="text-muted small">KDV Dahil</div>
                                        </div>
                                        <div class="text-end">
                                            <span class="fs-3 fw-bold text-primary">₺<span
                                                    id="totalAmount">0.00</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 bg-light rounded-bottom">
                        <button type="button" class="btn btn-lg btn-light px-4" data-bs-dismiss="modal">
                            İptal
                        </button>
                        <button type="button" id="addProduct" class="btn btn-primary btn-lg px-4 mt-2">
                            Yeni Ürün Ekle
                        </button>
                        <button type="submit" id="addSale" class="btn btn-lg btn-primary px-4">
                            Satışı Tamamla
                        </button>
                    </div>
            </div>
        
        </div>
    </div>
</form>


    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        $(document).ready(function() {
            let searchTimeout;

            // Müşteri arama
            $('#searchCustomer').on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val();

                if (searchTerm.length < 2) {
                    $('#customerList').hide();
                    return;
                }

                searchTimeout = setTimeout(() => {
                    axios.get('/ajax/get-customers', {
                            params: {
                                search: searchTerm
                            }
                        })
                        .then(response => {
                            const resultsContainer = $('#customerList .results-container');
                            resultsContainer.empty();

                            response.data.data.forEach(customer => {
                                resultsContainer.append(`
                        <div class="customer-item p-3 border-bottom cursor-pointer hover-bg-light" data-customer-id="${customer.id}">
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="fal fa-user-circle fa-lg text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark mb-1"> ${customer.first_name} ${customer.last_name}</div>
                                    <div class="d-flex gap-3 text-muted small">
                                        <span><i class="fal fa-phone me-1"></i> ${customer.phone}</span>
                                    </div>
                                    <div class="d-flex gap-3 text-muted small">
                                        <span><i class="fal fa-envelope me-1"></i> ${customer.email}</span>
                                    </div>
                                     <div class="d-flex gap-3 text-muted small">
                                        <span><i class="fal fa-star me-1"></i> ${customer.parapuan}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                            });

                            $('#customerList').show();
                        })
                        .catch(error => {
                            console.error('Müşteri arama hatası:', error);
                            toastr.error('Müşteri arama sırasında bir hata oluştu');
                        });
                }, 300);
            });

            // Müşteri seçimi
            $(document).on('click', '.customer-item', function() {
                const customerId = $(this).data('customer-id');
                const customerName = $(this).find('.fw-bold').text();

                $('#selectedCustomerId').val(customerId);
                $('#searchCustomer').val(customerName);
                $('#customerList').hide();
            });

            // Döküman tıklaması ile sonuç listesini gizle
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#customerList, #searchCustomer').length) {
                    $('#customerList').hide();
                }
            });

            // Ürünleri yükle
            function loadProducts(selectElement) {
                axios.get('/ajax/get-services')
                    .then(response => {
                        const products = response.data;
                        products.forEach(product => {
                            $(selectElement).append(`
                        <option value="${product.id}" data-price="${product.total_price}">
                            ${product.name} - ${product.code} (₺${product.total_price})
                        </option>
                    `);
                        });
                    })
                    .catch(error => {
                        console.error('Ürün yükleme hatası:', error);
                        toastr.error('Ürünler yüklenirken bir hata oluştu');
                    });
            }

            // İlk ürün select'ini yükle
            loadProducts('.product-select');

            // Yeni ürün slot'u ekle
            $('#addProduct').on('click', function() {
                const newSlot = `
            <div class="product-slot d-flex align-items-center gap-3 mb-2">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fal fa-box text-primary"></i>
                    </span>
                    <select class="custom-select custom-select-lg border-start-0 ps-0 product-select" name="product_id[]">
                        <option value="">Ürün Seçiniz</option>
                    </select>
                </div>
                <button type="button" class="btn btn-danger btn-lg px-4 remove-product">
                    <i class="fal fa-minus"></i>
                </button>
            </div>
        `;
                $('#productContainer').append(newSlot);
                loadProducts('#productContainer .product-slot:last-child .product-select');
            });

            // Ürün slot'unu kaldır
            $(document).on('click', '.remove-product', function() {
                $(this).closest('.product-slot').remove();
                calculateTotal();
            });

            // İndirim işlemleri
            $('#discountType').on('change', function() {
                const discountType = $(this).val();
                const discountContainer = $('#discountInputContainer');
                const discountSymbol = $('#discountSymbol');

                if (discountType === 'no_discount') {
                    discountContainer.hide();
                    $('#discountAmount').val('');
                    calculateTotal();
                } else {
                    discountContainer.show();
                    if (discountType === 'percentage') {
                        discountSymbol.html('%');
                        $('#discountAmount').attr('max', '100');
                    } else {
                        discountSymbol.html('₺');
                        $('#discountAmount').removeAttr('max');
                    }
                }
            });

            $('#discountAmount').on('input', calculateTotal);

            // Toplam tutar hesaplama fonksiyonunu güncelle
            function calculateTotal() {
                let subtotal = 0;
                $('.product-slot').each(function() {
                    const selectedOption = $(this).find('.product-select option:selected');
                    const price = selectedOption.length ? parseFloat(selectedOption.data('price')) || 0 : 0;
                    subtotal += price;
                });

                let discountAmount = 0;
                const discountType = $('#discountType').val();
                const discountValue = parseFloat($('#discountAmount').val()) || 0;

                if (discountType === 'percentage' && discountValue > 0) {
                    discountAmount = (subtotal * (discountValue / 100));
                } else if (discountType === 'fixed' && discountValue > 0) {
                    discountAmount = discountValue;
                }

                const total = Math.max(subtotal - discountAmount, 0);

                // Görünen değerleri güncelle
                $('#subtotalAmount').text(subtotal.toFixed(2));

                if (discountAmount > 0) {
                    $('#discountRow').show();
                    $('#discountAmountDisplay').text(discountAmount.toFixed(2));
                } else {
                    $('#discountRow').hide();
                }

                $('#totalAmount').text(isNaN(total) ? '0.00' : total.toFixed(2));

                // Gizli input değerlerini güncelle
                $('#hiddenSubtotal').val(subtotal.toFixed(2));
                $('#hiddenDiscountTotal').val(discountAmount.toFixed(2));
                $('#hiddenTotal').val(total.toFixed(2));
            }

            // Ürün değiştiğinde toplam tutarı güncelle
            $(document).on('change', '.product-select', calculateTotal);


            // Satış tamamla butonuna tıklandığında formu gönder
            $('#addSale').on('click', function() {
                $('#saleForm').submit();
            });
        });
    </script>

    <style>
        /* Modal Genel Stilleri */
        .modal-content {
            border-radius: 1rem;
            overflow: hidden;
        }

        .modal-header {
            padding: 1.5rem;
            background: linear-gradient(45deg, var(--bs-primary), #2b88d9);
        }

        .modal-body {
            padding: 2rem;
        }

        /* Müşteri Arama Bölümü */
        .customer-results {
            max-height: 400px;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .customer-item {
            transition: all 0.2s ease;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .customer-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
        }

        /* Ürün Seçimi Bölümü */
        .product-slot {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
        }

        .product-slot:hover {
            background-color: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }


        .remove-product {
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .remove-product:hover {
            background-color: #dc3545;
            transform: scale(1.05);
        }

        /* Butonlar */
        .btn {
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            box-shadow: 0 4px 15px rgba(var(--bs-primary-rgb), 0.3);
        }

        /* Toplam Tutar Bölümü */
        .total-amount-container {
            background: linear-gradient(45deg, #f8f9fa, #ffffff);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        /* Input Group Stilleri */
        .input-group-text {
            border-radius: 0.5rem 0 0 0.5rem;
            width: 3rem;
            justify-content: center;
        }

        .form-control,
        .custom-select {
            border-radius: 0 0.5rem 0.5rem 0;
        }

        /* Animasyonlar */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal.show .modal-dialog {
            animation: fadeIn 0.3s ease;
        }

        /* Scrollbar Stilleri */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Responsive Düzenlemeler */
        @media (max-width: 768px) {
            .modal-body {
                padding: 1rem;
            }

            .product-slot {
                flex-direction: column;
                gap: 1rem;
            }

        }
    </style>


    <script>
        $('#newCustomerBtn').on('click', function() {
            $('.newCustomerInformation').show();
            $('.customer-results').hide();
            $('#searchCustomer').val('');
            $('#customerList').hide();
            $('#customerSelect').hide();
            $('#newCustomerValue').val('1');
        });
    </script>
