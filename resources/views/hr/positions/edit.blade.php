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
                                <h4 class="text-primary mb-1"><i class="fal fa-edit mr-2"></i>Pozisyon Düzenle</h4>
                                <p class="text-muted mb-0">{{ $position->name }} - {{ $position->group_name }}</p>
                            </div>
                            <div>
                                <a href="{{ route('hr.positions.index') }}" class="btn btn-sm btn-secondary">
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
                        <form action="{{ route('hr.positions.update', $position->id) }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="group_id" class="form-label required-field">Grup (Departman)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-users"></i>
                                                </span>
                                            </div>
                                            <select class="form-control @error('group_id') is-invalid @enderror" 
                                                    id="group_id" name="group_id" required>
                                                <option value="">Grup Seçin</option>
                                                @foreach($groups as $group)
                                                    <option value="{{ $group->id }}" 
                                                        {{ old('group_id', $position->group_id) == $group->id ? 'selected' : '' }}>
                                                        {{ $group->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('group_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label required-field">Pozisyon Adı</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fal fa-user-tag"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $position->name) }}" 
                                                   placeholder="Örn: Şef, Garson, Kasiyer" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mevcut Görevler Gösterimi -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="existing-positions"></div>
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
                                                      placeholder="Pozisyon açıklaması">{{ old('description', $position->description) }}</textarea>
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
                                <a href="{{ route('hr.positions.index') }}" class="btn btn-secondary btn-lg ml-2">
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

<script>
$(document).ready(function() {
    // Grup seçildiğinde mevcut görevleri göster
    $('#group_id').on('change', function() {
        var groupId = $(this).val();
        var existingPositionsDiv = $('#existing-positions');
        var currentPositionId = {{ isset($position) ? $position->id : 0 }};
        
        // Mevcut görevler bölümünü temizle
        existingPositionsDiv.html('');
        
        if (groupId) {
            // Loading göster
            existingPositionsDiv.html('<div class="text-center py-3"><i class="fal fa-spinner fa-spin mr-2"></i>Yükleniyor...</div>');
            
            $.ajax({
                url: '{{ route("employees.positions.by.group") }}',
                data: { group_id: groupId },
                success: function(positions) {
                    existingPositionsDiv.html('');
                    
                    if (positions.length > 0) {
                        var html = '<div class="alert alert-info mb-3">';
                        html += '<h6 class="mb-2"><i class="fal fa-info-circle mr-2"></i>Bu Gruba Ait Mevcut Görevler:</h6>';
                        html += '<div class="row">';
                        
                        positions.forEach(function(position) {
                            var isCurrent = position.id == currentPositionId;
                            var badgeClass = isCurrent ? 'badge-success' : 'badge-primary';
                            var currentBadge = isCurrent ? ' <small>(Mevcut)</small>' : '';
                            
                            html += '<div class="col-md-4 mb-2">';
                            html += '<span class="badge ' + badgeClass + ' p-2"><i class="fal fa-user-tag mr-1"></i>' + position.name + currentBadge + '</span>';
                            html += '</div>';
                        });
                        
                        html += '</div>';
                        html += '<small class="text-muted">Yukarıdaki görevlerden birini seçebilir veya yeni bir görev ekleyebilirsiniz.</small>';
                        html += '</div>';
                        
                        existingPositionsDiv.html(html);
                    } else {
                        existingPositionsDiv.html('<div class="alert alert-warning mb-3"><i class="fal fa-exclamation-triangle mr-2"></i>Bu gruba ait henüz görev eklenmemiş.</div>');
                    }
                },
                error: function() {
                    existingPositionsDiv.html('<div class="alert alert-danger mb-3"><i class="fal fa-exclamation-circle mr-2"></i>Görevler yüklenirken hata oluştu.</div>');
                }
            });
        }
    });
    
    // Sayfa yüklendiğinde eğer grup seçiliyse görevleri yükle
    if ($('#group_id').val()) {
        $('#group_id').trigger('change');
    }
});
</script>

<style>
#existing-positions {
    margin-top: 15px;
}
</style>

@include('layouts.footer')

