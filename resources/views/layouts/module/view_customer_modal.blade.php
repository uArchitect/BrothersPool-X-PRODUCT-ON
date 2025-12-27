@foreach ($customers as $customer)
    <div class="modal fade" id="viewCustomerModal{{ $customer->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-lg-down modal-xl">
            <div class="modal-content shadow-lg">
                <!-- Modal Header -->
                <div class="modal-header bg-gradient-primary text-white p-4">
                    <div class="d-flex align-items-center gap-4">
                        <div class="customer-avatar rounded-circle p-3 bg-light text-primary">
                            <span
                                class="avatar-text">{{ substr($customer->first_name, 0, 1) }}{{ substr($customer->last_name, 0, 1) }}</span>
                        </div>
                        <div class="d-flex flex-column">
                            <h3 class="modal-title fw-bold m-0">&nbsp; {{ $customer->first_name }} {{ $customer->last_name }}
                            </h3>
                        </div>
                    </div>
                    <button type="button" class="btn text-white" data-bs-dismiss="modal" aria-label="Kapat">
                        <i class="fal fa-times fa-lg"></i>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <!-- Özet Kartları -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="summary-card bg-success">
                                <div class="card-icon">
                                    <i class="fal fa-money-check-alt"></i>
                                </div>
                                <div class="card-info">
                                    <h6 class="card-title">Toplam Ödeme</h6>
                                    <h4 class="card-value" id="totalPaid{{ $customer->id }}">0,00 ₺</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-card bg-danger">
                                <div class="card-icon">
                                    <i class="fal fa-balance-scale"></i>
                                </div>
                                <div class="card-info">
                                    <h6 class="card-title">Kalan Borç</h6>
                                    <h4 class="card-value" id="remainingDebt{{ $customer->id }}">0,00 ₺</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-card bg-info">
                                <div class="card-icon">
                                    <!----Coutn icon---->
                                    <i class="fal fa-list"></i>
                                </div>
                                <div class="card-info">
                                    <h6 class="card-title">İşlem Sayısı</h6>
                                    <h4 class="card-value" id="processCount{{ $customer->id }}">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- İşlem Geçmişi Tablosu -->
                    <div class="transactions-section">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="section-title mb-0">İşlem Geçmişi</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover custom-table">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>Satış No</th>
                                        <th class="text-end">Hizmet Ücreti</th>
                                        <th class="text-end">Ödenen</th>
                                        <th class="text-end">Kalan Borç</th>
                                    </tr>
                                </thead>
                                <tbody id="customerDebtTableBody{{ $customer->id }}">
                                    <!-- AJAX ile doldurulacak -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Müşteri borç bilgilerini çekip, kartları ve tabloyu güncelleyen script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('viewCustomerModal{{ $customer->id }}');
            modal.addEventListener('shown.bs.modal', function() {
                axios.get('/ajax/getCustomerPaymentInformation/' + {{ $customer->id }})
                    .then(function(response) {
                        var data = response.data;
                        var totalPaid = 0,
                            remainingDebt = 0;
                        var tableBody = document.getElementById(
                            'customerDebtTableBody{{ $customer->id }}');
                        tableBody.innerHTML = '';
                        
                        if (Array.isArray(data)) {
                            data.forEach(function(item) {
                                var paid = parseFloat(item.paid) || 0;
                                var remainingPrice = parseFloat(item.remaining_price) || 0;
                                var servicePrice = parseFloat(item.service_price) || 0;
                                
                                totalPaid += paid;
                                remainingDebt += remainingPrice;

                                var dateObj = new Date(item.sale_date);
                                var formattedDate = ("0" + dateObj.getDate()).slice(-2) + '.' +
                                    ("0" + (dateObj.getMonth() + 1)).slice(-2) + '.' +
                                    dateObj.getFullYear();
                                var formattedTime = ("0" + dateObj.getHours()).slice(-2) + ':' +
                                    ("0" + dateObj.getMinutes()).slice(-2);

                                var paidDisplay = paid <= 0 ? 'Ödeme yapılmadı' : paid.toFixed(2).replace('.', ',') + ' ₺';
                                var remainingDisplay = remainingPrice <= 0 ? '0,00 ₺' : remainingPrice.toFixed(2).replace('.', ',') + ' ₺';

                                var row = `
                            <tr>
                                <td>
                                    <div class="transaction-date">
                                        <span class="date fw-bold">${formattedDate}</span>
                                        <span class="time text-muted">${formattedTime}</span>
                                    </div>
                                </td>
                                <td>Satış #${item.sale_id}</td>
                                <td class="text-end fw-bold">${servicePrice.toFixed(2).replace('.', ',')} ₺</td>
                                <td class="text-end fw-bold">${paidDisplay}</td>
                                <td class="text-end fw-bold">${remainingDisplay}</td>
                            </tr>
                        `;
                                tableBody.insertAdjacentHTML('beforeend', row);
                            });

                            // Toplam değerlerin gösterimi için kontrol
                            var totalPaidDisplay = totalPaid <= 0 ? 'Ödeme yapılmadı' : totalPaid.toFixed(2).replace('.', ',') + ' ₺';
                            var totalRemainingDisplay = remainingDebt <= 0 ? '0,00 ₺' : remainingDebt.toFixed(2).replace('.', ',') + ' ₺';

                            document.getElementById('totalPaid{{ $customer->id }}').innerText = totalPaidDisplay;
                            document.getElementById('remainingDebt{{ $customer->id }}').innerText = totalRemainingDisplay;
                            document.getElementById('processCount{{ $customer->id }}').innerText = data.length;
                        }
                    })
                    .catch(function(error) {
                        console.error(error);
                    });
            });
        });
    </script>

    <!-- Geliştirilmiş Tasarım Stil Ayarları -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .modal-content {
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #6a6599, #544f84);
        }

        .modal-header {
            border-bottom: none;
            padding: 1.5rem;
        }

        .customer-avatar {
            width: 60px;
            height: 60px;
        }

        .avatar-text {
            font-size: 24px;
            font-weight: 700;
        }

        .customer-role {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .summary-card {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border-radius: 12px;
            color: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            font-size: 2rem;
            margin-right: 1rem;
        }

        .card-title {
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
            opacity: 0.85;
        }

        .card-value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .transactions-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .custom-table thead th {
            background: #e9ecef;
            color: #495057;
            border: none;
            font-weight: 600;
        }

        .custom-table tbody tr {
            background: #fff;
            transition: background 0.3s ease;
        }

        .custom-table tbody tr:hover {
            background: #f1f3f5;
        }

        .transaction-date .date {
            font-size: 1rem;
        }

        .transaction-date .time {
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
@endforeach
