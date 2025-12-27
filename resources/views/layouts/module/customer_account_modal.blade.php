@foreach($reservations as $reservation)
<!-- Cari Hesap Görünümü Modalı -->
<div class="modal fade" id="customerAccountModal{{$reservation->id}}" tabindex="-1" aria-labelledby="customerAccountModalLabel{{$reservation->id}}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="customerAccountModalLabel{{$reservation->id}}">Cari Hesap Görünümü - {{ $reservation->customer_name }}</h5>
                <button type="button" class="btn btn-sm text-white" data-bs-dismiss="modal" aria-label="Kapat" style="border: none; background: none;">
            <i class="fal fa-times"></i>
        </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h5>Müşteri Bilgileri</h5>
                    <p><strong>Adı:</strong> {{ $reservation->customer_name }}</p>
                    <p><strong>Telefon:</strong> {{ $reservation->customer->phone ?? 'N/A' }}</p>
                    <p><strong>E-posta:</strong> {{ $reservation->customer->email ?? 'N/A' }}</p>
                    <p><strong>Toplam Rezervasyon Tutarı:</strong> {{ number_format($reservation->total_price, 2) }} ₺</p>
                </div>

                <h5>Ödeme Geçmişi</h5>
                <div class="table-responsive">
                     
                </div>

            </div>
        </div>
    </div>
</div>
@endforeach
