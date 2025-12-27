@include('layouts.header')
<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Çalışanlar ve Personeller</li>
        <li class="breadcrumb-item active">Uzmanlık Alanları</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr d-flex justify-content-between align-items-center">
                    <h2>Uzmanlık Alanları <span class="fw-300"><i>Yönetim</i></span></h2>
                </div>
                <div class="panel-container show">
                    <div class="panel-content">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <form action="{{ route('employees.specialties.add') }}" method="POST" class="mb-4">
                            @csrf
                            <div class="row mb-2">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="specialties[0][name]" placeholder="Uzmanlık Alanı Adı" required>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="specialties[0][description]" placeholder="Açıklama">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Kaydet</button>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table id="specialties-table" class="table table-bordered table-hover table-striped w-100">
                                <thead class="bg-highlight">
                                    <tr>
                                        <th>#</th>
                                        <th>Uzmanlık Alanı</th>
                                        <th>Açıklama</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($specialties as $i => $specialty)
                                        <tr>
                                            <td>{{ $i+1 }}</td>
                                            <td>{{ $specialty->name }}</td>
                                            <td>{{ $specialty->description }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editSpecialtyModal" data-id="{{ $specialty->id }}" data-name="{{ $specialty->name }}" data-description="{{ $specialty->description }}">
                                                    <i class="fal fa-edit"></i> Düzenle
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteSpecialtyModal" data-id="{{ $specialty->id }}" data-name="{{ $specialty->name }}">
                                                    <i class="fal fa-trash"></i> Sil
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Düzenle Modal -->
    <div class="modal fade" id="editSpecialtyModal" tabindex="-1" role="dialog" aria-labelledby="editSpecialtyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="editSpecialtyForm" method="POST" action="{{ route('employees.specialties.update') }}">
                @csrf
                <input type="hidden" name="id" id="edit-specialty-id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSpecialtyModalLabel">Uzmanlık Alanı Düzenle</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit-specialty-name">Uzmanlık Alanı Adı</label>
                            <input type="text" class="form-control" id="edit-specialty-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-specialty-description">Açıklama</label>
                            <input type="text" class="form-control" id="edit-specialty-description" name="description">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sil Modal -->
    <div class="modal fade" id="deleteSpecialtyModal" tabindex="-1" role="dialog" aria-labelledby="deleteSpecialtyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="deleteSpecialtyForm" method="POST" action="{{ route('employees.specialties.delete') }}">
                @csrf
                <input type="hidden" name="id" id="delete-specialty-id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteSpecialtyModalLabel">Uzmanlık Alanı Sil</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p id="delete-specialty-message">Bu uzmanlık alanını silmek istediğinize emin misiniz?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Vazgeç</button>
                        <button type="submit" class="btn btn-danger">Sil</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>
@include('layouts.footer')
<!-- DataTables Bundle Script - Required for this page -->
<script src="{{ asset('js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#specialties-table').DataTable({
            orderCellsTop: true,
            fixedHeader: true,
            responsive: true,
            processing: false,
            serverSide: false,
            order: [],
            language: {
                paginate: {
                    first: "İlk",
                    last: "Son",
                    next: "Sonraki",
                    previous: "Önceki"
                },
                info: "Gösterilen: _START_ - _END_ / _TOTAL_",
                infoEmpty: "Gösterilecek kayıt yok",
                zeroRecords: "Eşleşen kayıt bulunamadı",
                lengthMenu: "Sayfa başına _MENU_ kayıt göster"
            }
        });

        // Düzenle modalı açarken verileri doldur
        $('#editSpecialtyModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var name = button.data('name');
            var description = button.data('description');
            var modal = $(this);
            modal.find('#edit-specialty-id').val(id);
            modal.find('#edit-specialty-name').val(name);
            modal.find('#edit-specialty-description').val(description);
        });

        // Sil modalı açarken verileri doldur
        $('#deleteSpecialtyModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var name = button.data('name');
            var modal = $(this);
            modal.find('#delete-specialty-id').val(id);
            modal.find('#delete-specialty-message').text('"' + name + '" uzmanlık alanını silmek istediğinize emin misiniz?');
        });
    });
</script>
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Başarılı',
            text: @json(session('success')),
            confirmButtonText: 'Tamam'
        });
    });
</script>
@endif
@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Hata',
            text: @json(session('error')),
            confirmButtonText: 'Tamam'
        });
    });
</script>
@endif