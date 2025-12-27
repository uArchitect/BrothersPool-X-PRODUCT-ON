@extends('layouts.header')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock text-primary"></i> Vardiya Yönetimi
                    </h3>
                    <div class="card-tools">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addShiftModal">
                            <i class="fas fa-plus"></i> Vardiya Ekle
                        </button>
                        <button class="btn btn-info" onclick="refreshShifts()">
                            <i class="fas fa-sync"></i> Yenile
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $shiftStats['total_shifts'] }}</h3>
                                    <p>Toplam Vardiya</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $shiftStats['active_shifts'] }}</h3>
                                    <p>Aktif Vardiya</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-play"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $shiftStats['completed_shifts'] }}</h3>
                                    <p>Tamamlanan</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $shiftStats['today_shifts'] }}</h3>
                                    <p>Bugünkü Vardiyalar</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary active" data-filter="all">Tümü</button>
                                <button type="button" class="btn btn-outline-success" data-filter="active">Aktif</button>
                                <button type="button" class="btn btn-outline-warning" data-filter="scheduled">Planlanan</button>
                                <button type="button" class="btn btn-outline-info" data-filter="completed">Tamamlanan</button>
                                <button type="button" class="btn btn-outline-danger" data-filter="cancelled">İptal</button>
                            </div>
                        </div>
                    </div>

                    <!-- Shifts Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="shiftsTable">
                            <thead>
                                <tr>
                                    <th>Çalışan</th>
                                    <th>Tarih</th>
                                    <th>Başlangıç</th>
                                    <th>Bitiş</th>
                                    <th>Gerçek Başlangıç</th>
                                    <th>Gerçek Bitiş</th>
                                    <th>Mola (dk)</th>
                                    <th>Durum</th>
                                    <th>Notlar</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shifts as $shift)
                                <tr data-status="{{ $shift->status }}">
                                    <td>{{ $shift->employee_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($shift->shift_date)->format('d.m.Y') }}</td>
                                    <td>{{ $shift->start_time }}</td>
                                    <td>{{ $shift->end_time }}</td>
                                    <td>{{ $shift->actual_start_time ?? '-' }}</td>
                                    <td>{{ $shift->actual_end_time ?? '-' }}</td>
                                    <td>{{ $shift->break_duration }}</td>
                                    <td>
                                        @switch($shift->status)
                                            @case('scheduled')
                                                <span class="badge badge-secondary">Planlandı</span>
                                                @break
                                            @case('active')
                                                <span class="badge badge-success">Aktif</span>
                                                @break
                                            @case('completed')
                                                <span class="badge badge-info">Tamamlandı</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge badge-danger">İptal</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ $shift->notes ?? '-' }}</td>
                                    <td>
                                        @if($shift->status === 'scheduled')
                                            <button class="btn btn-sm btn-success" onclick="startShift({{ $shift->id }})">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        @elseif($shift->status === 'active')
                                            <button class="btn btn-sm btn-info" onclick="completeShift({{ $shift->id }})">
                                                <i class="fas fa-stop"></i>
                                            </button>
                                        @endif
                                        
                                        @if(in_array($shift->status, ['scheduled', 'active']))
                                            <button class="btn btn-sm btn-danger" onclick="cancelShift({{ $shift->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        
                                        <button class="btn btn-sm btn-warning" onclick="viewShiftDetails({{ $shift->id }})">
                                            <i class="fas fa-eye"></i>
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

<!-- Add Shift Modal -->
<div class="modal fade" id="addShiftModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Yeni Vardiya Ekle</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addShiftForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Çalışan</label>
                        <select class="form-control" name="employee_id" required>
                            <option value="">Çalışan Seçin</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tarih</label>
                        <input type="date" class="form-control" name="shift_date" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Başlangıç Saati</label>
                                <input type="time" class="form-control" name="start_time" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bitiş Saati</label>
                                <input type="time" class="form-control" name="end_time" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mola Süresi (dakika)</label>
                        <input type="number" class="form-control" name="break_duration" min="0" max="480" value="0">
                    </div>
                    <div class="form-group">
                        <label>Notlar</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Vardiya notları..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Vardiya Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#shiftsTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "order": [[1, "desc"], [2, "asc"]]
    });

    // Filter buttons
    $('[data-filter]').on('click', function() {
        const filter = $(this).data('filter');
        
        $('[data-filter]').removeClass('active');
        $(this).addClass('active');
        
        if (filter === 'all') {
            $('#shiftsTable tbody tr').show();
        } else {
            $('#shiftsTable tbody tr').hide();
            $(`#shiftsTable tbody tr[data-status="${filter}"]`).show();
        }
    });

    // Add Shift Form
    $('#addShiftForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/shifts',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Hata oluştu: ' + xhr.responseJSON.message);
            }
        });
    });
});

function startShift(shiftId) {
    if (confirm('Bu vardiyayı başlatmak istediğinizden emin misiniz?')) {
        updateShiftStatus(shiftId, 'active');
    }
}

function completeShift(shiftId) {
    if (confirm('Bu vardiyayı tamamlamak istediğinizden emin misiniz?')) {
        updateShiftStatus(shiftId, 'completed');
    }
}

function cancelShift(shiftId) {
    if (confirm('Bu vardiyayı iptal etmek istediğinizden emin misiniz?')) {
        updateShiftStatus(shiftId, 'cancelled');
    }
}

function updateShiftStatus(shiftId, status) {
    $.ajax({
        url: `/shifts/${shiftId}/status`,
        method: 'PUT',
        data: { status: status },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                location.reload();
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            toastr.error('Hata oluştu: ' + xhr.responseJSON.message);
        }
    });
}

function viewShiftDetails(shiftId) {
    // Implementation for viewing shift details
    console.log('View shift details:', shiftId);
}

function refreshShifts() {
    location.reload();
}
</script>
@endsection
