<button class="btn btn-primary btn-sm" data-bs-toggle="modal"
    data-bs-target="#editUserModal{{ $employee->id }}"
    title="Çalışanı Düzenle">
    <i class="fal fa-pencil-alt"></i>
</button>
<button class="btn btn-info btn-sm" data-bs-toggle="modal"
    data-bs-target="#historyModal{{ $employee->id }}"
    title="İşlem Geçmişi">
    <i class="fal fa-history"></i>
</button>
<button class="btn btn-danger btn-sm delete-employee-btn"
    data-id="{{ $employee->id }}" title="Çalışanı Sil">
    <i class="fal fa-trash"></i>
</button> 