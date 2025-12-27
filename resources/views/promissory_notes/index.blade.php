@include('layouts.header')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="panel-container">
                    <div class="panel-content">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3>Senetler</h3>
                            <a href="{{ route('promissory_notes.create') }}" class="btn btn-primary">Yeni Senet Ekle</a>
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
                                        <th>Senet Numarası</th>
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
                                    @foreach($notes as $note)
                                    <tr>
                                        <td>{{ $note->note_number }}</td>
                                        <td>{{ $note->customer_name }}</td>
                                        <td>
                                            @if($note->type == 'verilen')
                                                <span class="badge badge-danger">Verilen</span>
                                            @else
                                                <span class="badge badge-success">Alınan</span>
                                            @endif
                                        </td>
                                        <td>{{ $note->bank_name }} @if($note->branch_name)<br><small>{{ $note->branch_name }}</small>@endif</td>
                                        <td>₺{{ number_format($note->amount, 2) }}</td>
                                        <td>{{ $note->issue_date }}</td>
                                        <td>{{ $note->maturity_date }}</td>
                                        <td>
                                            <span class="badge badge-{{ $note->status == 'PAID' ? 'success' : ($note->status == 'OVERDUE' ? 'danger' : 'warning') }}">
                                                @if($note->status == 'ACTIVE') Aktif
                                                @elseif($note->status == 'PAID') Ödendi
                                                @elseif($note->status == 'OVERDUE') Gecikmiş
                                                @elseif($note->status == 'CANCELLED') İptal Edildi
                                                @else {{ $note->status }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('promissory_notes.show', $note->id) }}" class="btn btn-sm btn-info">Görüntüle</a>
                                            <a href="{{ route('promissory_notes.edit', $note->id) }}" class="btn btn-sm btn-warning">Düzenle</a>
                                            <form action="{{ route('promissory_notes.destroy', $note->id) }}" method="POST" class="d-inline">
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
