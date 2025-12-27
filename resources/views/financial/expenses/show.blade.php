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
                                                <strong>Gider Numarası:</strong><br>
                                                <span class="text-primary">{{ $expense->expense_number }}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Tarih:</strong><br>
                                                {{ \Carbon\Carbon::parse($expense->date)->format('d.m.Y') }}
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <strong>Gider Türü:</strong><br>
                                                {{ $expense->expense_type_name ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <strong>Hesap:</strong><br>
                                                {{ $expense->account_name ?? 'N/A' }}
                                            </div>
                                            @if(isset($expense->reference_number) && $expense->reference_number)
                                                <div class="col-md-6 mt-3">
                                                    <strong>Referans Numarası:</strong><br>
                                                    {{ $expense->reference_number }}
                                                </div>
                                            @endif
                                            @if(isset($expense->employee_name) && $expense->employee_name)
                                                <div class="col-md-6 mt-3">
                                                    <strong>Çalışan:</strong><br>
                                                    {{ $expense->employee_name }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Gider Kalemleri -->
                                @if($expenseItems && $expenseItems->count() > 0)
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fal fa-list mr-2"></i>Gider Kalemleri
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Kalem Adı</th>
                                                            <th>Birim Fiyat</th>
                                                            <th>Miktar</th>
                                                            <th>Tutar</th>
                                                            <th>Açıklama</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($expenseItems as $item)
                                                            <tr>
                                                                <td>{{ $item->item_name }}</td>
                                                                <td class="text-right">₺{{ number_format($item->unit_price, 2) }}</td>
                                                                <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                                                                <td class="text-right">₺{{ number_format($item->amount, 2) }}</td>
                                                                <td>{{ $item->description ?? '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-primary">
                                                            <th colspan="3">TOPLAM</th>
                                                            <th class="text-right">₺{{ number_format($expense->amount, 2) }}</th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Açıklama -->
                                @if($expense->description)
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fal fa-comment mr-2"></i>Açıklama
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <p>{{ $expense->description }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <!-- Toplam Tutar -->
                                <div class="card mb-4">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fal fa-calculator mr-2"></i>Toplam Tutar
                                        </h5>
                                    </div>
                                    <div class="card-body text-center">
                                        <h2 class="text-danger">₺{{ number_format($expense->amount, 2) }}</h2>
                                    </div>
                                </div>

                                <!-- Fatura Fotoğrafı -->
                                @if($expense->receipt_image ?? null)
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fal fa-camera mr-2"></i>Fatura Fotoğrafı
                                            </h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <img src="{{ asset('images/' . $expense->receipt_image) }}" 
                                                 alt="Fatura Fotoğrafı" class="img-fluid rounded" 
                                                 style="max-height: 300px;">
                                        </div>
                                    </div>
                                @endif

                                <!-- Sistem Bilgileri -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fal fa-info mr-2"></i>Sistem Bilgileri
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <small class="text-muted">
                                            <strong>Oluşturma Tarihi:</strong> {{ \Carbon\Carbon::parse($expense->created_at)->format('d.m.Y H:i') }}<br>
                                            @if($expense->updated_at != $expense->created_at)
                                                <strong>Güncelleme Tarihi:</strong> {{ \Carbon\Carbon::parse($expense->updated_at)->format('d.m.Y H:i') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>

                                <!-- İşlemler -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fal fa-cogs mr-2"></i>İşlemler
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-warning">
                                                <i class="fal fa-edit mr-1"></i>Düzenle
                                            </a>
                                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                                                <i class="fal fa-arrow-left mr-1"></i>Geri Dön
                                            </a>
                                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" 
                                                  onsubmit="return confirm('Bu gideri silmek istediğinizden emin misiniz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger w-100">
                                                    <i class="fal fa-trash mr-1"></i>Sil
                                                </button>
                                            </form>
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
</div>

@include('layouts.footer')

