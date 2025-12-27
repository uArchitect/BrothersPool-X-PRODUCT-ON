@include('layouts.header')

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

                <div class="panel-container show">
                    <div class="panel-content">
                        <form id="incomeForm" action="{{ route('incomes.store') }}" method="POST"
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
                                            <label for="income_date">
                                                TARİH <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-calendar"></i></span>
                                                </div>
                                                <input type="date" class="form-control" name="date"
                                                    id="income_date" value="{{ date('Y-m-d') }}" required>
                                                <div class="invalid-feedback">Tarih seçimi zorunludur!</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="income_category">
                                                Gelir Kategorisi <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-tag"></i></span>
                                                </div>
                                                <select class="form-control custom-select" id="income_category"
                                                    name="income_category_id" required>
                                                    <option value="">Gelir kategorisi seçiniz</option>
                                                    @foreach ($incomeCategories as $category)
                                                        <option value="{{ $category->id }}">
                                                            {{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Gelir kategorisi seçimi zorunludur!</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="bank_account">
                                                BANKA / KASA HESABI <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-university"></i></span>
                                                </div>
                                                <select class="form-control custom-select" id="bank_account" name="account_id"
                                                    required>
                                                    <option value="">Seçiniz...</option>
                                                    @foreach ($accounts as $account)
                                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Hesap seçimi zorunludur!</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Belge ve Müşteri Bilgileri -->
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="income_number">
                                                BELGE NO <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-file-alt"></i></span>
                                                </div>
                                                <input type="text" class="form-control" name="income_number"
                                                    id="income_number" value="{{ $incomeId }}" required>
                                    </div>
                                </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="customer">
                                                MÜŞTERİ (CRM)
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-users"></i></span>
                                                </div>
                                                <select class="form-control custom-select" id="customer" name="customer_id">
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
                                                Müşteri seçilirse gelir müşteri hesap hareketlerine yansır
                                            </small>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="payment_method">
                                                ÖDEME YÖNTEMİ <span class="text-danger">*</span>
                                            </label>
                                                <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-credit-card"></i></span>
                                                </div>
                                                <select class="form-control custom-select" id="payment_method" name="payment_method" required>
                                                    <option value="cash">Nakit</option>
                                                    <option value="card">Kredi Kartı</option>
                                                    <option value="online">Online</option>
                                                    <option value="transfer">Havale/EFT</option>
                                                </select>
                                                <div class="invalid-feedback">Ödeme yöntemi seçimi zorunludur!</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                            <!-- Gelir kalemleri için yeni tasarım -->
                            <div class="card mb-4">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fal fa-table mr-2"></i>Gelir Kalemleri</h5>
                                    <button type="button" class="btn btn-success btn-sm waves-effect waves-themed"
                                        id="addNewIncomeRow">
                                        <i class="fal fa-plus mr-1"></i> Yeni Kalem Ekle
                                    </button>
                                            </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped w-100">
                                            <thead class="bg-success-600">
                                                <tr>
                                                    <th>KALEM ADI</th>
                                                    <th>BİRİM FİYAT</th>
                                                    <th>MİKTAR</th>
                                                    <th>TOPLAM</th>
                                                    <th>AÇIKLAMA</th>
                                                    <th class="text-center" style="width: 120px;">İŞLEM</th>
                                                </tr>
                                            </thead>
                                            <tbody id="incomeItems">
                                                <tr>
                                                    <td>
                                                        <select class="form-control item-name-input" name="item_name[]" required>
                                                            <option value="">Kalem seçiniz...</option>
                                                            @foreach($incomeTypes as $incomeType)
                                                                <option value="{{ $incomeType->name }}">{{ $incomeType->name }}</option>
                                                            @endforeach
                                                        </select>
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
                                                            name="item_description[]">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm btn-icon waves-effect waves-themed deleteIncomeItemBtn">
                                                            <i class="fal fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        </div>
                                    </div>
                                </div>

                            <!-- Toplam ve Açıklama -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fal fa-calculator mr-2"></i>Toplam ve Açıklama</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="total_amount">
                                                TOPLAM TUTAR <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-lira-sign"></i></span>
                                                </div>
                                                <input type="number" step="0.01" min="0" class="form-control" 
                                                    id="total_amount" name="amount" readonly 
                                                    style="background-color: #f8f9fa; font-weight: bold; font-size: 1.1em;">
                                                <div class="invalid-feedback">Toplam tutar hesaplanmalıdır!</div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="reference_number">
                                                REFERANS NUMARASI
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-hashtag"></i></span>
                                            </div>
                                                <input type="text" class="form-control" name="reference_number"
                                                    id="reference_number" placeholder="Fatura No, Makbuz No vb.">
                                        </div>
                                    </div>
                                </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="description">
                                                AÇIKLAMA
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-comment"></i></span>
                                            </div>
                                                <textarea class="form-control" name="description" id="description" rows="3"
                                                    placeholder="Gelir ile ilgili detaylı açıklama..."></textarea>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <!-- Form Butonları -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <a href="{{ route('incomes.index') }}" class="btn btn-secondary mr-2">
                                                <i class="fal fa-times mr-1"></i> İptal
                                            </a>
                                            <button type="submit" class="btn btn-success waves-effect waves-themed">
                                                <i class="fal fa-save mr-1"></i> Gelir Kaydet
                                    </button>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const incomeTypes = @json($incomeTypes ?? []);

document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill today's date if not set
    if (!document.getElementById('income_date').value) {
        document.getElementById('income_date').value = new Date().toISOString().split('T')[0];
    }

    // Auto-generate income number
    if (!document.getElementById('income_number').value) {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        document.getElementById('income_number').value = `GEL-${year}${month}${day}-${random}`;
    }

    // Calculate line total
    function calculateLineTotal(row) {
        const unitPrice = parseFloat($(row).find('.unit-price-input').val()) || 0;
        const quantity = parseFloat($(row).find('.quantity-input').val()) || 0;
        const total = unitPrice * quantity;
        $(row).find('.total-amount').val(total.toFixed(2));
        calculateTotal();
    }

    // Calculate total amount
    function calculateTotal() {
        let total = 0;
        $('#incomeItems tr').each(function() {
            total += parseFloat($(this).find('.total-amount').val()) || 0;
        });
        $('#total_amount').val(total.toFixed(2));
    }

    // Add new income item row
    $('#addNewIncomeRow').on('click', function() {
        let optionsHtml = '<option value="">Kalem seçiniz...</option>';
        incomeTypes.forEach(function(type) {
            optionsHtml += '<option value="' + type.name + '">' + type.name + '</option>';
        });
        
        const html = `
            <tr>
                <td>
                    <select class="form-control item-name-input" name="item_name[]" required>
                        ${optionsHtml}
                    </select>
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
                    <input type="text" class="form-control" name="item_description[]">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-icon waves-effect waves-themed deleteIncomeItemBtn">
                        <i class="fal fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#incomeItems').append(html);
    });

    // Delete income item row
    $(document).on('click', '.deleteIncomeItemBtn', function() {
        if ($('#incomeItems tr').length > 1) {
            $(this).closest('tr').remove();
            calculateTotal();
        } else {
            alert('En az bir gelir kalemi bulunmalıdır!');
        }
    });

    // Calculate on input change
    $(document).on('input', '.unit-price-input, .quantity-input', function() {
        calculateLineTotal($(this).closest('tr'));
    });

    // Form validation - KALDIRILDI
    // $('#incomeForm').on('submit', function(e) {
    //     // Validation kaldırıldı - direkt submit
    // });

    // Initialize calculation
    calculateTotal();
});
</script>

@include('layouts.footer')