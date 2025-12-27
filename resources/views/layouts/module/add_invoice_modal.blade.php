@foreach($reservations as $reservation)

<div class="modal fade" id="invoiceModuleModal{{ $reservation->id }}" tabindex="-1"
    aria-labelledby="invoiceModuleModalLabel{{ $reservation->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="invoiceModuleModalLabel{{ $reservation->id }}">Fatura Oluştur</h5>
                <button type="button" class="btn btn-sm text-white" data-bs-dismiss="modal" aria-label="Kapat" style="border: none; background: none;">
                    <i class="fal fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('reservations.invoice') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="reservation_id" id="reservation_id_{{ $reservation->id }}"
                        value="{{ $reservation->id }}">

                    <!-- Müşteri Seçimi -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label fw-bold">Müşteri</label>
                            <select class="custom-select" name="customer_id" required>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $customer->id == $reservation->customer_id ? 'selected' : '' }}>
                                    {{ $customer->first_name }} {{ $customer->last_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Oda Seçimi -->
                        <div class="col-md-6">
                            <label for="table_id" class="form-label fw-bold">Oda</label>
                            <select class="custom-select" name="table_id" required>
                                @foreach($tables as $table)
                                <option value="{{ $table->id }}" {{ $table->id == $reservation->table_id ? 'selected' : '' }}>
                                    {{ $table->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="total_price" class="form-label fw-bold">Toplam Tutar</label>
                        <input type="text" class="form-control" name="total_price" id="total_price2">

                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Rezervasyon Öğeleri</label>
                        <table class="table table-striped table-hover table-responsive">
                            <thead class="table-dark">
                                <tr>
                                    <th>Hizmet</th>
                                    <th>Fiyat</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody id="serviceItemsList{{ $reservation->id }}">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="d-flex">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    function getReservationItems(reservationId) {
        if (!reservationId) {
            console.error('Reservation ID is undefined!');
            return;
        }
        axios.get('/ajax/getReservationItems/' + reservationId)
            .then(function(response) {
                let items = response.data;
                let serviceItemsList = document.getElementById('serviceItemsList' + reservationId);
                serviceItemsList.innerHTML = ''; // Tabloyu temizle
                items.forEach(item => {
                    let row = `
                        <tr>
                            <td>${item.service_name}</td>
                            <td><input type="number" class="form-control servicePrice" name="service_price[]" value="${item.service_price}" required></td>
                            <td><button type="button" class="btn btn-danger delete-row"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    `;
                    serviceItemsList.innerHTML += row;
                });
                updateTotalPrice(reservationId); // Başlangıçta toplam fiyatı güncelle
            })
            .catch(function(error) {
                console.log(error);
            });
    }

    // Modal açıldığında verileri çekme
    document.addEventListener('DOMContentLoaded', function() {
        let invoiceModuleModals = document.querySelectorAll('.modal');
        invoiceModuleModals.forEach(modal => {
            modal.addEventListener('show.bs.modal', function(event) {
                let reservationId = modal.getAttribute('id').replace('invoiceModuleModal', '');
                if (!reservationId) {
                    console.error('Reservation ID is undefined!');
                    return;
                }
                getReservationItems(reservationId);
            });
        });
    });



    //tablodaki fiyatların toplaını alert olarak bas
    $(document).on('change', '.servicePrice', function() {
        let totalPrice = 0;
        let servicePrices = document.querySelectorAll('.servicePrice');
        servicePrices.forEach(servicePrice => {
            totalPrice += parseFloat(servicePrice.value);
        });


        document.getElementById('total_price2').value = totalPrice;


    });


    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('delete-row')) {
            e.target.closest('tr').remove(); // Satırı sil
            let reservationId = e.target.closest('tr').parentElement.id.replace('serviceItemsList', '');
            updateTotalPrice(reservationId); // Toplam fiyatı güncelle
        }
    });
</script>