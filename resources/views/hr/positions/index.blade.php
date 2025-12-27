@include('layouts.header')

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-container show">
                    <div class="panel-content">
                        
                        <!-- Başlık -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="text-primary mb-1"><i class="fal fa-user-tag mr-2"></i>Pozisyon Yönetimi</h4>
                                <p class="text-muted mb-0">Personel pozisyonlarını (görevler) yönetin</p>
                            </div>
                            <div>
                                <a href="{{ route('hr.positions.create') }}" class="btn btn-success">
                                    <i class="fal fa-plus mr-1"></i> Yeni Pozisyon Ekle
                                </a>
                                <a href="{{ route('hr.management') }}" class="btn btn-secondary ml-2">
                                    <i class="fal fa-arrow-left mr-1"></i> Geri Dön
                                </a>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fal fa-check-circle mr-2"></i>
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fal fa-exclamation-circle mr-2"></i>
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        @if(session('errors') && is_array(session('errors')))
                            <div class="alert alert-warning alert-dismissible fade show">
                                <i class="fal fa-exclamation-triangle mr-2"></i>
                                <strong>Bazı pozisyonlar eklenemedi:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach(session('errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        <!-- Tablo -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped" id="positionsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Grup</th>
                                        <th>Pozisyon Adı</th>
                                        <th>Açıklama</th>
                                        <th class="text-center">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($positions as $position)
                                        <tr>
                                            <td>{{ $position->id }}</td>
                                            <td><span class="badge badge-primary">{{ $position->group_name }}</span></td>
                                            <td><strong>{{ $position->name }}</strong></td>
                                            <td>{{ $position->description ?? '-' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('hr.positions.edit', $position->id) }}" 
                                                   class="btn btn-sm btn-primary" title="Düzenle">
                                                    <i class="fal fa-edit"></i>
                                                </a>
                                                <form action="{{ route('hr.positions.destroy', $position->id) }}" 
                                                      method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Bu pozisyonu silmek istediğinize emin misiniz?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                                        <i class="fal fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                <i class="fal fa-info-circle mr-2"></i>Henüz pozisyon eklenmemiş
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#positionsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json"
        },
        "pageLength": 25,
        "order": [[0, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": 4 }
        ]
    });
});
</script>

