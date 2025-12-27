@foreach($accounts as $account)
<div class="modal fade" id="editAccountModal{{ $account->id }}" tabindex="-1" role="dialog" aria-labelledby="editAccountModalLabel{{ $account->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAccountModalLabel{{ $account->id }}">
                    <i class="fal fa-edit text-warning"></i>
                    <span class="fw-700">Hesap Düzenle</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <form action="{{ route('bank.update') }}" method="POST" class="editAccountForm">
                @csrf
                <input type="hidden" name="id" value="{{ $account->id }}">
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
                                    <input type="text" class="form-control" id="edit_name_{{ $account->id }}" name="name" value="{{ $account->name }}" required placeholder="Hesap adını giriniz">
                                </div>
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
                                    <input type="text" class="form-control" id="edit_description_{{ $account->id }}" name="description" value="{{ $account->description ?? '' }}" required placeholder="Hesap açıklamasını giriniz">
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
                                    <input type="number" class="form-control" id="edit_balance_{{ $account->id }}" name="balance" step="0.01" min="0" value="{{ $account->balance ?? 0 }}" required placeholder="0.00">
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
                                    <input type="text" class="form-control" id="edit_iban_{{ $account->id }}" name="iban" value="{{ $account->iban ?? '' }}" placeholder="TR00 0000 0000 0000 0000 0000 00">
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
@endforeach

<script>
    $(document).ready(function(){
        // IBAN formatı için tüm edit modallarındaki IBAN alanlarına event listener ekle
        $(document).on('input', 'input[id^="edit_iban_"]', function() {
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
    });
</script>
