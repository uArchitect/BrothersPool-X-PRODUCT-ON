@include('layouts.header')

<style>
.income-detail {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 30px;
}

.detail-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.section-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #dee2e6;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #495057;
}

.detail-value {
    color: #6c757d;
}

.amount-display {
    font-size: 2rem;
    font-weight: bold;
    color: #28a745;
}
</style>

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="income-detail">
                            
                            <!-- Amount Display -->
                            <div class="text-center mb-4">
                                <div class="amount-display">{{ number_format($income->amount, 2) }} ₺</div>
                                <p class="text-muted mb-0">{{ $income->category_name ?? 'Kategori Yok' }}</p>
                            </div>

                            <!-- Basic Information -->
                            <div class="detail-section">
                                <h5 class="section-title">
                                    <i class="fal fa-info-circle mr-2"></i>Temel Bilgiler
                                </h5>
                                <div class="detail-row">
                                    <span class="detail-label">Gelir Numarası:</span>
                                    <span class="detail-value">{{ $income->income_number }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Tarih:</span>
                                    <span class="detail-value">{{ \Carbon\Carbon::parse($income->date)->format('d.m.Y') }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Durum:</span>
                                    <span class="detail-value">
                                        <span class="badge badge-success">{{ $income->status }}</span>
                                    </span>
                                </div>
                                @if($income->description)
                                    <div class="detail-row">
                                        <span class="detail-label">Açıklama:</span>
                                        <span class="detail-value">{{ $income->description }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Financial Information -->
                            <div class="detail-section">
                                <h5 class="section-title">
                                    <i class="fal fa-money-bill-wave mr-2"></i>Mali Bilgiler
                                </h5>
                                <div class="detail-row">
                                    <span class="detail-label">Kategori:</span>
                                    <span class="detail-value">{{ $income->category_name ?? 'Kategori Yok' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Hesap:</span>
                                    <span class="detail-value">{{ $income->account_name ?? 'Hesap Yok' }}</span>
                                </div>
                                @if($income->customer_name)
                                    <div class="detail-row">
                                        <span class="detail-label">Müşteri:</span>
                                        <span class="detail-value">{{ $income->customer_name }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Payment Information -->
                            <div class="detail-section">
                                <h5 class="section-title">
                                    <i class="fal fa-credit-card mr-2"></i>Ödeme Bilgileri
                                </h5>
                                <div class="detail-row">
                                    <span class="detail-label">Ödeme Yöntemi:</span>
                                    <span class="detail-value">
                                        @switch($income->payment_method)
                                            @case('cash')
                                                <i class="fal fa-money-bill mr-1"></i>Nakit
                                                @break
                                            @case('bank_transfer')
                                                <i class="fal fa-university mr-1"></i>Banka Havalesi
                                                @break
                                            @case('check')
                                                <i class="fal fa-file-invoice mr-1"></i>Çek
                                                @break
                                            @case('card')
                                                <i class="fal fa-credit-card mr-1"></i>Kredi Kartı
                                                @break
                                            @default
                                                <i class="fal fa-question-circle mr-1"></i>{{ ucfirst($income->payment_method ?? 'Belirtilmemiş') }}
                                        @endswitch
                                    </span>
                                </div>
                                @if($income->reference_number)
                                    <div class="detail-row">
                                        <span class="detail-label">Referans Numarası:</span>
                                        <span class="detail-value">{{ $income->reference_number }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- System Information -->
                            <div class="detail-section">
                                <h5 class="section-title">
                                    <i class="fal fa-cog mr-2"></i>Sistem Bilgileri
                                </h5>
                                <div class="detail-row">
                                    <span class="detail-label">Oluşturan:</span>
                                    <span class="detail-value">{{ $income->created_by_name ?? 'Bilinmiyor' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Oluşturulma Tarihi:</span>
                                    <span class="detail-value">{{ \Carbon\Carbon::parse($income->created_at)->format('d.m.Y H:i') }}</span>
                                </div>
                                @if($income->updated_at != $income->created_at)
                                    <div class="detail-row">
                                        <span class="detail-label">Son Güncelleme:</span>
                                        <span class="detail-value">{{ \Carbon\Carbon::parse($income->updated_at)->format('d.m.Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('incomes.index') }}" class="btn btn-secondary">
                                    <i class="fal fa-arrow-left mr-2"></i>Geri Dön
                                </a>
                                <div>
                                    <a href="{{ route('incomes.edit', $income->id) }}" class="btn btn-primary">
                                        <i class="fal fa-edit mr-2"></i>Düzenle
                                    </a>
                                    <button type="button" class="btn btn-danger ml-2" onclick="deleteIncome({{ $income->id }})">
                                        <i class="fal fa-trash mr-2"></i>Sil
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gelir Sil</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Bu gelir kaydını silmek istediğinizden emin misiniz?</p>
                <p class="text-danger"><strong>Bu işlem geri alınamaz!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteIncome(incomeId) {
    document.getElementById('deleteForm').action = '/incomes/' + incomeId;
    $('#deleteModal').modal('show');
}
</script>

@include('layouts.footer')
