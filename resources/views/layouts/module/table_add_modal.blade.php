<div class="modal fade" id="addRoomModal" tabindex="-1" role="dialog" aria-labelledby="addRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary-700">
                <h5 class="modal-title text-white" id="addRoomModalLabel">
                    <i class="fal fa-plus-circle mr-2"></i>Yeni Hizmet Odaları Ekle
                </h5>
                <button type="button" class="btn btn-sm text-white" data-bs-dismiss="modal" aria-label="Kapat" style="border: none; background: none;">
                    <i class="fal fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="addRoomForm" action="{{ route('tables.add') }}" method="post">
                    @csrf
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-success btn-sm" id="addMoreRoom">
                            <i class="fal fa-plus mr-1"></i> Yeni Satır Ekle
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-primary-500 text-white">
                                <tr>
                                    <th style="width: 60%">Oda Adı</th>
                                    <th style="width: 30%">Durum</th>
                                    <th style="width: 10%">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody id="roomFields">
                                <tr class="room-entry">
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="tables[0][name]" required>
                                    </td>
                                    <td>
                                        <select class="custom-select" name="rooms[0][status]" required>
                                            <option value="1">Hizmete Açık</option>
                                            <option value="0">Hizmete Açık Değil</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm btn-icon remove-room" title="Satırı Sil" disabled>
                                            <i class="fal fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fal fa-times mr-1"></i> İptal
                </button>
                <button type="submit" class="btn btn-primary" form="addRoomForm">
                    <i class="fal fa-check mr-1"></i> Kaydet
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Add Room Modal JavaScript - Sadece bu modal için gerekli kodlar
$(document).ready(function() {
    // Satır ekleme
    $('#addMoreRoom').off('click').on('click', function() {
        let roomCount = $('#roomFields .room-entry').length;
        const newRoom = `
            <tr class="room-entry">
                <td>
                    <input type="text" class="form-control form-control-sm" name="tables[${roomCount}][name]" required>
                </td>
                <td>
                    <select class="custom-select" name="rooms[${roomCount}][status]" required>
                        <option value="1">Hizmete Açık</option>
                        <option value="0">Hizmete Açık Değil</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-icon remove-room" title="Satırı Sil">
                        <i class="fal fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#roomFields').append(newRoom);
        updateRemoveButtons();
    });

    // Satır silme
    $('#roomFields').on('click', '.remove-room', function() {
        // Sadece bir satır kalırsa silme
        if ($('#roomFields .room-entry').length > 1) {
            $(this).closest('tr').remove();
            updateRoomFieldNames();
            updateRemoveButtons();
        }
    });

    // Masa satırlarının input isimlerini güncelle
    function updateRoomFieldNames() {
        $('#roomFields .room-entry').each(function(index) {
            $(this).find('input[name^="tables"]').attr('name', `tables[${index}][name]`);
            $(this).find('select[name^="tables"]').attr('name', `tables[${index}][status]`);
        });
    }

    // Sadece ilk satırda silme butonunu devre dışı bırak
    function updateRemoveButtons() {
        $('#roomFields .remove-room').prop('disabled', false);
        if ($('#roomFields .room-entry').length === 1) {
            $('#roomFields .remove-room').prop('disabled', true);
        }
    }

    // Modal açıldığında ilk satırda silme butonunu devre dışı bırak
    $('#addRoomModal').on('shown.bs.modal', function () {
        updateRemoveButtons();
    });

    // Sayfa yüklendiğinde ilk satırda silme butonunu devre dışı bırak
    updateRemoveButtons();
});
</script>