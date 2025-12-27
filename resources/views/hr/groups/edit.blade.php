@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-container show">
                    <div class="panel-content">
                        
                        <!-- Başlık -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="text-primary mb-1"><i class="fal fa-edit mr-2"></i>Grup Düzenle</h4>
                                <p class="text-muted mb-0">{{ $group->name }}</p>
                            </div>
                            <div>
                                <a href="{{ route('hr.groups.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="fal fa-arrow-left mr-1"></i> Geri Dön
                                </a>
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fal fa-exclamation-circle mr-2"></i>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        <!-- Form -->
                        <form action="{{ route('hr.groups.update', $group->id) }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="name" class="form-label required-field">Grup Adı</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-users"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $group->name) }}" 
                                                   placeholder="Örn: Mutfak, Servis, Kasa" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="description" class="form-label">Açıklama</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-info-circle"></i>
                                                </span>
                                            </div>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="3" 
                                                      placeholder="Grup açıklaması">{{ old('description', $group->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Butonlar -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fal fa-save mr-2"></i> Güncelle
                                </button>
                                <a href="{{ route('hr.groups.index') }}" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fal fa-times mr-2"></i> İptal
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')

