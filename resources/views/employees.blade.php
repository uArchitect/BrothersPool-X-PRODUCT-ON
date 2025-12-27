@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Çalışanlar ve Personeller</li>
        <li class="breadcrumb-item active">Çalışanlar ve Personeller</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr d-flex justify-content-between align-items-center">
                    <h2>Çalışanlar Listesi <span class="fw-300"><i>Yönetim</i></span></h2>
                    <a href="{{ route('employees.create') }}" class="btn btn-success waves-effect waves-themed">
                        <i class="fal fa-plus mr-1"></i>
                        Yeni Çalışan Ekle
                    </a>
                    </div>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="panel-tag mb-4">
                            Bu tablonun amacı, salonunuzdaki çalışanları ve personelleri kolayca yönetebilmenizdir.
                            Çalışanlarınızın detaylarına, düzenlemelere ve silme işlemlerine kolayca erişebilirsiniz.
                        </div>

                        <div class="table-responsive">
                            <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
                                <thead class="bg-highlight">
                                    <tr>
                                        <th>Çalışan Adı</th>
                                        <th>Grup</th>
                                        <th>Görev</th>
                                        <th>Telefon</th>
                                        <th>İşe Giriş Tarihi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employees as $employee)
                                    @php
                                        $group = $employee_groups->where('id', $employee->group_id)->first();
                                        $position = $employee_positions->where('id', $employee->position_id)->first();
                                    @endphp
                                    <tr data-id="{{ $employee->id }}">
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $group->name ?? 'N/A' }}</td>
                                        <td>{{ $position->name ?? 'N/A' }}</td>
                                        <td>{{ $employee->phone }}</td>
                                        <td>{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') : '' }}</td>
                                        <td>
                                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary btn-sm" title="Çalışanı Düzenle">
                                                <i class="fal fa-pencil-alt"></i>
                                            </a>
                                            <form action="{{ route('employees.delete') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $employee->id }}">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Çalışanı Sil" onclick="return confirm('Bu çalışanı silmek istediğinize emin misiniz?');">
                                                    <i class="fal fa-trash"></i>
                                                </button>
                                            </form>
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
</main>

@include('layouts.footer')

<!-- DataTables Bundle Script - Required for this page -->
<script src="{{ asset('js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script>
    $(function() {
        $('#dt-basic-example').DataTable({
            orderCellsTop: true,
            fixedHeader: true,
            responsive: true,
            processing: false,
            serverSide: false,
            order: [],
            columnDefs: [ { orderable: false, targets: [5] } ],
            language: {
                paginate: { first: "İlk", last: "Son", next: "Sonraki", previous: "Önceki" },
                info: "Gösterilen: _START_ - _END_ / _TOTAL_",
                infoEmpty: "Gösterilecek kayıt yok",
                zeroRecords: "Eşleşen kayıt bulunamadı",
                lengthMenu: "Sayfa başına _MENU_ kayıt göster"
            }
        });
    });
</script>

@if(session('success'))
<script>
    safeReady(function() {
        // Session mesajının daha önce gösterilip gösterilmediğini kontrol et
        var sessionKey = 'success_message_shown_' + @json(session('success'));
        if (!localStorage.getItem(sessionKey)) {
            showSuccess(@json(session('success')));
            // Mesajın gösterildiğini işaretle (5 saniye sonra otomatik temizlenecek)
            localStorage.setItem(sessionKey, 'true');
            setTimeout(function() {
                localStorage.removeItem(sessionKey);
            }, 5000);
        }
    });
</script>
@endif
@if(session('error'))
<script>
    safeReady(function() {
        // Error mesajının daha önce gösterilip gösterilmediğini kontrol et
        var errorKey = 'error_message_shown_' + @json(session('error'));
        if (!localStorage.getItem(errorKey)) {
            showError(@json(session('error')));
            // Mesajın gösterildiğini işaretle (5 saniye sonra otomatik temizlenecek)
            localStorage.setItem(errorKey, 'true');
            setTimeout(function() {
                localStorage.removeItem(errorKey);
            }, 5000);
        }
    });
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>



