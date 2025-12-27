@extends('layouts.header')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bell text-warning"></i> Bildirim Yönetimi
                    </h3>
                    <div class="card-tools">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addNotificationModal">
                            <i class="fas fa-plus"></i> Bildirim Gönder
                        </button>
                        <button class="btn btn-success" onclick="markAllAsRead()">
                            <i class="fas fa-check-double"></i> Tümünü Okundu İşaretle
                        </button>
                        <button class="btn btn-info" onclick="refreshNotifications()">
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
                                    <h3>{{ $notificationStats['total'] }}</h3>
                                    <p>Toplam Bildirim</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $notificationStats['unread'] }}</h3>
                                    <p>Okunmamış</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $notificationStats['today'] }}</h3>
                                    <p>Bugün</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $notificationStats['this_week'] }}</h3>
                                    <p>Bu Hafta</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary active" data-filter="all">Tümü</button>
                                <button type="button" class="btn btn-outline-warning" data-filter="unread">Okunmamış</button>
                                <button type="button" class="btn btn-outline-info" data-filter="info">Bilgi</button>
                                <button type="button" class="btn btn-outline-warning" data-filter="warning">Uyarı</button>
                                <button type="button" class="btn btn-outline-danger" data-filter="error">Hata</button>
                                <button type="button" class="btn btn-outline-success" data-filter="success">Başarı</button>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications List -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="notificationsTable">
                            <thead>
                                <tr>
                                    <th>Durum</th>
                                    <th>Başlık</th>
                                    <th>Mesaj</th>
                                    <th>Tür</th>
                                    <th>Öncelik</th>
                                    <th>Hedef</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notifications as $notification)
                                <tr data-type="{{ $notification->type }}" data-read="{{ $notification->is_read }}">
                                    <td>
                                        @if($notification->is_read)
                                            <span class="badge badge-success">Okundu</span>
                                        @else
                                            <span class="badge badge-warning">Okunmadı</span>
                                        @endif
                                    </td>
                                    <td>{{ $notification->title }}</td>
                                    <td>{{ Str::limit($notification->message, 50) }}</td>
                                    <td>
                                        @switch($notification->type)
                                            @case('info')
                                                <span class="badge badge-info">Bilgi</span>
                                                @break
                                            @case('warning')
                                                <span class="badge badge-warning">Uyarı</span>
                                                @break
                                            @case('error')
                                                <span class="badge badge-danger">Hata</span>
                                                @break
                                            @case('success')
                                                <span class="badge badge-success">Başarı</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($notification->priority)
                                            @case('low')
                                                <span class="badge badge-secondary">Düşük</span>
                                                @break
                                            @case('medium')
                                                <span class="badge badge-primary">Orta</span>
                                                @break
                                            @case('high')
                                                <span class="badge badge-warning">Yüksek</span>
                                                @break
                                            @case('urgent')
                                                <span class="badge badge-danger">Acil</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($notification->target_type)
                                            @case('all')
                                                <span class="badge badge-primary">Tümü</span>
                                                @break
                                            @case('role')
                                                <span class="badge badge-info">Rol</span>
                                                @break
                                            @case('user')
                                                <span class="badge badge-success">Kullanıcı</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($notification->created_at)->format('d.m.Y H:i') }}</td>
                                    <td>
                                        @if(!$notification->is_read)
                                            <button class="btn btn-sm btn-success" onclick="markAsRead({{ $notification->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-sm btn-info" onclick="viewNotification({{ $notification->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteNotification({{ $notification->id }})">
                                            <i class="fas fa-trash"></i>
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

<!-- Add Notification Modal -->
<div class="modal fade" id="addNotificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Yeni Bildirim Gönder</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addNotificationForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Başlık</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tür</label>
                                <select class="form-control" name="type" required>
                                    <option value="info">Bilgi</option>
                                    <option value="warning">Uyarı</option>
                                    <option value="error">Hata</option>
                                    <option value="success">Başarı</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hedef</label>
                                <select class="form-control" name="target_type" required>
                                    <option value="all">Tüm Kullanıcılar</option>
                                    <option value="role">Rol</option>
                                    <option value="user">Belirli Kullanıcı</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Öncelik</label>
                                <select class="form-control" name="priority" required>
                                    <option value="low">Düşük</option>
                                    <option value="medium" selected>Orta</option>
                                    <option value="high">Yüksek</option>
                                    <option value="urgent">Acil</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mesaj</label>
                        <textarea class="form-control" name="message" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Bildirim Gönder</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#notificationsTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "order": [[6, "desc"]]
    });

    // Filter buttons
    $('[data-filter]').on('click', function() {
        const filter = $(this).data('filter');
        
        $('[data-filter]').removeClass('active');
        $(this).addClass('active');
        
        if (filter === 'all') {
            $('#notificationsTable tbody tr').show();
        } else if (filter === 'unread') {
            $('#notificationsTable tbody tr').hide();
            $('#notificationsTable tbody tr[data-read="0"]').show();
        } else {
            $('#notificationsTable tbody tr').hide();
            $(`#notificationsTable tbody tr[data-type="${filter}"]`).show();
        }
    });

    // Add Notification Form
    $('#addNotificationForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/notifications',
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

function markAsRead(notificationId) {
    $.ajax({
        url: `/notifications/${notificationId}/read`,
        method: 'PUT',
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

function markAllAsRead() {
    if (confirm('Tüm bildirimleri okundu olarak işaretlemek istediğinizden emin misiniz?')) {
        $.ajax({
            url: '/notifications/mark-all-read',
            method: 'PUT',
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
}

function deleteNotification(notificationId) {
    if (confirm('Bu bildirimi silmek istediğinizden emin misiniz?')) {
        $.ajax({
            url: `/notifications/${notificationId}`,
            method: 'DELETE',
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
}

function viewNotification(notificationId) {
    // Implementation for viewing notification details
    console.log('View notification:', notificationId);
}

function refreshNotifications() {
    location.reload();
}
</script>
@endsection
