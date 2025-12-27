@foreach($accounts as $account)
<div class="modal fade" id="addAccountTransactionModal{{ $account->id }}" tabindex="-1" role="dialog" aria-labelledby="addAccountTransactionModalLabel{{ $account->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAccountTransactionModalLabel{{ $account->id }}">
                    Hesap Hareketleri - {{ $account->name }}
                </h5>
                <button type="button" class="btn text-white" data-bs-dismiss="modal" aria-label="Kapat">
                    <i class="fal fa-times" style="color:black"></i>
                </button>            </div>
            <div class="modal-body">
                <!-- Hesap Durumu için Modern Kartlar -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card gradient-primary">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box">
                                        <i class="fal fa-wallet"></i>
                                    </div>
                                    <div class="stat-content ms-3">
                                        <h6 class="stat-title"> Açılış Bakiyesi</h6>
                                        <h3 class="stat-value">{{ $account->balance }} ₺</h3>
                                    </div>
                                </div>
                                <div class="progress mt-3">
                                    <div class="progress-bar" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card gradient-success">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box">
                                        <i class="fal fa-long-arrow-up"></i>
                                    </div>
                                    <div class="stat-content ms-3">
                                        <h6 class="stat-title"> Toplam Gelir</h6>
                                        <h3 class="stat-value"><span id="totalIncome{{ $account->id }}">0</span> ₺</h3>
                                    </div>
                                </div>
                                <div class="progress mt-3">
                                    <div class="progress-bar" style="width: 75%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card gradient-danger">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box">
                                        <i class="fal fa-long-arrow-down"></i>
                                    </div>
                                    <div class="stat-content ms-3">
                                        <h6 class="stat-title"> Toplam Gider</h6>
                                        <h3 class="stat-value"><span id="totalExpense{{ $account->id }}">0</span> ₺</h3>
                                    </div>
                                </div>
                                <div class="progress mt-3">
                                    <div class="progress-bar" style="width: 65%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card gradient-info">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box">
                                        <i class="fal fa-balance-scale"></i>
                                    </div>
                                    <div class="stat-content ms-3">
                                        <h6 class="stat-title"> Toplam Bakiye</h6>
                                        <h3 class="stat-value"><span id="totalBalance{{ $account->id }}">0</span> ₺</h3>
                                    </div>
                                </div>
                                <div class="progress mt-3">
                                    <div class="progress-bar" style="width: 85%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table for Transactions -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th><i class="fal fa-exchange-alt me-2"></i> İşlem Türü</th>
                                <th><i class="fal fa-info-circle me-2"></i> Açıklama</th>
                                <th><i class="fal fa-lira-sign me-2"></i> Tutar</th>
                                <th><i class="fal fa-calendar-alt me-2"></i> İşlem Tarihi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Table rows will be inserted here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        @foreach($accounts as $account)
        $('#addAccountTransactionModal{{ $account->id }}').on('show.bs.modal', function () {
            var account_id = {{ $account->id }};
            
            $('#addAccountTransactionModal{{ $account->id }} table tbody').empty();

            var totalIncome = 0;
            var totalExpense = 0;

            $.ajax({
                url: '/bank/transaction/' + account_id,
                type: 'GET',
                success: function(response) {
                    response.transactions.forEach(function(transaction) {
                        var row = '<tr>';
                        // Gelir kontrolleri (Türkçe ve İngilizce)
                        if (transaction.type === 'Gelir' || transaction.type === 'Income' || transaction.type === 'income') {
                            row += '<td><span class="badge bg-success">Gelir</span></td>';
                            totalIncome += parseFloat(transaction.amount);
                        } 
                        // Gider kontrolleri (Türkçe ve İngilizce)
                        else if (transaction.type === 'Gider' || transaction.type === 'Expense' || transaction.type === 'expense') {
                            row += '<td><span class="badge bg-danger">Gider</span></td>';
                            totalExpense += parseFloat(transaction.amount);
                        }
                        // Diğer türler için varsayılan
                        else {
                            row += '<td><span class="badge bg-secondary">' + transaction.type + '</span></td>';
                        }
                        row += '<td>' + transaction.description + '</td>';
                        row += '<td>' + transaction.amount + ' TL</td>';
                        row += '<td>' + new Date(transaction.created_at).toLocaleString('tr-TR') + '</td>';
                        row += '</tr>';

                        $('#addAccountTransactionModal{{ $account->id }} table tbody').append(row);
                    });

                    $('#totalIncome{{ $account->id }}').text(totalIncome.toFixed(2));
                    $('#totalExpense{{ $account->id }}').text(totalExpense.toFixed(2));
                    $('#netTotal{{ $account->id }}').text((totalIncome - totalExpense).toFixed(2));
                    var totalBalance = parseFloat('{{ $account->balance }}') + totalIncome - totalExpense;
                    $('#totalBalance{{ $account->id }}').text(totalBalance.toFixed(2));

                },
                error: function() {
                    alert('An error occurred while loading the transactions.');
                }
            });
        });
        @endforeach
    });
</script>

<style>
    .modal-lg {
        max-width: 92% !important;
    }

    .modal-header {
        background: linear-gradient(120deg, #f8f9fa 0%, #ffffff 100%);
        border-bottom: 2px solid rgba(0,0,0,0.05);
        padding: 1.5rem 2rem;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        letter-spacing: -0.5px;
    }

    /* Stat Kartları */
    .stat-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 100%);
        z-index: 1;
    }

    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 30px rgba(0,0,0,0.12);
    }

    .stat-card .card-body {
        padding: 2rem;
        position: relative;
        z-index: 2;
    }

    .gradient-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .gradient-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .gradient-info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }

    .icon-box {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(5px);
    }

    .icon-box i {
        font-size: 1.75rem;
        color: rgba(255,255,255,0.95);
    }

    .stat-title {
        color: rgba(255,255,255,0.85);
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        font-weight: 400;
        letter-spacing: 0.5px;
    }

    .stat-value {
        color: #ffffff;
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 0;
        letter-spacing: -0.5px;
    }

    .progress {
        height: 6px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        background: rgba(255,255,255,0.8);
        border-radius: 10px;
    }

    /* Tablo Stilleri */
    .table-responsive {
        background: #ffffff;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        margin-top: 2rem;
    }

    .table {
        margin: 0;
        border-radius: 12px;
        border: none;
    }

    .table thead th {
        background: #f8fafc;
        font-weight: 600;
        padding: 1.25rem 1rem;
        color: #1e293b;
        border: none;
        font-size: 0.95rem;
        letter-spacing: 0.3px;
    }

    .table tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
        font-size: 0.95rem;
    }

    .table tbody tr:hover {
        background-color: #f8fafc;
    }

    .badge {
        padding: 0.6rem 1rem;
        font-size: 0.875rem;
        border-radius: 8px;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    .badge.bg-success {
        background: #dcfce7 !important;
        color: #166534;
    }

    .badge.bg-danger {
        background: #fee2e2 !important;
        color: #991b1b;
    }

    .badge.bg-secondary {
        background: #f1f5f9 !important;
        color: #475569;
    }

    /* Responsive Düzenlemeler */
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }
        
        .stat-value {
            font-size: 1.5rem;
        }
        
        .icon-box {
            width: 48px;
            height: 48px;
        }
        
        .table thead th {
            padding: 1rem 0.75rem;
        }
        
        .table tbody td {
            padding: 1rem 0.75rem;
        }
    }
</style>
