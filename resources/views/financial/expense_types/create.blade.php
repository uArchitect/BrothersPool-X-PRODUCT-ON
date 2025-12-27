@include('layouts.header')

<style>
.expense-type-form {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 30px;
}

.form-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.section-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #dee2e6;
}
</style>

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="expense-type-form">
                            <form method="POST" action="{{ route('expense_types.store') }}">
                                @csrf
                                
                                <!-- Basic Information -->
                                <div class="form-section">
                                    <h5 class="section-title">
                                        <i class="fal fa-info-circle mr-2"></i>Temel Bilgiler
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name" class="form-label">Gider Tipi Adı <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                       id="name" name="name" value="{{ old('name') }}" 
                                                       placeholder="Örn: Ofis Malzemeleri" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description" class="form-label">Açıklama</label>
                                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                                          id="description" name="description" rows="3" 
                                                          placeholder="Gider tipi açıklaması...">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Information -->
                                <div class="form-section">
                                    <h5 class="section-title">
                                        <i class="fal fa-cog mr-2"></i>Durum Ayarları
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="is_active">
                                                        <strong>Aktif</strong> - Bu gider tipi kullanılabilir
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Pasif gider tipleri yeni giderlerde görünmez.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('expense_types.index') }}" class="btn btn-secondary">
                                        <i class="fal fa-times mr-2"></i>İptal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fal fa-save mr-2"></i>Gider Tipi Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on name field
    document.getElementById('name').focus();
    
    // Color picker change event
    document.getElementById('color').addEventListener('change', function(e) {
        console.log('Selected color:', e.target.value);
    });
});
</script>

@include('layouts.footer')
