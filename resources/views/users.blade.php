@include('layouts.header')
@include('layouts.module.user_add_modal')
@include('layouts.module.add_user_group_modal')
<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Kullanıcı Ayarları</li>
        <li class="breadcrumb-item active">Kullanıcı Listesi</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr d-flex justify-content-between align-items-center">
                    <h2>
                        Kullanıcı Listesi <span class="fw-300"><i>Tablo</i></span>
                    </h2>
                    <div class="panel-toolbar">
                        <button type="button" class="btn btn-success dropdown-toggle px-3" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fal fa-plus-circle mr-1"></i> İşlemler
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="fal fa-user-plus"></i> Yeni Kullanıcı Ekle</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addGroupModal"><i class="fal fa-users"></i> Yeni Grup Ekle</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editGroupModal"><i class="fal fa-user-shield"></i> Grup İzni Düzenle</a></li>
                        </ul>
                    </div>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="panel-tag mb-4">
                            Kullanıcılarınızı buradan kolayca yönetebilirsiniz. Kullanıcı bilgilerine, düzenlemelere ve silme işlemlerine kolayca erişebilirsiniz.
                        </div>

                        <div class="table-responsive">
                            <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
                                <thead class="bg-highlight">
                                    <tr>
                                        <th>Telefon</th>
                                        <th>Ad Soyad</th>
                                        <th>Email</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->phone ?? 'N/A' }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td class="d-flex justify-content-center">
                                            <!-- Düzenle Butonu -->
                                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#editModal{{ $user->id }}">
                                                <i class="fal fa-edit"></i>
                                            </button>
                                            &nbsp;
                                            <a href="javascript:void(0);" class="btn btn-danger btn-delete-user" data-id="{{ $user->id }}">
                                                <i class="fal fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Telefon</th>
                                        <th>Ad Soyad</th>
                                        <th>Email</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div> <!-- end panel -->
        </div> <!-- end col -->
    </div> <!-- end row -->
</main>
@include('layouts.module.user_edit_modal', ['user' => $user])

<!-- bu katman yalnızca mobil menü etkinleştirildiğinde görünür -->
@include('layouts.footer')
<script src="{{ asset('js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script>
    $(document).ready(function() {
        // Setup - add a text input to each footer cell
        $('#dt-basic-example thead tr').clone(true).appendTo('#dt-basic-example thead');
        $('#dt-basic-example thead tr:eq(1) th').each(function(i) {
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control form-control-sm" placeholder="Search ' +
                title + '" />');

            $('input', this).on('keyup change', function() {
                if (table.column(i).search() !== this.value) {
                    table
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var table = $('#dt-basic-example').DataTable({
            orderCellsTop: true,
            fixedHeader: true,
        });

        // Kullanıcı ekleme (Add Modal)
        $(document).on('submit', '.user-add-form', function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize();
            var modal = form.closest('.modal');
            $.post("{{ route('users.add') }}", formData)
                .done(function(res) {
                    if (typeof $ !== 'undefined') {
                        $('.modal.show').modal('hide');
                    }
                    showSuccess('Kullanıcı başarıyla eklendi.');
                    modal.modal('hide');
                    // Yeni kullanıcıyı tabloya ekle (örnek: name, email, phone)
                    if(res && res.user) {
                        var user = res.user;
                        var newRow = table.row.add([
                            user.phone || 'N/A',
                            user.name,
                            user.email,
                            `<button type='button' class='btn btn-primary me-2' data-bs-toggle='modal' data-bs-target='#editModal${user.id}'>
                                <i class='fal fa-edit'></i>
                            </button>
                            <a href='javascript:void(0);' class='btn btn-danger btn-delete-user' data-id='${user.id}'>
                                <i class='fal fa-trash'></i>
                            </a>`
                        ]).draw().node();
                        $(newRow).attr('data-id', user.id);
                    } else {
                        setTimeout(() => window.location.reload(), 1000);
                    }
                })
                .fail(function(xhr) {
                    if(xhr.responseJSON && xhr.responseJSON.errors) {
                        var messages = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        showError(messages);
                    } else if(xhr.responseJSON && xhr.responseJSON.message) {
                        showError(xhr.responseJSON.message);
                    } else {
                        showError('Bilinmeyen bir hata oluştu.');
                    }
                });
        });

        // Kullanıcı güncelleme (Edit Modal)
        $(document).on('submit', '.user-edit-form', function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize();
            var modal = form.closest('.modal');
            $.post("{{ route('users.update') }}", formData)
                .done(function(res) {
                    if (typeof $ !== 'undefined') {
                        $('.modal.show').modal('hide');
                    }
                    showSuccess('Kullanıcı başarıyla güncellendi.');
                    modal.modal('hide');
                    // Tabloyu güncelle
                    if(res && res.user) {
                        var user = res.user;
                        var userId = user.id;
                        var row = $("#dt-basic-example tbody tr").filter(function() {
                            return $(this).find('.btn-delete-user').data('id') == userId;
                        });
                        if(row.length) {
                            row.find('td').eq(0).text(user.phone || 'N/A');
                            row.find('td').eq(1).text(user.name);
                            row.find('td').eq(2).text(user.email);
                        }
                    }
                })
                .fail(function(xhr) {
                    if(xhr.responseJSON && xhr.responseJSON.errors) {
                        var messages = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        showError(messages);
                    } else if(xhr.responseJSON && xhr.responseJSON.message) {
                        showError(xhr.responseJSON.message);
                    } else {
                        showError('Bilinmeyen bir hata oluştu.');
                    }
                });
        });

        // Kullanıcı silme (AJAX)
        $(document).on('click', '.btn-delete-user', function() {
            var userId = $(this).data('id');
            Swal.fire({
                title: 'Emin misiniz?',
                text: 'Bu kullanıcıyı silmek istediğinize emin misiniz?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, sil',
                cancelButtonText: 'Vazgeç'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ route('users.delete') }}", { id: userId, _token: '{{ csrf_token() }}' })
                        .done(function(res) {
                            if (typeof $ !== 'undefined') {
                                $('.modal.show').modal('hide');
                            }
                            showSuccess('Kullanıcı başarıyla silindi.');
                            // Satırı tablodan kaldır
                            var row = $("#dt-basic-example tbody tr").filter(function() {
                                return $(this).find('.btn-delete-user').data('id') == userId;
                            });
                            if(row.length) {
                                table.row(row).remove().draw();
                            }
                        })
                        .fail(function(xhr) {
                            if(xhr.responseJSON && xhr.responseJSON.errors) {
                                var messages = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                                showError(messages);
                            } else if(xhr.responseJSON && xhr.responseJSON.message) {
                                showError(xhr.responseJSON.message);
                            } else {
                                showError('Bilinmeyen bir hata oluştu.');
                            }
                        });
                }
            });
        });
    });
</script>

<!-- Silme işlemi için form -->
<form id="deleteUserForm" method="POST" action="{{ route('users.delete') }}" style="display:none;">
    @csrf
    <input type="hidden" name="id" id="deleteUserId">
</form>

<!-- SweetAlert2 z-index fix -->
<style>
.swal2-container {
    z-index: 20000 !important;
}
</style>

