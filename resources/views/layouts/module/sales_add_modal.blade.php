<!-- Bootstrap 4, jQuery ve SmartAdmin CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

@foreach ($reservations as $reservation)
    <div class="modal fade" id="SalesAddModal{{ $reservation->id }}" tabindex="-1" aria-labelledby="SalesAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success-700 text-white">
                    <h5 class="modal-title d-flex align-items-center fw-700">
                        <i class="fal fa-check-circle mr-2"></i> İşlemi Tamamla
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Kapat">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('payment.add') }}" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                    <div class="modal-body bg-light p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label text-dark small text-uppercase">Müşteri</label>
                                    <select class="form-control form-control-lg" name="customer_id" required>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ $customer->id == $reservation->customer_id ? 'selected' : '' }}>
                                                {{ $customer->first_name }} {{ $customer->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label text-dark small text-uppercase">Oda</label>
                                    <select class="form-control form-control-lg" name="table_id" required>
                                        @foreach ($tables as $table)
                                            <option value="{{ $table->id }}" {{ $table->id == $reservation->table_id ? 'selected' : '' }}>
                                                {{ $table->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label text-dark small text-uppercase">Personel</label>
                                    <select class="form-control form-control-lg" name="employee_id" required>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ $employee->id == $reservation->employee_id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label text-dark small text-uppercase">Durum</label>
                                    <select class="form-control form-control-lg" name="status" required>
                                        <option value="pending" {{ $reservation->status == 'pending' ? 'selected' : '' }}>Beklemede</option>
                                        <option value="started" {{ $reservation->status == 'started' ? 'selected' : '' }}>Başlatıldı</option>
                                        <option value="completed" {{ $reservation->status == 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                                        <option value="cancelled" {{ $reservation->status == 'cancelled' ? 'selected' : '' }}>İptal Edildi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white py-2">
                                        <h6 class="card-title fw-700 text-primary mb-0">Rezervasyon Öğeleri</h6>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="bg-light border-0 text-dark">Hizmet</th>
                                                        <th class="bg-light border-0 text-dark">Fiyat</th>
                                                        <th class="bg-light border-0 text-dark text-right">#</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Dinamik olarak JS ile doldurulacak -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white py-3">
                                        <h6 class="card-title fw-bold text-primary mb-0">Ödeme Detayları</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-6 mb-3">
                                                <label class="form-label text-muted small text-uppercase">Toplam Tutar</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fal fa-money-bill"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control form-control-lg" name="total_price" id="total_price" value="{{ $reservation->total_price }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 mb-3">
                                                <label class="form-label text-muted small text-uppercase">İndirim Tipi</label>
                                                <select class="custom-select" name="discount_type" id="discount_type" required>
                                                    <option value="none" {{ ($reservation->discount_percent == 0 && $reservation->discount == 0) ? 'selected' : '' }}>İndirim Yok</option>
                                                    <option value="percentage" {{ ($reservation->discount_percent > 0) ? 'selected' : '' }}>Yüzde İndirimi</option>
                                                    <option value="amount" {{ ($reservation->discount > 0 && $reservation->discount_percent == 0) ? 'selected' : '' }}>Tutar İndirimi</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6 mb-3 discount-value-group" style="display:none;">
                                                <label class="form-label text-muted small text-uppercase">İndirim Değeri</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fal fa-percentage"></i></span>
                                                    </div>
                                                    <input type="number" class="form-control form-control-lg" name="discount_percent" id="discount_percent" value="{{ $reservation->discount_percent ?? '' }}" style="display:none;">
                                                    <input type="number" class="form-control form-control-lg" name="discount_amount" id="discount_amount_input" value="{{ $reservation->discount ?? '' }}" style="display:none;">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 mb-3">
                                                <label class="form-label text-muted small text-uppercase">Nihai Tutar</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fal fa-money-check-alt"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control form-control-lg" name="final_amount" id="final_amount" readonly>
                                                    <input type="hidden" name="discount_amount" id="discount_amount" readonly>
                                                </div>
                                                <small class="text-success" id="discount_difference" style="display:none;"></small>
                                            </div>
                                            <div class="form-group col-md-6 mb-3">
                                                <label class="form-label text-muted small text-uppercase">Ödeme Seçenekleri</label>
                                                <select class="form-control form-control-lg" name="payment_method" required>
                                                    @foreach ($payment_methods as $payment_method)
                                                        <option value="{{ $payment_method->id }}">{{ $payment_method->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6 mb-3">
                                                <label class="form-label text-muted small text-uppercase">Ödeme Tutarı</label>
                                                <input type="text" class="form-control form-control-lg" name="payment_amount" id="payment_amount">
                                            </div>
                                            <div class="form-group col-md-6 mb-3">
                                                <label class="form-label text-muted small text-uppercase">Kalan Tutar</label>
                                                <input type="text" class="form-control form-control-lg" name="remaining_amount" id="remaining_amount">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-primary waves-effect waves-themed extraItemsAdd">
                            <i class="fal fa-plus mr-1"></i> Hizmet Ekle
                        </button>
                        <button type="button" class="btn btn-secondary modal-kapat-btn" data-dismiss="modal">
                            <i class="fal fa-times mr-1"></i> Vazgeç
                        </button>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fal fa-check mr-1"></i> Tamamla
                        </button>
                        <button type="button" class="btn btn-danger all_payment_paid">
                            <i class="fal fa-money-bill"></i> Tüm Ödeme Alındı
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<script>
$(function() {
    // Modal açıldığında indirim ve nihai tutar alanlarını yükle
    $(document).on('shown.bs.modal', '[id^="SalesAddModal"]', function() {
        const modal = $(this);
        updateDiscountFields(modal);
        updateFinalAmount(modal);
    });

    // İndirim tipi değişince inputları göster/gizle ve hesapla
    $(document).on('change', '#discount_type', function() {
        const modal = $(this).closest('.modal');
        updateDiscountFields(modal);
        updateFinalAmount(modal);
    });

    // İndirim değeri veya toplam değişince hesapla
    $(document).on('input change', '#total_price, #discount_percent, #discount_amount_input', function() {
        const modal = $(this).closest('.modal');
        updateFinalAmount(modal);
    });

    // Hizmet fiyatı değişirse toplamı güncelle
    $(document).on('input', '.service-price', function() {
        const modal = $(this).closest('.modal');
        let total = 0;
        modal.find('.service-price').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        modal.find('#total_price').val(total.toFixed(2));
        updateFinalAmount(modal);
    });

    // Ödeme tutarı değişirse kalan tutarı güncelle
    $(document).on('input', '#payment_amount', function() {
        const modal = $(this).closest('.modal');
        updateFinalAmount(modal);
    });

    // İndirim inputlarını göster/gizle
    function updateDiscountFields(modal) {
        const type = modal.find('#discount_type').val();
        const discountGroup = modal.find('.discount-value-group');
        if (type === 'percentage') {
            discountGroup.show();
            modal.find('#discount_percent').show();
            modal.find('#discount_amount_input').hide();
        } else if (type === 'amount') {
            discountGroup.show();
            modal.find('#discount_percent').hide();
            modal.find('#discount_amount_input').show();
        } else {
            discountGroup.hide();
            modal.find('#discount_percent').hide();
            modal.find('#discount_amount_input').hide();
        }
    }

    // Nihai tutarı ve indirim farkını hesapla
    function updateFinalAmount(modal) {
        const totalPrice = parseFloat(modal.find('#total_price').val()) || 0;
        const discountType = modal.find('#discount_type').val();
        let discountValue = 0;
        let discountAmount = 0;
        if (discountType === 'percentage') {
            discountValue = parseFloat(modal.find('#discount_percent').val()) || 0;
            if (discountValue < 0) discountValue = 0;
            if (discountValue > 100) discountValue = 100;
            discountAmount = (totalPrice * discountValue) / 100;
        } else if (discountType === 'amount') {
            discountValue = parseFloat(modal.find('#discount_amount_input').val()) || 0;
            if (discountValue < 0) discountValue = 0;
            if (discountValue > totalPrice) discountValue = totalPrice;
            discountAmount = discountValue;
        }
        let finalPrice = totalPrice - discountAmount;
        if (finalPrice < 0) finalPrice = 0;
        modal.find('#final_amount').val(finalPrice.toFixed(2));
        modal.find('input[name="discount_amount"]').val(discountAmount);
        if (discountAmount > 0) {
            modal.find('#discount_difference').text('İndirim: -' + discountAmount.toFixed(2) + ' ₺').show();
        } else {
            modal.find('#discount_difference').hide();
        }
        // Kalan tutarı da güncelle
        let payment = parseFloat(modal.find('#payment_amount').val()) || 0;
        let remaining = finalPrice - payment;
        if (remaining < 0) remaining = 0;
        modal.find('#remaining_amount').val(remaining.toFixed(2));
    }

    // Only delegate event handlers ONCE
    $(document)
        .off('click.salesAdd')
        .on('click.salesAdd', '.extraItemsAdd', function() {
            var modal = $(this).closest('.modal');
            var tbody = modal.find('table tbody');
            // Benzersiz hizmet kontrolü
            var existingServices = [];
            tbody.find('.service-select').each(function() {
                var val = $(this).val();
                if(val) existingServices.push(val);
            });
            var firstServiceId = '@if(count($services) > 0){{ $services[0]->id }}@else{{ '' }}@endif';
            if(existingServices.includes(firstServiceId)) {
                alert('Aynı hizmet birden fazla eklenemez!');
                return;
            }
            var tr = `<tr class="service-row">
                <td>
                    <select name="service_id[]" class="form-control service-select" required>
                        <option value="">Hizmet Seçin</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" data-price="{{ $service->price }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="service_price[]" class="form-control service-price" step="0.01" min="0" required>
                </td>
                <td class="">
                    <button type="button" class="btn btn-sm btn-danger remove-service"><i class="fal fa-trash"></i></button>
                </td>
            </tr>`;
            tbody.append(tr);
            // Otomatik olarak ilk seçili hizmetin fiyatını yaz ve toplamı güncelle
            var newRow = tbody.find('tr').last();
            var select = newRow.find('.service-select');
            var firstOption = select.find('option').eq(1); // 0: 'Hizmet Seçin', 1: ilk gerçek hizmet
            if (firstOption.length && firstOption.data('price')) {
                select.val(firstOption.val());
                newRow.find('.service-price').val(firstOption.data('price'));
            }
            updateFinalAmount(modal);
        })
        .off('click.salesRemove')
        .on('click.salesRemove', '.remove-service', function() {
            var modal = $(this).closest('.modal');
            $(this).closest('tr').remove();
            updateFinalAmount(modal);
        })
        .off('change.salesSelect')
        .on('change.salesSelect', '.service-select', function() {
            var price = $(this).find('option:selected').data('price') || '';
            $(this).closest('tr').find('.service-price').val(price);
            var modal = $(this).closest('.modal');
            updateFinalAmount(modal);
        })
        .off('input.salesPrice change.salesPrice')
        .on('input.salesPrice change.salesPrice', '.service-price', function() {
            var modal = $(this).closest('.modal');
            // Validasyon: Negatif veya aşırı değer engelle
            var val = parseFloat($(this).val());
            if(val < 0) {
                $(this).val(0);
                $(this).addClass('is-invalid');
            } else if(val > 100000) {
                $(this).val(100000);
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
            updateFinalAmount(modal);
        })
        .off('change.salesDiscountType')
        .on('change.salesDiscountType', 'select[name="discount_type"]', function() {
            var modal = $(this).closest('.modal');
            var discountType = $(this).val();
            var discountValueDiv = modal.find('#discount_value_div');
            // Progressive disclosure: Sadece seçilirse göster
            discountValueDiv.toggle(discountType !== 'none');
            updateFinalAmount(modal);
        })
        .off('input.salesDiscount')
        .on('input.salesDiscount', 'input[name="discount_percent"]', function() {
            var modal = $(this).closest('.modal');
            updateFinalAmount(modal);
        })
        .off('input.salesPayment')
        .on('input.salesPayment', 'input[name="payment_amount"]', function() {
            var modal = $(this).closest('.modal');
            updateFinalAmount(modal);
        })
        .off('click.salesAllPaid')
        .on('click.salesAllPaid', '.all_payment_paid', function() {
            var modal = $(this).closest('.modal');
            var finalAmount = parseFloat(modal.find('input[name="final_amount"]').val()) || 0;
            modal.find('input[name="payment_amount"]').val(finalAmount.toFixed(2));
            updateFinalAmount(modal);
        });

    // On form submit, prevent overpayment
    $(document).on('submit', '[id^="SalesAddModal"] form', function(e) {
        var modal = $(this).closest('.modal');
        var remaining = parseFloat(modal.find('input[name="remaining_amount"]').val()) || 0;
        if (remaining < 0) {
            e.preventDefault();
            alert('Ödeme tutarı, toplam tutardan fazla olamaz!');
        }
    });
});
</script>
</script>

