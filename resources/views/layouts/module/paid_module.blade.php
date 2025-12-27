@foreach($reservations as $reservation)
<div class="modal fade" id="paidModuleModal{{$reservation->id}}" tabindex="-1" aria-labelledby="paidModuleModalLabel{{$reservation->id}}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="paidModuleModalLabel{{$reservation->id}}">Ödeme Alma</h5>
                <button type="button" class="btn btn-sm text-white" data-bs-dismiss="modal" aria-label="Kapat" style="border: none; background: none;">
                    <i class="fal fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('payment.add')}}" method="POST">
                    <input type="hidden" name="customer_id" value="{{$reservation->customer_id}}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Müşteri Adı</label>
                        <input type="text" class="form-control" value="{{$reservation->customer_name}}" readonly>
                    </div>

                    <div id="paymentFields{{$reservation->id}}" class="mb-3">
                        <div class="row mb-3 payment-field">
                            <div class="col-md-6">
                                <label class="form-label">Ödeme</label>
                                <input type="text" class="form-control payment-amount" name="payment_amount[]" oninput="updatePayments({{$reservation->id}})" value="{{$reservation->total_price - $reservation->discount}}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ödeme Türü</label>
                                <select class="custom-select payment-type" name="payment_type[]">
                                    <option value="cash">Nakit</option>
                                    <option value="credit_card">Kredi Kartı</option>
                                    <option value="transfer">Havale</option>
                                    <option value="eft">EFT</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary w-100" onclick="addPaymentField({{$reservation->id}})">Ödeme Ekle</button>
                        </div>

                        <div class="col-md-6">
                            <button type="button" class="btn btn-success w-100" onclick="completePayment({{$reservation->id}})">Tam Ödeme Alındı</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ödeme Notu</label>
                        <textarea class="form-control" name="payment_note" rows="3"></textarea>
                    </div>

                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>Toplam Hizmet</td>
                                <td><span id="totalProduct{{$reservation->id}}">1</span></td>
                                <td>Toplam Ödenecek Para</td>
                                <td><span id="totalPrice{{$reservation->id}}">{{$reservation->total_price - $reservation->discount}}</span></td>
                            </tr>
                            <tr>
                                <td>Toplam Ödenen Para</td>
                                <td><input type="text" id="totalPaid{{$reservation->id}}" class="form-control" name="total_paid" value="0" readonly></td>
                                <td>Kalan</td>
                                <td><input type="text" id="change{{$reservation->id}}" class="form-control" name="change" value="0" readonly></td>
                            </tr>
                        </tbody>
                    </table>

                     <tfoot>
                        <input type="hidden" name="reservation_id" value="{{$reservation->id}}">
                        <button type="submit" class="btn btn-primary w-100">Ödeme Al</button>
                    </tfoot>

            </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
    function addPaymentField(reservationId) {
        const paymentFields = document.getElementById('paymentFields' + reservationId);
        const newPaymentField = document.createElement('div');
        newPaymentField.classList.add('row', 'mb-3', 'payment-field');

        newPaymentField.innerHTML = `
            <div class="col-md-6">
                <label class="form-label">Ödeme</label>
                <input type="number" class="form-control payment-amount" name="payment_amount[]" oninput="updatePayments(${reservationId})">
            </div>
            <div class="col-md-6">
                <label class="form-label">Ödeme Türü</label>
                <select class="custom-select payment-type" name="payment_type[]">
                    <option value="cash">Nakit</option>
                    <option value="credit_card">Kredi Kartı</option>
                    <option value="transfer">Havale</option>
                    <option value="eft">EFT</option>
                </select>
            </div>
        `;
        paymentFields.appendChild(newPaymentField);
    }

    function updatePayments(reservationId) {
        const totalPrice = parseFloat(document.getElementById('totalPrice' + reservationId).innerText);
        const paymentAmounts = document.querySelectorAll(`#paidModuleModal${reservationId} .payment-amount`);
        let totalPaid = 0;

        paymentAmounts.forEach(input => {
            const value = parseFloat(input.value) || 0;
            if (value < 0) {
                input.value = 0;
            }
            totalPaid += parseFloat(input.value) || 0;
        });

        document.getElementById('totalPaid' + reservationId).value = totalPaid.toFixed(2);
        const change = (totalPaid - totalPrice).toFixed(2);
        document.getElementById('change' + reservationId).value = change;

        const changeInput = document.getElementById('change' + reservationId);
        if (parseFloat(change) < 0) {
            changeInput.style.backgroundColor = '#ffdddd';
        } else if (parseFloat(change) > 0) {
            changeInput.style.backgroundColor = '#ddffdd';
        } else {
            changeInput.style.backgroundColor = '#ffffff';
        }
    }

    function completePayment(reservationId) {
        const totalPrice = parseFloat(document.getElementById('totalPrice' + reservationId).innerText);
        const firstPaymentInput = document.querySelector(`#paidModuleModal${reservationId} .payment-amount`);
        if (firstPaymentInput) {
            firstPaymentInput.value = totalPrice.toFixed(2);
            updatePayments(reservationId);
        }
    }
</script>