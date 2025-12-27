@extends('layouts.header')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-star text-warning"></i> Sadakat Programı Yönetimi
                    </h3>
                    <div class="card-tools">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addPointsModal">
                            <i class="fas fa-plus"></i> Puan Ekle
                        </button>
                        <button class="btn btn-success" data-toggle="modal" data-target="#redeemPointsModal">
                            <i class="fas fa-gift"></i> Puan Kullan
                        </button>
                        <button class="btn btn-info" data-toggle="modal" data-target="#settingsModal">
                            <i class="fas fa-cog"></i> Ayarlar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $loyaltyStats['total_customers'] }}</h3>
                                    <p>Toplam Müşteri</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $loyaltyStats['loyalty_members'] }}</h3>
                                    <p>Sadakat Üyesi</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ number_format($loyaltyStats['total_points']) }}</h3>
                                    <p>Toplam Puan</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-coins"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $loyaltyStats['average_points'] }}</h3>
                                    <p>Ortalama Puan</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer List -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="loyaltyTable">
                            <thead>
                                <tr>
                                    <th>Müşteri</th>
                                    <th>Telefon</th>
                                    <th>E-posta</th>
                                    <th>Puan</th>
                                    <th>VIP</th>
                                    <th>Son İşlem</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
                                    <td>{{ $customer->phone ?? '-' }}</td>
                                    <td>{{ $customer->email ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-warning">{{ number_format($customer->parapuan) }}</span>
                                    </td>
                                    <td>
                                        @if($customer->is_vip)
                                            <span class="badge badge-success">VIP</span>
                                        @else
                                            <span class="badge badge-secondary">Normal</span>
                                        @endif
                                    </td>
                                    <td>{{ $customer->updated_at ? \Carbon\Carbon::parse($customer->updated_at)->format('d.m.Y H:i') : '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="viewCustomerDetails({{ $customer->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="addPoints({{ $customer->id }}, '{{ $customer->first_name }} {{ $customer->last_name }}')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="redeemPoints({{ $customer->id }}, '{{ $customer->first_name }} {{ $customer->last_name }}', {{ $customer->parapuan }})">
                                            <i class="fas fa-gift"></i>
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

<!-- Add Points Modal -->
<div class="modal fade" id="addPointsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Puan Ekle</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addPointsForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Müşteri</label>
                        <select class="form-control" name="customer_id" required>
                            <option value="">Müşteri Seçin</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->parapuan }} puan)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Puan Miktarı</label>
                        <input type="number" class="form-control" name="points" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Açıklama</label>
                        <input type="text" class="form-control" name="reason" placeholder="Puan ekleme sebebi" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Puan Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Redeem Points Modal -->
<div class="modal fade" id="redeemPointsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Puan Kullan</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="redeemPointsForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Müşteri</label>
                        <select class="form-control" name="customer_id" required>
                            <option value="">Müşteri Seçin</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" data-points="{{ $customer->parapuan }}">{{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->parapuan }} puan)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kullanılacak Puan</label>
                        <input type="number" class="form-control" name="points" min="1" required>
                        <small class="text-muted">Mevcut puan: <span id="currentPoints">0</span></small>
                    </div>
                    <div class="form-group">
                        <label>Açıklama</label>
                        <input type="text" class="form-control" name="reason" placeholder="Puan kullanma sebebi" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-warning">Puan Kullan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Sadakat Programı Ayarları</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="settingsForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>TL Başına Puan</label>
                                <input type="number" class="form-control" name="points_per_tl" step="0.01" min="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Puan Başına TL</label>
                                <input type="number" class="form-control" name="tl_per_point" step="0.0001" min="0.0001" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Minimum Kullanım Puanı</label>
                                <input type="number" class="form-control" name="min_redemption" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Maksimum Kullanım Yüzdesi (%)</label>
                                <input type="number" class="form-control" name="max_redemption_percent" min="1" max="100" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Doğum Günü Bonus Puanı</label>
                                <input type="number" class="form-control" name="birthday_bonus" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Program Aktif</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_active" value="1">
                                    <label class="form-check-label">Sadakat programını aktif et</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#loyaltyTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "order": [[3, "desc"]]
    });

    // Add Points Form
    $('#addPointsForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/loyalty/add-points',
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

    // Redeem Points Form
    $('#redeemPointsForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/loyalty/redeem-points',
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

    // Update current points when customer is selected
    $('select[name="customer_id"]').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const points = selectedOption.data('points') || 0;
        $('#currentPoints').text(points);
        $('input[name="points"]').attr('max', points);
    });

    // Settings Form
    $('#settingsForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/loyalty/settings',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#settingsModal').modal('hide');
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

function addPoints(customerId, customerName) {
    $('select[name="customer_id"]').val(customerId);
    $('#addPointsModal').modal('show');
}

function redeemPoints(customerId, customerName, points) {
    $('select[name="customer_id"]').val(customerId);
    $('#currentPoints').text(points);
    $('input[name="points"]').attr('max', points);
    $('#redeemPointsModal').modal('show');
}

function viewCustomerDetails(customerId) {
    // Implementation for viewing customer details
    console.log('View customer details:', customerId);
}
</script>
@endsection
