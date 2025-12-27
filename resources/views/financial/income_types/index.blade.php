@include('layouts.header')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="panel-container">
                    <div class="panel-content">
                        <!-- Başlık ve İşlemler -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h4 class="mb-0">Gelir Tipleri Listesi</h4>
                                <small class="text-muted">Sistemdeki tüm gelir tiplerini yönetin</small>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('income_types.create') }}" class="btn btn-success">
                                    <i class="fal fa-plus mr-1"></i>Yeni Gelir Tipi
                                </a>
                            </div>
                        </div>

                        <!-- Gelir Tipleri Tablosu -->
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="incomeTypesTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Sıra</th>
                                                <th>Gelir Tipi Adı</th>
                                                <th>Açıklama</th>
                                                <th>Durum</th>
                                                <th>Gelir Sayısı</th>
                                                <th>Toplam Tutar</th>
                                                <th>Oluşturma Tarihi</th>
                                                <th>İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($incomeTypes as $index => $incomeType)
                                                <tr>
                                                    <td>{{ $incomeType->sort_order ?? ($index + 1) }}</td>
                                                    <td>
                                                        <strong>{{ $incomeType->name }}</strong>
                                                    </td>
                                                    <td>
                                                        @if($incomeType->description)
                                                            {{ Str::limit($incomeType->description, 50) }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($incomeType->is_active)
                                                            <span class="badge badge-success">Aktif</span>
                                                        @else
                                                            <span class="badge badge-secondary">Pasif</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-secondary">-</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <strong class="text-muted">₺0.00</strong>
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($incomeType->created_at)->format('d.m.Y') }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('income_types.show', $incomeType->id) }}" 
                                                               class="btn btn-sm btn-info" title="Görüntüle">
                                                                <i class="fal fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('income_types.edit', $incomeType->id) }}" 
                                                               class="btn btn-sm btn-warning" title="Düzenle">
                                                                <i class="fal fa-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm {{ $incomeType->is_active ? 'btn-secondary' : 'btn-success' }}" 
                                                                    onclick="toggleStatus({{ $incomeType->id }})" 
                                                                    title="{{ $incomeType->is_active ? 'Pasifleştir' : 'Aktifleştir' }}">
                                                                <i class="fal {{ $incomeType->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                            </button>
                                                            <a href="{{ route('income_types.delete', $incomeType->id) }}" 
                                                               class="btn btn-sm btn-danger" 
                                                               title="Sil"
                                                               onclick="return confirm('Bu gelir tipini silmek istediğinizden emin misiniz?\n\n\"{{ $incomeType->name }}\" gelir tipi silinecektir.\n\nBu işlem geri alınamaz!')">
                                                                <i class="fal fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        <div class="py-4">
                                                            <i class="fal fa-tags fa-3x text-muted mb-3"></i>
                                                            <h5 class="text-muted">Henüz gelir tipi bulunmamaktadır</h5>
                                                            <p class="text-muted">İlk gelir tipinizi oluşturmak için yukarıdaki butonu kullanın.</p>
                                                        </div>
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
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

<script>
$(document).ready(function() {
    $('#incomeTypesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json"
        },
        "pageLength": 25,
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": 7 }
        ]
    });
});

function toggleStatus(id) {
    if (confirm('Bu gelir tipinin durumunu değiştirmek istediğinizden emin misiniz?')) {
        $.ajax({
            url: '{{ url("income_types") }}/' + id + '/toggle-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Hata: ' + response.message);
                }
            },
            error: function() {
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            }
        });
    }
}

</script>

@include('layouts.footer')

