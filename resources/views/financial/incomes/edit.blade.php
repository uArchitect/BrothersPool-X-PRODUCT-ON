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
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="panel-container">
                    <div class="panel-content">
                        <form action="{{ route('incomes.update', $income->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                            <!-- Temel Bilgiler -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">
                                        <i class="fal fa-info-circle mr-2"></i>Temel Bilgiler
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="income_number" class="form-label">
                                                Gelir Numarası <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-hashtag"></i></span>
                                                </div>
                                                <input type="text" class="form-control" name="income_number"
                                                    id="income_number" value="{{ old('income_number', $income->income_number) }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="income_category_id" class="form-label">
                                                Gelir Kategorisi <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-tag"></i></span>
                                                </div>
                                                <select class="form-control custom-select" id="income_category_id" name="income_category_id" required>
                                                    <option value="">Gelir kategorisi seçiniz</option>
                                                    @foreach ($incomeCategories as $category)
                                                        <option value="{{ $category->id }}" {{ old('income_category_id', $income->income_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="account_id" class="form-label">
                                                Hesap <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-wallet"></i></span>
                                                </div>
                                                <select class="form-control custom-select" id="account_id" name="account_id" required>
                                                    <option value="">Hesap seçiniz</option>
                                                    @foreach ($accounts as $account)
                                                        <option value="{{ $account->id }}" {{ old('account_id', $income->account_id) == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                                    @endforeach
                                                </select>
                                    </div>
                                </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="customer_id" class="form-label">
                                                Müşteri
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-user"></i></span>
                                                </div>
                                                <select class="form-control custom-select" id="customer_id" name="customer_id">
                                                    <option value="">Müşteri seçiniz</option>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}" {{ old('customer_id', $income->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="date" class="form-label">
                                                Tarih <span class="text-danger">*</span>
                                            </label>
                                                <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-calendar"></i></span>
                                                </div>
                                                <input type="date" class="form-control" id="date" name="date"
                                                    value="{{ old('date', $income->date) }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="payment_method" class="form-label">
                                                Ödeme Yöntemi <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-credit-card"></i></span>
                                    </div>
                                                <select class="form-control custom-select" id="payment_method" name="payment_method" required>
                                                    <option value="">Ödeme yöntemi seçiniz</option>
                                                    <option value="cash" {{ old('payment_method', $income->payment_method) == 'cash' ? 'selected' : '' }}>Nakit</option>
                                                    <option value="card" {{ old('payment_method', $income->payment_method) == 'card' ? 'selected' : '' }}>Kredi Kartı</option>
                                                    <option value="online" {{ old('payment_method', $income->payment_method) == 'online' ? 'selected' : '' }}>Online Ödeme</option>
                                                    <option value="transfer" {{ old('payment_method', $income->payment_method) == 'transfer' ? 'selected' : '' }}>Havale/EFT</option>
                                                    <option value="check" {{ old('payment_method', $income->payment_method) == 'check' ? 'selected' : '' }}>Çek</option>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <!-- Gelir Kalemleri -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">
                                        <i class="fal fa-list mr-2"></i>Gelir Kalemleri
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="incomeItemsTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="35%">Kalem Adı <span class="text-danger">*</span></th>
                                                    <th width="15%">Birim Fiyat <span class="text-danger">*</span></th>
                                                    <th width="10%">Miktar <span class="text-danger">*</span></th>
                                                    <th width="15%">Tutar</th>
                                                    <th width="15%">Açıklama</th>
                                                    <th width="5%">İşlem</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($incomeItems && count($incomeItems) > 0)
                                                    @foreach($incomeItems as $index => $item)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>
                                                                <input type="text" class="form-control" name="item_name[]" 
                                                                    value="{{ $item->item_name }}" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control unit-price" name="unit_price[]" 
                                                                    step="0.01" min="0" value="{{ $item->unit_price }}" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control quantity" name="quantity[]" 
                                                                    step="0.01" min="0.01" value="{{ $item->quantity }}" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control amount" name="amount[]" 
                                                                    step="0.01" value="{{ $item->amount }}" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name="item_description[]" value="{{ $item->description }}">
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                                                    <i class="fal fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td>1</td>
                                                        <td>
                                                            <input type="text" class="form-control" name="item_name[]" 
                                                                placeholder="Kalem adı giriniz..." required>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control unit-price" name="unit_price[]" 
                                                                step="0.01" min="0" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control quantity" name="quantity[]" 
                                                                step="0.01" min="0.01" value="1" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control amount" name="amount[]" 
                                                                step="0.01" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="item_description[]">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                                <i class="fal fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-success" id="addRow">
                                        <i class="fal fa-plus mr-1"></i>Kalem Ekle
                                    </button>
                                            </div>
                                        </div>

                            <!-- Fatura Bilgileri -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">
                                        <i class="fal fa-file-invoice mr-2"></i>Fatura Bilgileri
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="alert alert-info">
                                                <i class="fal fa-info-circle mr-2"></i>
                                                Fatura fotoğrafı yukarıdaki "Temel Bilgiler" bölümünden yüklenebilir.
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <!-- Toplam ve Açıklama -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">
                                        <i class="fal fa-calculator mr-2"></i>Toplam ve Açıklama
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="description" class="form-label">
                                                Açıklama
                                            </label>
                                            <textarea class="form-control" id="description" name="description" rows="4"
                                                placeholder="Gelir hakkında detaylı açıklama...">{{ old('description', $income->description) }}</textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">Toplam Tutar</h6>
                                                    <h3 class="text-success" id="totalAmount">₺{{ number_format($income->amount, 2, ',', '.') }}</h3>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <!-- Butonlar -->
                            <div class="row">
                                <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('incomes.index') }}" class="btn btn-secondary">
                                            <i class="fal fa-arrow-left mr-1"></i>Geri Dön
                                    </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fal fa-save mr-1"></i>Gelir Güncelle
                                    </button>
                                    </div>
                                </div>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let rowCount = {{ $incomeItems ? count($incomeItems) : 1 }};

    // Satır ekleme
    $('#addRow').click(function() {
        rowCount++;
        
        const newRow = `
            <tr>
                <td>${rowCount}</td>
                <td>
                    <input type="text" class="form-control" name="item_name[]" 
                        placeholder="Kalem adı giriniz..." required>
                </td>
                <td>
                    <input type="number" class="form-control unit-price" name="unit_price[]" 
                        step="0.01" min="0" required>
                </td>
                <td>
                    <input type="number" class="form-control quantity" name="quantity[]" 
                        step="0.01" min="0.01" value="1" required>
                </td>
                <td>
                    <input type="number" class="form-control amount" name="amount[]" 
                        step="0.01" readonly>
                </td>
                <td>
                    <input type="text" class="form-control" name="item_description[]">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="fal fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#incomeItemsTable tbody').append(newRow);
    });

    // Satır silme
    $(document).on('click', '.remove-row', function() {
        if ($('#incomeItemsTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
            updateRowNumbers();
            calculateTotal();
        }
    });

    // Tutar hesaplama
    $(document).on('input', '.unit-price, .quantity', function() {
        const row = $(this).closest('tr');
        const unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
        const quantity = parseFloat(row.find('.quantity').val()) || 0;
        const amount = unitPrice * quantity;
        
        row.find('.amount').val(amount.toFixed(2));
        calculateTotal();
    });

    // Toplam hesaplama
    function calculateTotal() {
        let total = 0;
        $('.amount').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#totalAmount').text('₺' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
    }

    // Satır numaralarını güncelleme
    function updateRowNumbers() {
        $('#incomeItemsTable tbody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    // Form validasyonu - KALDIRILDI
    // $('form').submit(function(e) {
    //     // Validation kaldırıldı - direkt submit
    // });

    // Sayfa yüklendiğinde toplam hesapla
    calculateTotal();
});
</script>

@include('layouts.footer')