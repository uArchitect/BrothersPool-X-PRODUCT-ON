
@include('dashboard.partials.header')
@include('dashboard.partials.module.employee_add_modal')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script>

<link href="{{ asset('assets/css/expense.css') }}" rel="stylesheet" type="text/css" />

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 font-size-18">Giderler</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Analiz</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Giderler</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-md-12 col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Gider Ekle</h5>
                            <p class="card-title-desc">Buradan gider ekleyebilirsiniz.</p>
                            <form class="needs-validation" action="{{ route('expenses.add') }}" method="POST" novalidate>
                                @csrf
                                <div class="row gx-3">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="expense_date" >TARİH *</label>
                                            <input type="date" class="form-control" name="date" id="expense_date" required>
                                        </div>
                                    </div>
                                    <!-- end col -->
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="expense_type" >GİDER TİPİ *</label>
                                            <select class="select2 form-control mb-3 custom-select" id="expense_type" name="expense_type_id" required>
                                                <option  value="">Seçiniz...</option>
                                                @foreach($ExpenseTypes as $expense_type)
                                                <option value="{{ $expense_type->code }}">{{ $expense_type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!-- end col -->
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="bank_account" >BANKA / KASA HESABI *</label>
                                            <select class="custom-select" id="bank_account" name="account_id" required>
                                                <option  value="">Seçiniz...</option>
                                                @foreach($accounts as $bank_account)
                                                <option value="{{ $bank_account->id }}">{{ $bank_account->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row gx-3">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="expense_number" >BELGE NO *</label>
                                            <input type="text" class="form-control" name="expense_number" id="expense_number" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="employee" >PERSONEL *</label>
                                            <select class="custom-select" id="employee" name="employee_id" required>
                                                <option  value="">Seçiniz...</option>
                                                @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="tax_rate" >VERGİ NO *</label>
                                            <select class="custom-select" id="tax_rate" name="tax_rate" required>
                                                <option  value="">Seçiniz...</option>
                                                <option value="1">1</option>
                                                <!-- Diğer seçenekler -->
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">KATEGORİ</th>
                                            <th scope="col">GİDER ADI</th>
                                            <th scope="col">TUTAR</th>
                                            <th scope="col">AÇIKLAMA</th>
                                            <th scope="col">İŞLEM</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select class="custom-select" name="expense_category_id[]" required>
                                                    <option  value="">Seçiniz...</option>
                                                    @foreach($expense_categories as $expense_category)
                                                    <option value="{{ $expense_category->id }}">{{ $expense_category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="expense[]" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="amount[]" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="description[]" required>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" id="addExpenseItemBtn" class="btn btn-primary waves-effect waves-light"><i class="fas fa-plus"></i></button>
                                                <button type="button" class="btn btn-danger deleteExpenseItemBtn"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="row gx-4">
                                    <div class="col-md-12">
                                        <div class="mb-4">
                                            <label for="expense_note" >Not (Opsiyonel)</label>
                                            <textarea class="ckeditor" name="note" id="expense_note"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="fas fa-save"></i> Kaydet</button>
                                <button type="reset" class="btn btn-danger waves-effect waves-light resetBtn"><i class="fas fa-times"></i> Sıfırla</button>
                            </form>
                            <!-- end form -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        CKEDITOR.replace('expense_note');

        $('#addExpenseItemBtn').click(function() {
            var html = '<tr>';
            html += '<td>';
            html += '<select class="custom-select" name="expense_category_id[]" required>';
            html += '<option  value="">Seçiniz...</option>';
            @foreach($expense_categories as $expense_category)
            html += '<option value="{{ $expense_category->id }}">{{ $expense_category->name }}</option>';
            @endforeach
            html += '</select>';
            html += '</td>';
            html += '<td>';
            html += '<input type="text" class="form-control" name="expense[]" required>';
            html += '</td>';
            html += '<td>';
            html += '<input type="text" class="form-control" name="amount[]" required>';
            html += '</td>';
            html += '<td>';
            html += '<input type="text" class="form-control" name="description[]" required>';
            html += '</td>';
            html += '<td class="text-center">';
            html += '<button type="button" class="btn btn-danger deleteExpenseItemBtn"><i class="fas fa-trash"></i></button>';
            html += '</td>';
            html += '</tr>';
            $('tbody').append(html);
        });

        $(document).on('click', '.deleteExpenseItemBtn', function() {
            $(this).closest('tr').remove();
        });

        $('.resetBtn').click(function() {
            $('form')[0].reset();
            CKEDITOR.instances['expense_note'].setData('');
        });
    });
</script>

@include('dashboard.partials.footer')
@include('dashboard.partials.datatable')
