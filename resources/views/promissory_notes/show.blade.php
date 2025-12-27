@include('layouts.header')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="panel-container">
                    <div class="panel-content">
                        <h3>Senet Detayları</h3>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Senet Numarası:</strong> {{ $note->note_number }}</p>
                                <p><strong>Müşteri:</strong> {{ $note->customer_name }}</p>
                                <p><strong>Hesap:</strong> {{ $note->account_name ?? 'Yok' }}</p>
                                <p><strong>Senet Türü:</strong>
                                    @if($note->type == 'verilen')
                                        <span class="badge badge-danger">Verilen Senet</span>
                                    @else
                                        <span class="badge badge-success">Alınan Senet</span>
                                    @endif
                                </p>
                                <p><strong>Banka Adı:</strong> {{ $note->bank_name }}</p>
                                <p><strong>Şube Adı:</strong> {{ $note->branch_name ?? 'Yok' }}</p>
                                <p><strong>Tutar:</strong> ₺{{ number_format($note->amount, 2) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Düzenleme Tarihi:</strong> {{ $note->issue_date }}</p>
                                <p><strong>Vade Tarihi:</strong> {{ $note->maturity_date }}</p>
                                <p><strong>Durum:</strong>
                                    <span class="badge badge-{{ $note->status == 'PAID' ? 'success' : ($note->status == 'OVERDUE' ? 'danger' : 'warning') }}">
                                        @if($note->status == 'ACTIVE') Aktif
                                        @elseif($note->status == 'PAID') Ödendi
                                        @elseif($note->status == 'OVERDUE') Gecikmiş
                                        @elseif($note->status == 'CANCELLED') İptal Edildi
                                        @else {{ $note->status }}
                                        @endif
                                    </span>
                                </p>
                                <p><strong>Açıklama:</strong> {{ $note->description ?? 'Yok' }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('promissory_notes.edit', $note->id) }}" class="btn btn-warning">Düzenle</a>
                            <a href="{{ route('promissory_notes.index') }}" class="btn btn-secondary">Listeye Dön</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
