<div class="modal fade" id="editSpecialtyModal" tabindex="-1" aria-labelledby="editSpecialtyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Uzmanlık Alanı Düzenle</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Kapat" id="editSpecialtyModalClose"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <form id="editSpecialtyForm">
                    @csrf
                    <input type="hidden" id="edit_specialty_id" name="id">
                    <div class="form-group">
                        <label for="edit_specialty_name">Uzmanlık Alanı Adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_specialty_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_specialty_description">Açıklama</label>
                        <input type="text" class="form-control" id="edit_specialty_description" name="description">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="updateSpecialtyBtn">
                    <i class="fal fa-save mr-1"></i>
                    Güncelle
                </button>
            </div>
        </div>
    </div>
</div> 