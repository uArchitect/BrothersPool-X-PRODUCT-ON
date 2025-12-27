@include('layouts.header')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="panel-container">
                    <div class="panel-content">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3>Çekler</h3>
                            <a href="{{ route('checks.create') }}" class="btn btn-primary">Yeni Çek Ekle</a>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Çek Numarası</th>
                                        <th>Müşteri</th>
                                        <th>Tür</th>
                                        <th>Banka</th>
                                        <th>Tutar</th>
                                        <th>Düzenleme Tarihi</th>
                                        <th>Vade Tarihi</th>
                                        <th>Durum</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($checks as $check)
                                    <tr>
                                        <td>{{ $check->check_number }}</td>
                                        <td>{{ $check->customer_name }}</td>
                                        <td>
                                            @if($check->type == 'verilen')
                                                <span class="badge badge-danger">Verilen</span>
                                            @else
                                                <span class="badge badge-success">Alınan</span>
                                            @endif
                                        </td>
                                        <td>{{ $check->bank_name }} @if($check->branch_name)<br><small>{{ $check->branch_name }}</small>@endif</td>
                                        <td>₺{{ number_format($check->amount, 2) }}</td>
                                        <td>{{ $check->issue_date }}</td>
                                        <td>{{ $check->maturity_date }}</td>
                                        <td>
                                            <span class="badge badge-{{ $check->status == 'CLEARED' ? 'success' : ($check->status == 'BOUNCED' ? 'danger' : 'warning') }}">
                                                @if($check->status == 'PENDING') Beklemede
                                                @elseif($check->status == 'CLEARED') Tahsil Edildi
                                                @elseif($check->status == 'BOUNCED') Karşılıksız
                                                @elseif($check->status == 'CANCELLED') İptal Edildi
                                                @else {{ $check->status }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('checks.show', $check->id) }}" class="btn btn-sm btn-info">Görüntüle</a>
                                            <a href="{{ route('checks.edit', $check->id) }}" class="btn btn-sm btn-warning">Düzenle</a>
                                            <form action="{{ route('checks.destroy', $check->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Emin misiniz?')">Sil</button>
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
</div>
@include('layouts.footer')
