@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='fal fa-chart-line'></i> Dashboard
        </h1>
    </div>

    <!-- Üst kartlar -->
    <div class="row mb-4">
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('financial.management') }}" class="text-white">
                <div class="p-4 bg-gradient-primary rounded-lg overflow-hidden position-relative text-white mb-g shadow-lg hover-scale-up">
                    <div class="d-flex align-items-center">
                        <div class="icon-stack fs-6 mr-3">
                            <i class="fal fa-chart-line"></i>
                        </div>
                        <div>
                            <h3 class="display-4 d-block l-h-n m-0 fw-500 counter">
                                ₺{{ number_format($dailyIncome, 2) }}
                                <small class="m-0 l-h-n opacity-70">Günlük Gelir</small>
                            </h3>
                            <div class="mt-2 fs-sm">
                                @if($dailyExpense > 0)
                                    <span class="text-info">
                                        <i class="fal fa-minus mr-1"></i> Gider: ₺{{ number_format($dailyExpense, 2) }}
                                    </span>
                                @else
                                    <span class="text-success">
                                        <i class="fal fa-check mr-1"></i> Kar: ₺{{ number_format($dailyIncome, 2) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('financial.management') }}" class="text-white">
                <div class="p-3 bg-danger-200 rounded overflow-hidden position-relative text-white mb-g shadow-sm">
                    <div class="">
                        <h3 class="display-4 d-block l-h-n m-0 fw-500">
                            ₺{{ number_format($dailyExpense ?? 0, 2) }}
                            <small class="m-0 l-h-n">Günlük Gider</small>
                        </h3>
                    </div>
                    <i class="fal fa-file-invoice-dollar position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1"
                        style="font-size:6rem"></i>
                </div>
            </a>
        </div>

        <!--Mevcut Toplam Kasa, Toplam Randevu, Toplam Hizmet, Toplam Müşteri-->

        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('financial.management') }}" class="text-white">
                <div class="p-3 bg-warning-200 rounded overflow-hidden position-relative text-white mb-g shadow-sm">
                    <div class="">
                        <h3 class="display-4 d-block l-h-n m-0 fw-500">
                            ₺{{ number_format($totalBalance ?? 0, 2) }}
                            <small class="m-0 l-h-n">Mevcut Toplam Kasa</small>
                        </h3>
                    </div>
                    <i class="fal fa-wallet position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1"
                        style="font-size:6rem"></i>
                </div>
            </a>
        </div>

        <!---Mevcut Müşteri Sayısı, Toplam Hizmet Sayısı, Toplam Randevu Sayısı, Toplam Gelir-->
        <div class="col-sm-6 col-xl-3">
            <div class="text-white">
                <div class="p-3 bg-info-200 rounded overflow-hidden position-relative text-white mb-g shadow-sm">
                    <div class="">
                        <h3 class="display-4 d-block l-h-n m-0 fw-500">
                            {{ $totalCustomers }}
                            <small class="m-0 l-h-n">Mevcut Müşteri Sayısı</small>
                        </h3>
                    </div>
                    <i class="fal fa-users position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1"
                        style="font-size:6rem"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Aylık Gelir Grafiği -->
        <div class="col-xl-6">
            <div id="panel-2" class="panel shadow-lg rounded-lg">
                <div class="panel-hdr bg-gradient-primary text-white">
                    <h2 class="fw-700">
                        <i class="fal fa-chart-line mr-2"></i>
                        Aylık Gelir Analizi
                    </h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip"
                            data-offset="0,10" data-original-title="Küçült"></button>
                        <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip"
                            data-offset="0,10" data-original-title="Tam Ekran"></button>
                    </div>
                </div>
                <div class="panel-container show">
                    <div class="panel-content p-0">
                        <div id="monthlyChart" style="width:100%; height:400px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hizmet Dağılımı Pasta Grafiği -->
        <div class="col-xl-6">
            <div id="panel-1" class="panel shadow-sm">
                <div class="panel-hdr bg-gradient-warning text-white">
                    <h2 class="fw-700">
                        <i class="fal fa-bell mr-2"></i>Vade Hatırlatmaları ({{ $upcomingChecks->count() + $upcomingPromissoryNotes->count() }})
                    </h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip"
                            data-offset="0,10" data-original-title="Küçült"></button>
                        <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip"
                            data-offset="0,10" data-original-title="Tam Ekran"></button>
                    </div>
                </div>
                <div class="panel-container show">
                    <div class="panel-content">
                        @if($upcomingChecks->count() > 0 || $upcomingPromissoryNotes->count() > 0)
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <div class="d-flex align-items-center">
                                    <div class="alert-icon">
                                        <i class="fal fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="flex-1 ml-2">
                                        <span class="h5">Vade Hatırlatmaları</span>
                                        <br>Yaklaşan vade tarihleri ve önemli hatırlatmalar.
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Tip</th>
                                            <th>Açıklama</th>
                                            <th>Müşteri</th>
                                            <th>Tutar</th>
                                            <th>Vade Tarihi</th>
                                            <th>Kalan Gün</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($upcomingChecks as $check)
                                        <tr>
                                            <td>
                                                <span class="badge badge-info">Çek</span>
                                            </td>
                                            <td>
                                                <strong>{{ $check->check_number }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $customer = DB::table('customers')->where('id', $check->customer_id)->first();
                                                @endphp
                                                {{ $customer->title ?? 'Müşteri' }}
                                            </td>
                                            <td>
                                                <span class="font-weight-bold text-primary">
                                                    ₺{{ number_format($check->amount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($check->maturity_date)->format('d.m.Y') }}
                                            </td>
                                            <td>
                                                @php
                                                    $daysLeft = now()->diffInDays($check->maturity_date, false);
                                                @endphp
                                                @if($daysLeft < 0)
                                                    <span class="badge badge-danger">
                                                        {{ abs($daysLeft) }} gün geçmiş
                                                    </span>
                                                @elseif($daysLeft == 0)
                                                    <span class="badge badge-warning">Bugün</span>
                                                @else
                                                    <span class="badge badge-{{ $daysLeft <= 3 ? 'warning' : 'info' }}">
                                                        {{ $daysLeft }} gün
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($check->status)
                                                    @case('BEKLEMEDE')
                                                        <span class="badge badge-warning">Beklemede</span>
                                                        @break
                                                    @case('TAHSIL_EDILDI')
                                                        <span class="badge badge-success">Tahsil Edildi</span>
                                                        @break
                                                    @case('IADE_EDILDI')
                                                        <span class="badge badge-danger">İade Edildi</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ $check->status }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    <p class="text-muted mb-0">Vadesi yaklaşan çek yok.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                        
                                        @forelse($upcomingPromissoryNotes as $note)
                                        <tr>
                                            <td>
                                                <span class="badge badge-warning">Senet</span>
                                            </td>
                                            <td>
                                                <strong>{{ $note->note_number }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $customer = DB::table('customers')->where('id', $note->customer_id)->first();
                                                @endphp
                                                {{ $customer->title ?? 'Müşteri' }}
                                            </td>
                                            <td>
                                                <span class="font-weight-bold text-primary">
                                                    ₺{{ number_format($note->amount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($note->maturity_date)->format('d.m.Y') }}
                                            </td>
                                            <td>
                                                @php
                                                    $daysLeft = now()->diffInDays($note->maturity_date, false);
                                                @endphp
                                                @if($daysLeft < 0)
                                                    <span class="badge badge-danger">
                                                        {{ abs($daysLeft) }} gün geçmiş
                                                    </span>
                                                @elseif($daysLeft == 0)
                                                    <span class="badge badge-warning">Bugün</span>
                                                @else
                                                    <span class="badge badge-{{ $daysLeft <= 3 ? 'warning' : 'info' }}">
                                                        {{ $daysLeft }} gün
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($note->status)
                                                    @case('AKTIF')
                                                        <span class="badge badge-warning">Aktif</span>
                                                        @break
                                                    @case('ODENDI')
                                                        <span class="badge badge-success">Ödendi</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ $note->status }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    <p class="text-muted mb-0">Vadesi yaklaşan çek yok.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                        
                                        @forelse($upcomingPromissoryNotes as $note)
                                        <tr>
                                            <td>
                                                <span class="badge badge-warning">Senet</span>
                                            </td>
                                            <td>
                                                <strong>{{ $note->note_number }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $customer = DB::table('customers')->where('id', $note->customer_id)->first();
                                                @endphp
                                                {{ $customer->title ?? 'Müşteri' }}
                                            </td>
                                            <td>
                                                <span class="font-weight-bold text-primary">
                                                    ₺{{ number_format($note->amount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($note->maturity_date)->format('d.m.Y') }}
                                            </td>
                                            <td>
                                                @php
                                                    $daysLeft = now()->diffInDays($note->maturity_date, false);
                                                @endphp
                                                @if($daysLeft < 0)
                                                    <span class="badge badge-danger">
                                                        {{ abs($daysLeft) }} gün geçmiş
                                                    </span>
                                                @elseif($daysLeft == 0)
                                                    <span class="badge badge-warning">Bugün</span>
                                                @else
                                                    <span class="badge badge-{{ $daysLeft <= 3 ? 'warning' : 'info' }}">
                                                        {{ $daysLeft }} gün
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($note->status)
                                                    @case('AKTIF')
                                                        <span class="badge badge-warning">Aktif</span>
                                                        @break
                                                    @case('ODENDI')
                                                        <span class="badge badge-success">Ödendi</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ $note->status }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    <p class="text-muted mb-0">Vadesi yaklaşan senet yok.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fal fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="text-muted">Vade Hatırlatması Yok</h5>
                                <p class="text-muted">Yaklaşan vade tarihi bulunmuyor.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Gerekli scriptler -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.16.0/d3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof c3 === 'undefined' || typeof d3 === 'undefined') {
            console.error('C3.js veya D3.js kütüphaneleri yüklenemedi!');
            return;
        }
        init();
    });

    function init() {
        try {
            aylikGelir();
        } catch (error) {
            console.error('Grafik oluşturma hatası:', error);
        }
    }

    function aylikGelir() {
        var gelirVerisi = {!! json_encode($aylikGelirVerisi) !!};
        var giderVerisi = {!! json_encode($aylikGiderVerisi) !!};
        var karVerisi = {!! json_encode($aylikKarVerisi) !!};
        
        var chart = c3.generate({
            bindto: '#monthlyChart',
            data: {
                columns: [
                    ['Gelir'].concat(gelirVerisi),
                    ['Gider'].concat(giderVerisi),
                    ['Net Kar'].concat(karVerisi)
                ],
                types: {
                    'Gelir': 'bar',
                    'Gider': 'bar',
                    'Net Kar': 'line'
                },
                colors: {
                    'Gelir': '#2ecc71',
                    'Gider': '#e74c3c',
                    'Net Kar': '#3498db'
                },
                groups: [
                    ['Gelir', 'Gider']
                ]
            },
            axis: {
                x: {
                    type: 'category',
                    categories: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'],
                    tick: {
                        rotate: 0,
                        multiline: false
                    }
                },
                y: {
                    label: {
                        text: 'Tutar (₺)',
                        position: 'outer-middle'
                    },
                    tick: {
                        format: function(d) {
                            return new Intl.NumberFormat('tr-TR', {
                                style: 'currency',
                                currency: 'TRY',
                                minimumFractionDigits: 0
                            }).format(d);
                        }
                    }
                }
            },
            bar: {
                width: {
                    ratio: 0.5
                }
            },
            grid: {
                y: {
                    show: true,
                    lines: [{value: 0, text: ''}]
                }
            },
            tooltip: {
                format: {
                    title: function (d) {
                        var aylar = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 
                                    'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
                        return aylar[d] + ' ' + new Date().getFullYear();
                    },
                    value: function(value) {
                        return new Intl.NumberFormat('tr-TR', {
                            style: 'currency',
                            currency: 'TRY',
                            minimumFractionDigits: 0
                        }).format(value);
                    }
                }
            },
            legend: {
                position: 'inset',
                inset: {
                    anchor: 'top-right',
                    x: 20,
                    y: 10,
                    step: 1
                }
            }
        });
    }
</script>

<style>
.hover-scale-up {
    transition: transform 0.2s ease;
}
.hover-scale-up:hover {
    transform: translateY(-5px);
}
.bg-gradient-primary {
    background: linear-gradient(45deg, #4361ee, #3bc9db);
}
.counter {
    font-size: 2.5rem;
}
.icon-stack {
    font-size: 2.5rem;
    opacity: 0.8;
}
</style>

@include('layouts.footer')
