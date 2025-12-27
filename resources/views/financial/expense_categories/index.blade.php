@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Finansal Yönetim</li>
        <li class="breadcrumb-item active">Gider Kategorileri</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    @if(session('success'))
        <script>window.addEventListener('DOMContentLoaded', () => showSuccess('{{ session('success') }}'));</script>
    @endif

    @if(session('error'))
        <script>window.addEventListener('DOMContentLoaded', () => showError('{{ session('error') }}'));</script>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="panel" id="panel-1">

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="alert-icon">
                                        <i class="fal fa-info-circle"></i>
                                    </div>
                                    <div class="flex-1 ml-2">
                                        <span class="h5">Gider Kategori Yönetim Paneli</span>
                                        <br>Gider kategorilerini ekle, düzenle veya detaylarını görüntüle.
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('expense_categories.create') }}" class="btn btn-success">
                                        <i class="fal fa-plus mr-1"></i>Yeni Kategori Ekle
                                    </a>
                                </div>
                            </div>
                        </div>

                        <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-highlight">
                                <tr>
                                    <th class="text-center" style="width: 20px;"><i class="fal fa-folder" title="Kategori"></i></th>
                                    <th>Kategori Bilgileri</th>
                                    <th>Açıklama</th>
                                    <th>Durum</th>
                                    <th>Oluşturulma</th>
                                    <th style="width: 120px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenseCategories as $expenseCategory)
                                <tr>
                                    <td class="text-center">
                                        <i class="fal fa-folder text-primary"></i>
                                    </td>
                                    <td>
                                        <div class="category-info">
                                            <div class="category-avatar rounded-circle">
                                                <span class="avatar-initials">{{ strtoupper(substr($expenseCategory->name,0,2)) }}</span>
                                            </div>
                                            <div class="category-name">
                                                <strong>{{ $expenseCategory->name }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($expenseCategory->description)
                                            <div class="text-muted">
                                                {{ Str::limit($expenseCategory->description, 50) }}
                                            </div>
                                        @else
                                            <span class="text-muted">Açıklama yok</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($expenseCategory->is_active)
                                            <span class="badge badge-success">
                                                <i class="fal fa-check mr-1"></i>Aktif
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fal fa-times mr-1"></i>Pasif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <small class="text-muted">
                                                <i class="fal fa-calendar mr-1"></i>
                                                {{ \Carbon\Carbon::parse($expenseCategory->created_at)->format('d.m.Y') }}
                                            </small>
                                            <small class="text-muted">
                                                <i class="fal fa-clock mr-1"></i>
                                                {{ \Carbon\Carbon::parse($expenseCategory->created_at)->format('H:i') }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('expense_categories.show', $expenseCategory->id) }}" class="btn btn-xs btn-info" title="Görüntüle">
                                                <i class="fal fa-eye"></i>
                                            </a>
                                            <a href="{{ route('expense_categories.edit', $expenseCategory->id) }}" class="btn btn-xs btn-warning" title="Düzenle">
                                                <i class="fal fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-xs btn-danger btn-delete-category" data-id="{{ $expenseCategory->id }}" data-name="{{ $expenseCategory->name }}">
                                                <i class="fal fa-trash"></i>
                                            </button>
                                        </div>
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
</main>

@include('layouts.footer')

<script defer src="{{ asset('js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#dt-basic-example').DataTable({
        responsive: true,
        stateSave: true,
        pageLength: 25,
        order: [[1, 'asc']],
        language: { url: "{{ asset('media/data/tr.json') }}" },
        columnDefs: [
            { orderable: false, targets: [0, 5] },
            { searchable: false, targets: [0, 5] }
        ]
    });

    $(document).on('click', '.btn-delete-category', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Kategori Sil',
            text: `"${name}" kategorisini silmek istediğinizden emin misiniz?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet, Sil!',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/expense_categories/${id}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>

<style>
.category-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.category-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

.avatar-initials {
    font-size: 12px;
    font-weight: 600;
}

.category-name {
    font-size: 14px;
    font-weight: 500;
}

.gap-2 {
    gap: 0.5rem;
}

.btn-xs {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}
</style>