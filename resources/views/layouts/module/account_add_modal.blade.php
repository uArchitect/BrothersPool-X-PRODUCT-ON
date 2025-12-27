<div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fal fa-plus-circle text-success"></i>
                    <span class="fw-700">Kasa/Banka Hesabı</span> Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat" id="addAccountModalClose"></button>
            </div>
            <form action="{{route('bank.add')}}" method="POST" id="addAccountForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">
                                    Hesap Adı
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fal fa-university"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="name" name="name" value="Ana Kasa" required placeholder="Hesap adını giriniz">
                                </div>
                                <small class="form-text text-muted">Örnek: Ana Kasa, Ziraat Bankası, İş Bankası vb.</small>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">
                                    Açıklama
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fal fa-info-circle"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="description" name="description" required placeholder="Hesap açıklamasını giriniz">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">
                                    Başlangıç Bakiyesi
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₺</span>
                                    </div>
                                    <input type="number" class="form-control" id="balance" name="balance" step="0.01" min="0" required placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">
                                    IBAN
                                    <small class="text-muted">(İsteğe bağlı - Banka hesapları için)</small>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fal fa-credit-card"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="iban" name="iban" placeholder="TR00 0000 0000 0000 0000 0000 00">
                                </div>
                                <small class="form-text text-muted">Kasa hesapları için IBAN gerekli değildir</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary waves-effect waves-themed" data-bs-dismiss="modal">
                        <i class="fal fa-times mr-1"></i> Kapat
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-themed">
                        <i class="fal fa-check mr-1"></i> Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        // Form validasyon
        $("#addAccountForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3
                },
                description: {
                    required: true
                },
                balance: {
                    required: true,
                    number: true,
                    min: 0
                },
                iban: {
                    required: false,
                    minlength: 26
                }
            },
            messages: {
                name: {
                    required: "Lütfen hesap adı giriniz",
                    minlength: "Hesap adı en az 3 karakter olmalıdır"
                },
                description: {
                    required: "Lütfen açıklama giriniz"
                },
                balance: {
                    required: "Lütfen bakiye giriniz",
                    number: "Lütfen geçerli bir sayı giriniz",
                    min: "Bakiye 0'dan küçük olamaz"
                },
                iban: {
                    minlength: "IBAN en az 26 karakter olmalıdır"
                }
            },
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });

        // IBAN formatı
        $("#iban").on('input', function() {
            let value = this.value.replace(/\s/g, '').replace(/[^A-Z0-9]/gi, '');
            let formattedValue = '';
            
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            
            this.value = formattedValue.toUpperCase();
        });

        // Modal açıldığında focus
        $('#addAccountModal').on('shown.bs.modal', function () {
            $('#name').focus();
        });

        // Modal kapandığında formu temizle
        $('#addAccountModal').on('hidden.bs.modal', function () {
            $('#addAccountForm')[0].reset();
            $('#addAccountForm').find('.is-invalid').removeClass('is-invalid');
            $('#addAccountForm').find('.invalid-feedback').remove();
        });
    });
</script>
