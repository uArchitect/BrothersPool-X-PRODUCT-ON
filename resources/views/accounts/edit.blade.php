@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Mali İşlemler</li>
        <li class="breadcrumb-item"><a href="{{ route('accounts') }}">Kasa ve Banka Hesapları</a></li>
        <li class="breadcrumb-item active">Hesap Düzenle</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>
                        <i class="fal fa-edit text-warning"></i>
                        Hesap Düzenle
                    </h2>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Başarılı!</strong> {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Hata!</strong> {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Lütfen aşağıdaki hataları düzeltin:</strong>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form action="{{ route('accounts.update', $account->id) }}" method="POST" id="editAccountForm">
                            @csrf
                            @method('PUT')
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
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $account->name) }}" 
                                                   required placeholder="Hesap adını giriniz">
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
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
                                            <input type="text" class="form-control @error('description') is-invalid @enderror" 
                                                   id="description" name="description" 
                                                   value="{{ old('description', $account->description ?? '') }}" 
                                                   required placeholder="Hesap açıklamasını giriniz">
                                        </div>
                                        @error('description')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
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
                                            <input type="number" class="form-control @error('balance') is-invalid @enderror" 
                                                   id="balance" name="balance" step="0.01" min="0" 
                                                   value="{{ old('balance', $account->balance ?? 0) }}" 
                                                   required placeholder="0.00">
                                        </div>
                                        @error('balance')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
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
                                            <input type="text" class="form-control @error('iban') is-invalid @enderror" 
                                                   id="iban" name="iban" 
                                                   value="{{ old('iban', $account->iban ?? '') }}" 
                                                   placeholder="TR00 0000 0000 0000 0000 0000 00">
                                        </div>
                                        @error('iban')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Kasa hesapları için IBAN gerekli değildir</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-group text-right">
                                        <a href="{{ route('accounts') }}" class="btn btn-outline-secondary waves-effect waves-themed mr-2">
                                            <i class="fal fa-times mr-1"></i> İptal
                                        </a>
                                        <button type="submit" class="btn btn-primary waves-effect waves-themed">
                                            <i class="fal fa-check mr-1"></i> Kaydet
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
</main>

@include('layouts.footer')

<script>
    $(document).ready(function(){
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
    });
</script>

