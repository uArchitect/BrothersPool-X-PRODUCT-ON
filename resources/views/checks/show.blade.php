@include('layouts.header')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="panel-container">
                    <div class="panel-content">
                        <h3>Çek Detayları</h3>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Çek Numarası:</strong> {{ $check->check_number }}</p>
                                <p><strong>Müşteri:</strong> {{ $check->customer_name }}</p>
                                <p><strong>Hesap:</strong> {{ $check->account_name ?? 'Yok' }}</p>
                                <p><strong>Çek Türü:</strong>
                                    @if($check->type == 'verilen')
                                        <span class="badge badge-danger">Verilen Çek</span>
                                    @else
                                        <span class="badge badge-success">Alınan Çek</span>
                                    @endif
                                </p>
                                <p><strong>Banka Adı:</strong> {{ $check->bank_name }}</p>
                                <p><strong>Şube Adı:</strong> {{ $check->branch_name ?? 'Yok' }}</p>
                                <p><strong>Tutar:</strong> ₺{{ number_format($check->amount, 2) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Düzenleme Tarihi:</strong> {{ $check->issue_date }}</p>
                                <p><strong>Vade Tarihi:</strong> {{ $check->maturity_date }}</p>
                                <p><strong>Durum:</strong>
                                    <span class="badge badge-{{ $check->status == 'CLEARED' ? 'success' : ($check->status == 'BOUNCED' ? 'danger' : 'warning') }}">
                                        @if($check->status == 'PENDING') Beklemede
                                        @elseif($check->status == 'CLEARED') Tahsil Edildi
                                        @elseif($check->status == 'BOUNCED') Karşılıksız
                                        @elseif($check->status == 'CANCELLED') İptal Edildi
                                        @else {{ $check->status }}
                                        @endif
                                    </span>
                                </p>
                                <p><strong>Açıklama:</strong> {{ $check->description ?? 'Yok' }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('checks.edit', $check->id) }}" class="btn btn-warning">Düzenle</a>
                            <a href="{{ route('checks.index') }}" class="btn btn-secondary">Listeye Dön</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
