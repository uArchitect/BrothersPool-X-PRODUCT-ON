@include('layouts.header')

<style>
.expense-type-detail {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 30px;
}

.detail-section {
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

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #495057;
}

.detail-value {
    color: #6c757d;
}
</style>

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="expense-type-detail">
                            
                            <!-- Basic Information -->
                            <div class="detail-section">
                                <h5 class="section-title">
                                    <i class="fal fa-info-circle mr-2"></i>Temel Bilgiler
                                </h5>
                                <div class="detail-row">
                                    <span class="detail-label">Gider Tipi Adı:</span>
                                    <span class="detail-value">{{ $expenseType->name }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Durum:</span>
                                    <span class="detail-value">
                                        <span class="badge {{ $expenseType->is_active ? 'badge-success' : 'badge-secondary' }}">
                                            <i class="fal fa-{{ $expenseType->is_active ? 'check' : 'times' }} mr-1"></i>
                                            {{ $expenseType->is_active ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </span>
                                </div>
                                @if($expenseType->description)
                                    <div class="detail-row">
                                        <span class="detail-label">Açıklama:</span>
                                        <span class="detail-value">{{ $expenseType->description }}</span>
                                    </div>
                                @endif
                            </div>


                            <!-- System Information -->
                            <div class="detail-section">
                                <h5 class="section-title">
                                    <i class="fal fa-cog mr-2"></i>Sistem Bilgileri
                                </h5>
                                <div class="detail-row">
                                    <span class="detail-label">Oluşturulma Tarihi:</span>
                                    <span class="detail-value">{{ \Carbon\Carbon::parse($expenseType->created_at)->format('d.m.Y H:i') }}</span>
                                </div>
                                @if($expenseType->updated_at != $expenseType->created_at)
                                    <div class="detail-row">
                                        <span class="detail-label">Son Güncelleme:</span>
                                        <span class="detail-value">{{ \Carbon\Carbon::parse($expenseType->updated_at)->format('d.m.Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('expense_types.index') }}" class="btn btn-secondary">
                                    <i class="fal fa-arrow-left mr-2"></i>Geri Dön
                                </a>
                                <div>
                                    <a href="{{ route('expense_types.edit', $expenseType->id) }}" class="btn btn-primary">
                                        <i class="fal fa-edit mr-2"></i>Düzenle
                                    </a>
                                    <button type="button" class="btn btn-danger ml-2" onclick="deleteExpenseType({{ $expenseType->id }})">
                                        <i class="fal fa-trash mr-2"></i>Sil
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gider Tipi Sil</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Bu gider tipini silmek istediğinizden emin misiniz?</p>
                <p class="text-danger"><strong>Bu işlem geri alınamaz!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteExpenseType(expenseTypeId) {
    document.getElementById('deleteForm').action = '/expense_types/' + expenseTypeId;
    $('#deleteModal').modal('show');
}
</script>

@include('layouts.footer')
