@include('layouts.header')
@include('layouts.module.employee_add_modal')

<style>
.ck-editor__editable {
    min-height: 200px !important;
}
.ck.ck-editor {
    width: 100%;
}
.ck-content {
    font-size: 14px;
    line-height: 1.6;
}

/* Kategori dropdown stilleri */
.category-dropdown {
    position: relative;
}
.category-dropdown .dropdown-menu {
    max-height: 450px;
    overflow-y: auto;
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
.category-dropdown .dropdown-menu.show {
    display: block;
}
.category-dropdown .category-search {
    border: 1px solid #dee2e6;
}
.category-dropdown .category-search:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    outline: none;
}
.category-dropdown .category-option {
    display: block;
    padding: 0.5rem 1rem;
    color: #212529;
    text-decoration: none;
    cursor: pointer;
}
.category-dropdown .category-option:hover {
    background-color: #f8f9fa;
    color: #212529;
    text-decoration: none;
}
.category-dropdown .category-button {
    text-align: left;
    justify-content: space-between;
    display: flex;
    align-items: center;
    position: relative;
}
.category-dropdown .category-button::after {
    margin-left: auto;
    content: "▼";
    font-size: 0.8em;
}
</style>



<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel shadow-sm">
                <div class="panel-hdr bg-primary-700 text-white">
                    <h2><i class="fal fa-money-bill-alt mr-2"></i>Gider Detayları</h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-panel hover-effect-dot waves-effect waves-themed"
                            data-action="panel-collapse"></button>
                        <button class="btn btn-panel hover-effect-dot waves-effect waves-themed"
                            data-action="panel-fullscreen"></button>
                    </div>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        <form id="expenseForm" action="{{ route('expenses.add') }}" method="POST"
                            class="needs-validation" novalidate enctype="multipart/form-data">
                            @csrf

                            <!-- Form grupları için card yapısı -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fal fa-info-circle mr-2"></i>Temel Bilgiler</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Temel Bilgiler -->
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label  for="expense_date">
                                                TARİH <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="fal fa-calendar"></i></span>
                                                </div>
                                                <input type="date" class="form-control" name="date"
                                                    id="expense_date" value="{{ date('Y-m-d') }}" required>
                                                <div class="invalid-feedback">Tarih seçimi zorunludur!</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label  for="expense_type">
                                                GİDER TİPİ <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-tag"></i></span>
                                                </div>
                                                <select class="form-control custom-select" id="expense_type"
                                                    name="expense_type_id" required>
                                                    @foreach ($expense_types as $expense_type)
                                                        <option value="{{ $expense_type->id }}">
                                                            {{ $expense_type->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Gider tipi seçimi zorunludur!</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label  for="bank_account">
                                                BANKA / KASA HESABI <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="fal fa-university"></i></span>
                                                </div>
                                                <select class="form-control custom-select" id="bank_account" name="account_id"
                                                    required>
                                                    <option value="">Seçiniz...</option>
                                                    @foreach ($accounts as $account)
                                                        <option value="{{ $account->id }}">{{ $account->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Hesap seçimi zorunludur!</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Belge ve Personel Bilgileri -->
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label  for="expense_number">
                                                BELGE NO <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="fal fa-file-alt"></i></span>
                                                </div>
                                                <input type="text" class="form-control" name="expense_number"
                                                    id="expense_number" value="{{ $expense_id }}" required readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3" id="employeeSelectContainer" style="display: none;">
                                            <label  for="employee">
                                                PERSONEL <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-user"></i></span>
                                                </div>
                                                <select class="form-control custom-select" id="employee"
                                                    name="employee_id">
                                                    <option value="">Seçiniz...</option>
                                                    @foreach ($employees as $employee)
                                                        <option value="{{ $employee->id }}">{{ $employee->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Personel seçimi zorunludur!</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label  for="customer">
                                                MÜŞTERİ (CRM)
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-users"></i></span>
                                                </div>
                                                <select class="form-control custom-select" id="customer"
                                                    name="customer_id">
                                                    <option value="">Müşteri seçiniz (opsiyonel)</option>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}">
                                                            {{ $customer->title ?? $customer->code ?? 'Müşteri' }} 
                                                            @if($customer->current_balance > 0)
                                                                (Alacağım: ₺{{ number_format($customer->current_balance, 2) }})
                                                            @elseif($customer->current_balance < 0)
                                                                (Borç: ₺{{ number_format(abs($customer->current_balance), 2) }})
                                                            @else
                                                                (Dengeli)
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <small class="form-text text-muted">
                                                <i class="fal fa-info-circle mr-1"></i>
                                                Müşteri seçilirse gider müşteri hesap hareketlerine yansır
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gider kalemleri için yeni tasarım -->
                            <div class="card mb-4">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fal fa-table mr-2"></i>Gider Kalemleri</h5>
                                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-themed"
                                        id="addNewExpenseRow">
                                        <i class="fal fa-plus mr-1"></i> Yeni Kalem Ekle
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped w-100">
                                            <thead class="bg-primary-600">
                                                <tr>
                                                    <th>KATEGORİ</th>
                                                    <th>GİDER ADI</th>
                                                    <th>BİRİM FİYAT</th>
                                                    <th>MİKTAR</th>
                                                    <th>TOPLAM</th>
                                                    <th>AÇIKLAMA</th>
                                                    <th class="text-center" style="width: 120px;">İŞLEM</th>
                                                </tr>
                                            </thead>
                                            <tbody id="expenseItems">
                                                <tr>
                                                    <td>
                                                        <div class="dropdown category-dropdown">
                                                            <input type="hidden" name="expense_category_id[]" class="category-value" required>
                                                            <button class="btn btn-outline-secondary dropdown-toggle category-button w-100" type="button">
                                                                <span class="category-text">Seçiniz...</span>
                                                            </button>
                                                            <div class="dropdown-menu w-100 category-menu">
                                                                <div class="px-3 py-2">
                                                                    <input type="text" class="form-control form-control-sm category-search" placeholder="Kategori ara...">
                                                                </div>
                                                                <div class="dropdown-divider"></div>
                                                                <div class="category-options">
                                                                    @foreach ($expenses_categories as $category)
                                                                        <a class="dropdown-item category-option" href="#" data-value="{{ $category->id }}">
                                                                            {{ $category->name }}
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control item-name-input" name="expense[]"
                                                            required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" min="0" 
                                                            class="form-control unit-price-input" name="unit_price[]"
                                                            placeholder="0.00" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" min="0.01" 
                                                            class="form-control quantity-input" name="quantity[]"
                                                            value="1" placeholder="1" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" min="0" readonly
                                                            class="form-control total-amount" name="amount[]"
                                                            placeholder="0.00" style="background-color: #f8f9fa;">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="description[]">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm btn-icon waves-effect waves-themed deleteExpenseItemBtn">
                                                            <i class="fal fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="2" class="text-right"><strong>TOPLAM:</strong>
                                                    </td>
                                                    <td colspan="5">
                                                        <strong id="totalAmount">0.00 TL</strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Not Alanı -->
                            <div class="row mt-4">
                                <div class="col-md-12 mb-3">
                                    <label  for="expense_note">Not (Opsiyonel)</label>
                                    <textarea class="form-control" name="note" id="expense_note" rows="6" style="min-height: 150px;"></textarea>
                                </div>
                            </div>

                            <!---Fatura Fotograafı yükleme alanı--->
                            <div class="row mt-4">
                                <div class="col-md-12 mb-3">
                                    <label  for="invoice_photo">Fatura Fotoğrafı (Opsiyonel)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fal fa-file-image"></i></span>
                                        </div>
                                        <input type="file" class="form-control" name="invoice_photo" id="invoice_photo" accept="image/*">
                                    </div>
                                    <small class="form-text text-muted">Yalnızca resim dosyaları yüklenebilir. Maksimum boyut: 5MB.</small>
                                </div>
                            </div>

                            <!-- Form butonları için yeni tasarım -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <button type="submit" id="submitBtn"
                                                class="btn btn-lg btn-primary waves-effect waves-themed px-5">
                                                <i class="fal fa-check mr-1"></i> Kaydet
                                            </button>
                                            <button type="button"
                                                class="btn btn-lg btn-outline-danger waves-effect waves-themed px-5 resetBtn ml-2">
                                                <i class="fal fa-undo mr-1"></i> Sıfırla
                                            </button>
                                        </div>
                                        <div>
                                            <a href="{{ route('expenses.list') }}" 
                                               class="btn btn-lg btn-outline-info waves-effect waves-themed px-5">
                                                <i class="fal fa-list mr-1"></i> Gider Listesi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Gerekli JS Dosyaları -->

@include('layouts.footer')

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
    // Debounce function for performance optimization
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    $(document).ready(function() {
        // Laravel flash messages
        @if(session('success'))
            showSuccess('{{ session('success') }}');
        @endif
        
        @if(session('error'))
            showError('{{ session('error') }}');
        @endif

        @if($errors->any())
            showError('{{ implode("<br>", $errors->all()) }}');
        @endif

        // CKEditor 5'i başlat
        ClassicEditor
            .create(document.querySelector('#expense_note'), {
                toolbar: ['undo', 'redo', '|', 'heading', '|', 'bold', 'italic', '|', 
                         'link', 'insertTable', '|', 'bulletedList', 'numberedList'],
                language: 'tr'
            })
            .then(editor => {
                window.expenseNoteEditor = editor;
            })
            .catch(error => {
                // CKEditor initialization failed, fallback to textarea
                $('#expense_note').show();
            });

        // Calculate individual line item total
        function calculateLineTotal(row) {
            const unitPrice = parseFloat($(row).find('.unit-price-input').val()) || 0;
            const quantity = parseFloat($(row).find('.quantity-input').val()) || 0;
            const total = unitPrice * quantity;
            $(row).find('.total-amount').val(total.toFixed(2));
            return total;
        }

        // Toplam tutarı güncelle - optimized
        function updateTotal() {
            let total = 0;
            $('#expenseItems tr').each(function() {
                total += calculateLineTotal($(this));
            });
            $('#totalAmount').text(`${total.toFixed(2)} TL`);
        }

        // Yeni kalem ekle
        $('#addNewExpenseRow').on('click', function() {
            const html = `
            <tr>
                <td>
                    <div class="dropdown category-dropdown">
                        <input type="hidden" name="expense_category_id[]" class="category-value" required>
                        <button class="btn btn-outline-secondary dropdown-toggle category-button w-100" type="button">
                            <span class="category-text">Seçiniz...</span>
                        </button>
                        <div class="dropdown-menu w-100 category-menu">
                            <div class="px-3 py-2">
                                <input type="text" class="form-control form-control-sm category-search" placeholder="Kategori ara...">
                            </div>
                            <div class="dropdown-divider"></div>
                            <div class="category-options">
                                @foreach ($expenses_categories as $category)
                                    <a class="dropdown-item category-option" href="#" data-value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </td>
                <td><input type="text" class="form-control item-name-input" name="expense[]" required></td>
                <td><input type="number" step="0.01" min="0" class="form-control unit-price-input" name="unit_price[]" placeholder="0.00" required></td>
                <td><input type="number" step="0.01" min="0.01" class="form-control quantity-input" name="quantity[]" value="1" placeholder="1" required></td>
                <td><input type="number" step="0.01" min="0" readonly class="form-control total-amount" name="amount[]" placeholder="0.00" style="background-color: #f8f9fa;"></td>
                <td><input type="text" class="form-control" name="description[]"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm deleteExpenseItemBtn">
                        <i class="fal fa-trash"></i>
                    </button>
                </td>
            </tr>`;
            $('#expenseItems').append(html);
        });

        // Satır sil
        $(document).on('click', '.deleteExpenseItemBtn', function() {
            if ($('#expenseItems tr').length > 1) {
                $(this).closest('tr').remove();
                updateTotal();
            } else {
                showError('En az bir gider kalemi olmalıdır!');
            }
        });

        // Unit price ve quantity değişimi - debounced for performance
        $(document).on('input', '.unit-price-input, .quantity-input', function() {
            calculateLineTotal($(this).closest('tr'));
            updateTotal();
        });

        // Form sıfırla
        $('.resetBtn').click(function() {
            showConfirm('Form içeriği sıfırlanacak. Emin misiniz?').then((result) => {
                if (result.isConfirmed) {
                    $('#expenseForm')[0].reset();
                    if (window.expenseNoteEditor) {
                        window.expenseNoteEditor.setData('');
                    }
                    $('.custom-select').val('');
                    resetCategories();
                    updateTotal();
                    showSuccess('Form başarıyla sıfırlandı');
                }
            });
        });

        // Form validasyonu ve gönderim
        $('#expenseForm').on('submit', function(e) {
            const form = this;
            
            // Gerekli alanları kontrol et
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                $(form).addClass('was-validated');
                return false;
            }

            // En az bir gider kalemi kontrolü
            if ($('#expenseItems tr').length < 1) {
                e.preventDefault();
                showError('En az bir gider kalemi eklemelisiniz!');
                return false;
            }

            // Kategori seçimi kontrolü
            let categoryError = false;
            $('.category-value').each(function() {
                if (!$(this).val()) {
                    categoryError = true;
                    return false;
                }
            });

            if (categoryError) {
                e.preventDefault();
                showError('Lütfen tüm gider kalemleri için kategori seçiniz!');
                return false;
            }

            // Unit price ve quantity kontrolü
            let priceError = false;
            $('.unit-price-input').each(function() {
                if (!$(this).val() || parseFloat($(this).val()) <= 0) {
                    priceError = true;
                    return false;
                }
            });

            if (priceError) {
                e.preventDefault();
                showError('Lütfen tüm gider kalemleri için geçerli birim fiyat giriniz!');
                return false;
            }

            // Submit butonunu değiştir
            $('#submitBtn').prop('disabled', true)
                          .html('<i class="fal fa-spinner fa-spin mr-1"></i> Kaydediliyor...');
        });

        // Personel alanı göster/gizle
        $('#expense_type').on('change', function() {
            const selectedType = $(this).val();
            const employeeContainer = $('#employeeSelectContainer');
            const employeeSelect = $('#employee');

            if (selectedType == 2) {
                employeeContainer.show();
                employeeSelect.prop('required', true);
            } else {
                employeeContainer.hide();
                employeeSelect.prop('required', false).val('');
            }
        });

        // Kategori dropdown fonksiyonları
        function initCategoryDropdown() {
            // Kategori seçimi
            $(document).on('click', '.category-option', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const value = $(this).data('value');
                const text = $(this).text().trim();
                const dropdown = $(this).closest('.category-dropdown');
                
                dropdown.find('.category-value').val(value);
                dropdown.find('.category-text').text(text);
                dropdown.find('.dropdown-menu').removeClass('show');
            });

            // Arama fonksiyonu - hem input hem keyup event'leri için
            function performSearch(searchInput) {
                const searchTerm = searchInput.val().toLowerCase().trim();
                const dropdown = searchInput.closest('.category-dropdown');
                const options = dropdown.find('.category-option');
                
                if (searchTerm === '') {
                    // Arama boşsa tüm seçenekleri göster
                    options.show();
                } else {
                    // Arama terimi varsa filtrele
                    options.each(function() {
                        const text = $(this).text().toLowerCase().trim();
                        if (text.includes(searchTerm)) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
            }

            // Input event'i
            $(document).on('input', '.category-search', debounce(function(e) {
                e.stopPropagation();
                performSearch($(this));
            }, 200));

            // Keyup event'i (alternatif)
            $(document).on('keyup', '.category-search', function(e) {
                e.stopPropagation();
                performSearch($(this));
            });


            // Arama inputuna tıklandığında dropdown kapanmasını engelle
            $(document).on('click', '.category-search', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });

            // Dropdown menüsüne tıklandığında kapanmasını engelle
            $(document).on('click', '.category-menu', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });

            // Dropdown dışına tıklandığında kapat
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.category-dropdown').length) {
                    $('.category-dropdown .dropdown-menu').removeClass('show');
                }
            });

            // Dropdown butonuna tıklandığında toggle
            $(document).on('click', '.category-button', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const dropdown = $(this).closest('.category-dropdown');
                const menu = dropdown.find('.dropdown-menu');
                
                // Diğer dropdown'ları kapat
                $('.category-dropdown .dropdown-menu').not(menu).removeClass('show');
                
                // Bu dropdown'ı toggle et
                menu.toggleClass('show');
                
                // Dropdown açılıyorsa arama inputunu temizle ve focus yap
                if (menu.hasClass('show')) {
                    setTimeout(() => {
                        dropdown.find('.category-search').val('').focus();
                        dropdown.find('.category-option').show();
                    }, 10);
                }
            });
        }

        // Form sıfırlandığında kategorileri de sıfırla
        function resetCategories() {
            $('.category-dropdown').each(function() {
                $(this).find('.category-value').val('');
                $(this).find('.category-text').text('Seçiniz...');
            });
        }

        // Kategori dropdown'ları başlat
        initCategoryDropdown();

        // Memory cleanup on page unload
        $(window).on('beforeunload', function() {
            // Destroy CKEditor
            if (window.expenseNoteEditor) {
                try {
                    window.expenseNoteEditor.destroy();
                } catch (error) {
                    // Silent fail
                }
            }
            
            // Clear any timers
            if (window.debounceTimeouts) {
                Object.values(window.debounceTimeouts).forEach(clearTimeout);
            }
        });
    });
</script>
