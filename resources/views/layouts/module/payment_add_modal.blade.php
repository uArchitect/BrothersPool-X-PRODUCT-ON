<style>
    #paymentAddModal select,
    #paymentAddModal input,
    #paymentAddModal textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 5px;
    }

    #paymentAddModal .form-label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }

    #paymentAddModal .form-group {
        margin-bottom: 15px;
    }

    #paymentAddModal textarea {
        min-height: 100px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<div class="modal fade" id="paymentAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title">
                    <i class="fal fa-plus-circle me-2"></i> Yeni Ödeme Ekle
                </h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Kapat" id="addCustomerModalModalClose">
                    <span aria-hidden="true" style="color:white;">×</span>
                </button>
            </div>

            <form action="{{ route('payment.installment') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <div class="modal-body p-4">
                    <div class="panel-tag mb-4">
                        <i class="fal fa-info-circle mr-1"></i> Bu sayfada müşterilerinizden gelen ödemeleri
                        ekleyebilirsiniz.
                    </div>

                    <input type="hidden" name="paymentType" value="installment">

                    <div class="form-group">
                        <label class="form-label" for="payment_account">
                            <i class="fal fa-cash-register mr-1"></i>
                            Hangi Kasaya Eklenecek
                            <span class="text-danger">*</span>
                        </label>
                        <select id="payment_account" name="paymentAccount" required>
                            <option value="1">Ana Kasa</option>
                        </select>
                        <div class="invalid-feedback">Lütfen bir kasa seçin.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="is_from_customer">
                            <i class="fal fa-user-circle mr-1"></i>
                            Müşteriden Mi Geldi?
                        </label>
                        <select id="is_from_customer" name="isFromCustomer">
                            <option value="0">Hayır</option>
                            <option value="1">Evet</option>
                        </select>
                        <div class="invalid-feedback">Lütfen bir seçenek seçin.</div>
                    </div>

                    <!-- Müşteri Seçimi ve İlgili İşlem -->
                    <div id="customer_fields" class="d-none">
                        <div class="form-group">
                            <label class="form-label" for="customer_id">
                                <i class="fal fa-user mr-1"></i>
                                Müşteri Seçin
                            </label>
                            <select id="customer_id" name="customer">
                                <option value="">Müşteri Seçin...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="customer_transaction">
                                <i class="fal fa-tags mr-1"></i>
                                İşlem Borçları
                            </label>
                            <select id="customer_transaction" name="customerTransaction">
                                <option value="">İşlem Türü Seçin...</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="payment_amount">
                            <i class="fal fa-money-bill-alt mr-1"></i>
                            Ödeme Miktarı (₺)
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" id="payment_amount" name="paymentAmount" required>
                        <div class="invalid-feedback">Lütfen ödeme miktarını girin.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="payment_type">
                            <i class="fal fa-credit-card mr-1"></i>
                            Ödeme Türü
                            <span class="text-danger">*</span>
                        </label>
                        <select id="payment_type" name="paymentType" required>
                            <option value="cash">Nakit</option>
                            <option value="credit_card">Kredi Kartı</option>
                            <option value="bank_transfer">Banka Transferi</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="payment_date">
                            <i class="fal fa-calendar-alt mr-1"></i>
                            Ödeme Tarihi
                            <span class="text-danger">*</span>
                        </label>
                        <input type="date" id="payment_date" name="paymentDate" required>
                        <div class="invalid-feedback">Lütfen ödeme tarihini seçin.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="payment_description">
                            <i class="fal fa-comment-alt mr-1"></i>
                            Açıklama
                        </label>
                        <textarea id="payment_description" name="paymentDescription" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 bg-light rounded-bottom">
                    <button type="button" class="btn btn-lg btn-light px-4" data-bs-dismiss="modal">
                        <i class="fal fa-times me-1"></i> İptal
                    </button>
                    <button type="submit" class="btn btn-lg btn-primary px-4">
                        <i class="fal fa-check me-1"></i> Ödeme Al
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
            const modal = document.getElementById('paymentAddModal');
            if (!modal) return;

            const isFromCustomer = modal.querySelector('#is_from_customer');
            const customerId = modal.querySelector('#customer_id');

            // Müşteri seçimi değişikliği
            if (isFromCustomer) {
                isFromCustomer.addEventListener('change', function() {
                    const customerFields = modal.querySelector('#customer_fields');
                    if (this.value === '1') {
                        customerFields.classList.remove('d-none');
                    } else {
                        customerFields.classList.add('d-none');
                        if (customerId) {
                            customerId.value = '';
                            customerId.dispatchEvent(new Event('change'));
                        }
                        const transactionSelect = modal.querySelector('#customer_transaction');
                        if (transactionSelect) {
                            transactionSelect.innerHTML = '';
                        }
                    }
                });
            }

            // Müşteri ID değişikliği
            if (customerId) {
                customerId.addEventListener('change', function() {
                    const customer_id = this.value;
                    if (customer_id) {
                        fetch('/ajax/getCustomerProcess/' + customer_id)
                            .then(response => response.json())
                            .then(function(data) {

                                const transactionSelect = modal.querySelector('#customer_transaction');
                                if (transactionSelect) {
                                    transactionSelect.innerHTML = '';
                                    data.forEach(function(item) {
                                        const option = document.createElement('option');
                                        option.value = item.sale_id;
                                        option.textContent = item.sale_date +
                                            " - Kalan Tutar: " + item.remaining_price + "₺";
                                        transactionSelect.appendChild(option);
                                    });
                                }
                            })
                            .catch(function(error) {
                                console.error('Müşteri işlemleri yüklenirken hata:', error);
                                if (typeof showError === 'function') {
                                    showError(
                                        'Müşteri işlemleri yüklenirken bir hata oluştu. Lütfen tekrar deneyin.'
                                    );
                                }
                            });
                    }
                });
            }

            // Modal açıldığında müşteri listesini yükle
            let isCustomerLoading = false;

            $(modal).on('show.bs.modal', function() {
                const customerSelect = modal.querySelector('#customer_id');
                const transactionSelect = modal.querySelector('#customer_transaction');
                if (customerSelect) {
                    // Yükleniyor mesajı göster
                    customerSelect.innerHTML = '<option value="">Yükleniyor...</option>';
                }
                if (transactionSelect) {
                    transactionSelect.innerHTML = '<option value="">İşlem Türü Seçin...</option>';
                }
                if (isCustomerLoading) return;
                isCustomerLoading = true;
                fetch('/ajax/getAllCustomers')
                    .then(response => response.json())
                    .then(function(data) {
                        if (customerSelect) {
                            if (!data || !data.length) {
                                customerSelect.innerHTML = '<option value="">Kayıtlı müşteri yok</option>';
                            } else {
                                customerSelect.innerHTML = '<option value="">Müşteri Seçin...</option>';
                                data.forEach(function(customer) {
                                    const option = document.createElement('option');
                                    option.value = customer.id;
                                    option.textContent = customer.first_name + " " + customer.last_name;
                                    customerSelect.appendChild(option);
                                });
                            }
                        }
                    })
                    .catch(function(error) {
                        if (customerSelect) {
                            customerSelect.innerHTML = '<option value="">Müşteri listesi yüklenemedi</option>';
                        }
                        console.error('Müşteri listesi yüklenirken hata:', error);
                        if (typeof showError === 'function') {
                            showError('Müşteri listesi yüklenirken bir hata oluştu. Lütfen tekrar deneyin.');
                        }
                    })
                    .finally(function() {
                        isCustomerLoading = false;
                    });
            });

        } catch (error) {
            console.warn('Payment modal initialization error:', error);
        }
    });
</script>
