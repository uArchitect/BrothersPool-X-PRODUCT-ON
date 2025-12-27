@include('layouts.header')

<style>
.income-category-form {
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
                        <div class="income-category-form">
                            <form method="POST" action="{{ route('income_categories.store') }}">
                                @csrf
                                
                                <!-- Basic Information -->
                                <div class="form-section">
                                    <h5 class="section-title">
                                        <i class="fal fa-info-circle mr-2"></i>Temel Bilgiler
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="name" class="form-label">Gelir Kategorisi Adı <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                       id="name" name="name" value="{{ old('name') }}" 
                                                       placeholder="Örn: Satış Geliri" required>
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
                                                          placeholder="Gelir kategorisi açıklaması...">{{ old('description') }}</textarea>
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
                                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="is_active">
                                                        <strong>Aktif</strong> - Bu gelir kategorisi kullanılabilir
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Pasif gelir kategorileri yeni gelirlerde görünmez.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('income_categories.index') }}" class="btn btn-secondary">
                                        <i class="fal fa-times mr-2"></i>İptal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fal fa-save mr-2"></i>Gelir Kategorisi Kaydet
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
});
</script>

@include('layouts.footer')
