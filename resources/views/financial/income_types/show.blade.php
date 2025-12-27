@include('layouts.header')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="panel-container">
                    <div class="panel-content">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Temel Bilgiler -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fal fa-info-circle mr-2"></i>Temel Bilgiler
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Gelir Tipi Adı:</strong><br>
                                                <span class="text-primary h5">{{ $incomeType->name }}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Durum:</strong><br>
                                                @if($incomeType->is_active)
                                                    <span class="badge badge-success badge-lg">Aktif</span>
                                                @else
                                                    <span class="badge badge-secondary badge-lg">Pasif</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <strong>Sıralama:</strong><br>
                                                <span class="text-info">{{ $incomeType->sort_order ?? 0 }}</span>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <strong>Oluşturma Tarihi:</strong><br>
                                                {{ \Carbon\Carbon::parse($incomeType->created_at)->format('d.m.Y H:i') }}
                                            </div>
                                            @if($incomeType->updated_at != $incomeType->created_at)
                                                <div class="col-md-6 mt-3">
                                                    <strong>Son Güncelleme:</strong><br>
                                                    {{ \Carbon\Carbon::parse($incomeType->updated_at)->format('d.m.Y H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                        
                                        @if($incomeType->description)
                                            <hr>
                                            <div class="mt-3">
                                                <strong>Açıklama:</strong><br>
                                                <p class="mt-2">{{ $incomeType->description }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- İstatistikler -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fal fa-chart-bar mr-2"></i>İstatistikler
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="text-center">
                                                    <h3 class="text-info">{{ $incomesCount }}</h3>
                                                    <p class="text-muted mb-0">Toplam Gelir Kaydı</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="text-center">
                                                    <h3 class="text-success">₺{{ number_format($totalAmount, 2) }}</h3>
                                                    <p class="text-muted mb-0">Toplam Tutar</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($incomesCount > 0)
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="text-center">
                                                        <h4 class="text-primary">₺{{ number_format($totalAmount / $incomesCount, 2) }}</h4>
                                                        <p class="text-muted mb-0">Ortalama Gelir</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="text-center">
                                                        <h4 class="text-warning">{{ \Carbon\Carbon::parse($incomeType->created_at)->diffInDays(now()) }}</h4>
                                                        <p class="text-muted mb-0">Gün Önce Oluşturuldu</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Son Gelirler -->
                                @if($incomesCount > 0)
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fal fa-list mr-2"></i>Son Gelirler
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $recentIncomes = DB::table('incomes')
                                                    ->where('income_type_id', $incomeType->id)
                                                    ->orderBy('created_at', 'desc')
                                                    ->limit(5)
                                                    ->get();
                                            @endphp
                                            
                                            @if($recentIncomes->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Gelir No</th>
                                                                <th>Tarih</th>
                                                                <th>Tutar</th>
                                                                <th>Durum</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($recentIncomes as $income)
                                                                <tr>
                                                                    <td>{{ $income->income_number ?? 'N/A' }}</td>
                                                                    <td>{{ \Carbon\Carbon::parse($income->date)->format('d.m.Y') }}</td>
                                                                    <td class="text-success">₺{{ number_format($income->amount, 2) }}</td>
                                                                    <td>
                                                                        @if($income->status == 'TAMAMLANDI')
                                                                            <span class="badge badge-success">Tamamlandı</span>
                                                                        @elseif($income->status == 'BEKLEMEDE')
                                                                            <span class="badge badge-warning">Beklemede</span>
                                                                        @else
                                                                            <span class="badge badge-secondary">{{ $income->status }}</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted text-center">Henüz bu gelir tipine ait gelir kaydı bulunmamaktadır.</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <!-- İşlemler -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fal fa-cogs mr-2"></i>İşlemler
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('income_types.edit', $incomeType->id) }}" class="btn btn-warning">
                                                <i class="fal fa-edit mr-1"></i>Düzenle
                                            </a>
                                            <a href="{{ route('income_types.index') }}" class="btn btn-secondary">
                                                <i class="fal fa-arrow-left mr-1"></i>Geri Dön
                                            </a>
                                            <form action="{{ route('income_types.destroy', $incomeType->id) }}" method="POST" 
                                                  onsubmit="return confirm('Bu gelir tipini silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger w-100">
                                                    <i class="fal fa-trash mr-1"></i>Sil
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Durum Değiştir -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fal fa-toggle-on mr-2"></i>Durum Yönetimi
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center">
                                            @if($incomeType->is_active)
                                                <button type="button" class="btn btn-secondary" onclick="toggleStatus({{ $incomeType->id }})">
                                                    <i class="fal fa-eye-slash mr-1"></i>Pasifleştir
                                                </button>
                                                <small class="form-text text-muted mt-2">
                                                    Bu gelir tipi şu anda aktif durumda.
                                                </small>
                                            @else
                                                <button type="button" class="btn btn-success" onclick="toggleStatus({{ $incomeType->id }})">
                                                    <i class="fal fa-eye mr-1"></i>Aktifleştir
                                                </button>
                                                <small class="form-text text-muted mt-2">
                                                    Bu gelir tipi şu anda pasif durumda.
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Bilgi -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fal fa-info mr-2"></i>Bilgi
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <small class="text-muted">
                                            <strong>ID:</strong> {{ $incomeType->id }}<br>
                                            <strong>Oluşturulma:</strong> {{ \Carbon\Carbon::parse($incomeType->created_at)->format('d.m.Y H:i') }}<br>
                                            @if($incomeType->updated_at != $incomeType->created_at)
                                                <strong>Güncellenme:</strong> {{ \Carbon\Carbon::parse($incomeType->updated_at)->format('d.m.Y H:i') }}
                                            @endif
                                        </small>
                                    </div>
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
<script>
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

