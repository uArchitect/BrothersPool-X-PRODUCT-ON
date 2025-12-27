@include('layouts.header')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="panel-container">
                    <div class="panel-content">
                        <form action="{{ route('income_types.update', $incomeType->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
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
                                                           id="name" name="name" value="{{ old('name', $incomeType->name) }}" 
                                                           placeholder="Örn: Satış Geliri, Hizmet Geliri" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">Bu ad gelir kayıtlarında görünecektir.</small>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="sort_order" class="form-label">Sıralama</label>
                                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                           id="sort_order" name="sort_order" value="{{ old('sort_order', $incomeType->sort_order ?? 0) }}" 
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
                                                              placeholder="Bu gelir tipi hakkında detaylı açıklama yazabilirsiniz...">{{ old('description', $incomeType->description) }}</textarea>
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
                                                       {{ old('is_active', $incomeType->is_active) ? 'checked' : '' }}>
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
                                                    <i class="fal fa-save mr-1"></i>Değişiklikleri Kaydet
                                                </button>
                                                <a href="{{ route('income_types.show', $incomeType->id) }}" class="btn btn-info">
                                                    <i class="fal fa-eye mr-1"></i>Görüntüle
                                                </a>
                                                <a href="{{ route('income_types.index') }}" class="btn btn-secondary">
                                                    <i class="fal fa-arrow-left mr-1"></i>Geri Dön
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sistem Bilgileri -->
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fal fa-info mr-2"></i>Sistem Bilgileri
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <small class="text-muted">
                                                <strong>ID:</strong> {{ $incomeType->id }}<br>
                                                <strong>Oluşturulma:</strong> {{ \Carbon\Carbon::parse($incomeType->created_at)->format('d.m.Y H:i') }}<br>
                                                @if($incomeType->updated_at != $incomeType->created_at)
                                                    <strong>Son Güncelleme:</strong> {{ \Carbon\Carbon::parse($incomeType->updated_at)->format('d.m.Y H:i') }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>

                                    <!-- İstatistikler -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fal fa-chart-bar mr-2"></i>İstatistikler
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $incomesCount = DB::table('incomes')->where('income_type_id', $incomeType->id)->count();
                                                $totalAmount = DB::table('incomes')->where('income_type_id', $incomeType->id)->sum('amount');
                                            @endphp
                                            
                                            <div class="text-center">
                                                <h4 class="text-info">{{ $incomesCount }}</h4>
                                                <p class="text-muted mb-2">Gelir Kaydı</p>
                                                
                                                <h4 class="text-success">₺{{ number_format($totalAmount, 2) }}</h4>
                                                <p class="text-muted mb-0">Toplam Tutar</p>
                                            </div>
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

