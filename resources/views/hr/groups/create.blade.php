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
                                <h4 class="text-primary mb-1"><i class="fal fa-plus-circle mr-2"></i>Yeni Grup Ekle</h4>
                                <p class="text-muted mb-0">Birden fazla grup ekleyebilirsiniz</p>
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
                        <form action="{{ route('hr.groups.store') }}" method="POST" id="groupsForm">
                            @csrf
                            
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fal fa-table mr-2"></i>Grup Listesi</h5>
                                    <button type="button" class="btn btn-success btn-sm" id="addRowBtn">
                                        <i class="fal fa-plus mr-1"></i> Satır Ekle
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="groupsTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="40%">Grup Adı</th>
                                                    <th width="50%">Açıklama</th>
                                                    <th width="10%" class="text-center">İşlem</th>
                                                </tr>
                                            </thead>
                                            <tbody id="groupsTableBody">
                                                <!-- İlk satır -->
                                                <tr class="group-row">
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm" 
                                                               name="groups[0][name]" 
                                                               placeholder="Örn: Mutfak, Servis, Kasa" required>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm" 
                                                               name="groups[0][description]" 
                                                               placeholder="Grup açıklaması">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger btn-sm remove-row-btn" disabled>
                                                            <i class="fal fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Butonlar -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fal fa-save mr-2"></i> Grupları Kaydet
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

<script>
$(document).ready(function() {
    let rowCount = 1;
    
    // Satır ekleme
    $('#addRowBtn').on('click', function() {
        const newRow = `
            <tr class="group-row">
                <td>
                    <input type="text" class="form-control form-control-sm" 
                           name="groups[${rowCount}][name]" 
                           placeholder="Örn: Mutfak, Servis, Kasa" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" 
                           name="groups[${rowCount}][description]" 
                           placeholder="Grup açıklaması">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row-btn">
                        <i class="fal fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#groupsTableBody').append(newRow);
        rowCount++;
        
        // İlk satırın silme butonunu aktif et
        if ($('.group-row').length > 1) {
            $('.remove-row-btn').prop('disabled', false);
        }
    });
    
    // Satır silme
    $(document).on('click', '.remove-row-btn', function() {
        if ($('.group-row').length > 1) {
            $(this).closest('tr').remove();
            
            // Eğer tek satır kaldıysa silme butonunu devre dışı bırak
            if ($('.group-row').length === 1) {
                $('.remove-row-btn').prop('disabled', true);
            }
        }
    });
    
    // Form gönderilmeden önce boş satırları temizle
    $('#groupsForm').on('submit', function(e) {
        // Boş satırları kaldır
        $('.group-row').each(function() {
            const name = $(this).find('input[name*="[name]"]').val();
            
            if (!name || name.trim() === '') {
                $(this).remove();
            }
        });
        
        // Eğer hiç satır kalmadıysa formu gönderme
        if ($('.group-row').length === 0) {
            e.preventDefault();
            alert('Lütfen en az bir grup ekleyin!');
            return false;
        }
    });
});
</script>

<style>
.group-row {
    transition: background-color 0.2s;
}
.group-row:hover {
    background-color: #f8f9fa;
}
.remove-row-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
