@include('layouts.header')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="panel-container">
                    <div class="panel-content">
                        <form action="{{ route('income_types.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Temel Bilgiler -->
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fal fa-info-circle mr-2"></i>Temel Bilgiler
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="name" class="form-label">Gelir Tipi Adı <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                           id="name" name="name" value="{{ old('name') }}" 
                                                           placeholder="Örn: Satış Geliri, Hizmet Geliri" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">Bu ad gelir kayıtlarında görünecektir.</small>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="sort_order" class="form-label">Sıralama</label>
                                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                           id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" 
                                                           min="0" step="1">
                                                    @error('sort_order')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">Liste görünümünde sıralama için kullanılır.</small>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label for="description" class="form-label">Açıklama</label>
                                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                                              id="description" name="description" rows="4" 
                                                              placeholder="Bu gelir tipi hakkında detaylı açıklama yazabilirsiniz...">{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">Maksimum 1000 karakter.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <!-- Durum ve İşlemler -->
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fal fa-cogs mr-2"></i>Durum ve İşlemler
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    <strong>Aktif</strong>
                                                </label>
                                                <small class="form-text text-muted d-block">
                                                    Aktif gelir tipleri yeni gelir kayıtlarında kullanılabilir.
                                                </small>
                                            </div>

                                            <hr>

                                            <div class="d-grid gap-2">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fal fa-save mr-1"></i>Gelir Tipini Kaydet
                                                </button>
                                                <a href="{{ route('income_types.index') }}" class="btn btn-secondary">
                                                    <i class="fal fa-arrow-left mr-1"></i>Geri Dön
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Yardım -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fal fa-question-circle mr-2"></i>Yardım
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <small class="text-muted">
                                                <strong>Gelir Tipi:</strong> Gelirlerinizi kategorize etmek için kullanılır.<br><br>
                                                
                                                <strong>Örnekler:</strong><br>
                                                • Satış Geliri<br>
                                                • Hizmet Geliri<br>
                                                • Komisyon Geliri<br>
                                                • Faiz Geliri<br>
                                                • Diğer Gelirler
                                            </small>
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
</div>

@include('layouts.footer')

