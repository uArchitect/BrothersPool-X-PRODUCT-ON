<!-- Sales Details Modal -->
<style>
    #saleDetailsModal {
        --primary-color: #667eea;
        --secondary-color: #764ba2;
        --success-color: #28a745;
        --success-color-2: #20c997;
        --border-color: #e9ecef;
        --hover-bg: #f8f9fa;
        --shadow: 0 5px 15px rgba(0,0,0,0.2);
        --focus-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    #saleDetailsModal .modal-xl { max-width: 1000px; }
    #saleDetailsModal .card { transition: all 0.3s ease; border-radius: 10px; }
    #saleDetailsModal .btn { border-radius: 8px; font-weight: 500; transition: all 0.3s ease; border: none; }
    #saleDetailsModal .btn:hover { transform: translateY(-2px); box-shadow: var(--shadow); }
    #saleDetailsModal .btn-success { background: linear-gradient(135deg, var(--success-color), var(--success-color-2)); }
    #saleDetailsModal .font-weight-semibold { font-weight: 600; }
    #saleDetailsModal .border-primary { border-color: var(--primary-color) !important; }
    
    #saleDetailsModal .table thead th {
        border: none; font-weight: 600; text-transform: uppercase; font-size: 0.85em;
        letter-spacing: 0.5px; padding: 15px 8px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    }
    
    #saleDetailsModal .table tbody td {
        border: 1px solid var(--border-color); padding: 12px 8px; vertical-align: middle;
    }
    
    #saleDetailsModal .table tbody tr:hover { background-color: var(--hover-bg); }
    
    #saleDetailsModal .space-y-2 > div:not(:last-child) { border-bottom: 1px solid var(--border-color); }
    
    #saleDetailsModal .form-control {
        border-radius: 8px; border: 2px solid var(--border-color); transition: all 0.3s ease;
    }
    
    #saleDetailsModal .form-control:focus {
        border-color: var(--primary-color); box-shadow: var(--focus-shadow);
    }
    
    @media (max-width: 768px) {
        #saleDetailsModal .modal-xl { max-width: 95%; margin: 10px auto; }
        #saleDetailsModal .card-body { padding: 1rem !important; }
        #saleDetailsModal .table th, #saleDetailsModal .table td { padding: 8px 4px; font-size: 0.85em; }
        #saleDetailsModal .modal-footer { flex-direction: column; gap: 10px; }
        #saleDetailsModal .modal-footer > div { width: 100%; text-align: center; }
    }
</style>

<div class="modal fade" id="saleDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header -->
            <div class="modal-header bg-gradient-primary text-white border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h4 class="modal-title font-weight-bold mb-0">
                    <i class="fal fa-file-invoice-dollar mr-2"></i> E-Fatura Hazırlığı
                </h4>
                <button type="button" class="close text-white closeModal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="opacity: 0.8;">&times;</span>
                </button>
            </div>
            
            <div class="modal-body p-0">
                <!-- Salon Bilgileri -->
                <div class="bg-light py-4 px-4 border-bottom">
                    <div class="text-center">
                        <h4 class="text-dark font-weight-bold mb-1">{{ $settings->salon_name ?? 'Salon Adı' }}</h4>
                        <div class="text-muted">
                            <p class="mb-1"><i class="fal fa-map-marker-alt mr-1"></i> {{ $settings->address ?? '' }}</p>
                            <div class="d-flex justify-content-center align-items-center flex-wrap">
                                @if($settings->phone_number ?? '')
                                    <span class="mr-3"><i class="fal fa-phone mr-1"></i> {{ $settings->phone_number }}</span>
                                @endif
                                @if($settings->email ?? '')
                                    <span class="mr-3"><i class="fal fa-envelope mr-1"></i> {{ $settings->email }}</span>
                                @endif
                                @if($settings->tax_number ?? '')
                                    <span><i class="fal fa-id-card mr-1"></i> VKN: {{ $settings->tax_number }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-4">
                    <!-- E-Fatura Türü Seçimi -->
                    <form action="{{route('createInvoice')}}" method="POST" id="invoiceForm">
                        @csrf
                        <input type="hidden" name="sale_id" id="hiddenSaleId" value="">
                        <input type="hidden" name="cancel_sale_id" id="hiddenCancelSaleId" value="">
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-primary bg-light">
                                    <div class="card-body p-3">
                                        <h6 class="card-title text-primary font-weight-bold mb-3">
                                            <i class="fal fa-file-signature mr-2"></i>E-Fatura Türü Seçimi
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="invoiceType" class="form-label text-muted">Fatura Türü:</label>
                                                <select class="form-control form-control-lg" name="invoice_type" id="invoiceType" required>
                                                    <option value="">Seçiniz</option>
                                                    <option value="commercial">Ticari Fatura</option>
                                                    <option value="basic">Temel Fatura</option>
                                                    <option value="export">İhracat Faturası</option>
                                                    <option value="exempt">İstisna Faturası</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="invoiceScenario" class="form-label text-muted">Senaryo:</label>
                                                <select class="form-control form-control-lg" name="invoice_scenario" id="invoiceScenario" required>
                                                    <option value="">Seçiniz</option>
                                                    <option value="basic">Temel</option>
                                                    <option value="commercial">Ticari</option>
                                                    <option value="export">İhracat</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                    </form>

                    <!-- Fatura ve Müşteri Bilgileri -->
                    <div class="row mb-4">
                        <!-- Fatura Bilgileri -->
                        <div class="col-lg-6 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body p-3">
                                    <h6 class="card-title text-primary font-weight-bold mb-3">
                                        <i class="fal fa-file-alt mr-2"></i>Fatura Bilgileri
                                    </h6>
                                    <div class="space-y-2">
                                        <div class="d-flex justify-content-between align-items-center py-1">
                                            <span class="text-muted">Fatura No:</span>
                                            <span class="font-weight-semibold" id="modalInvoiceNo">#INV-0001</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center py-1">
                                            <span class="text-muted">Tarih:</span>
                                            <span class="font-weight-semibold" id="modalDate">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center py-1">
                                            <span class="text-muted">Durum:</span>
                                            <span class="font-weight-semibold" id="modalStatus">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center py-1">
                                            <span class="text-muted">E-Fatura:</span>
                                            <span class="font-weight-semibold text-primary" id="modalUUID">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Müşteri Bilgileri -->
                        <div class="col-lg-6 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body p-3">
                                    <h6 class="card-title text-primary font-weight-bold mb-3">
                                        <i class="fal fa-user mr-2"></i>Müşteri Bilgileri
                                    </h6>
                                    <div class="space-y-2">
                                        <div class="d-flex justify-content-between align-items-center py-1">
                                            <span class="text-muted">Müşteri:</span>
                                            <span class="font-weight-semibold" id="modalCustomer">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center py-1">
                                            <span class="text-muted">VKN/TCKN:</span>
                                            <span class="font-weight-semibold" id="modalCustomerTax">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center py-1">
                                            <span class="text-muted">Adres:</span>
                                            <span class="font-weight-semibold" id="modalCustomerAddress">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hizmetler Tablosu -->
                    <div class="mb-4">
                        <h6 class="text-dark font-weight-bold mb-3">
                            <i class="fal fa-list-ul mr-2 text-primary"></i>Hizmet Detayları
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr class="bg-primary text-white">
                                        <th class="text-center border-0" style="width: 60px; border-radius: 6px 0 0 6px;">#</th>
                                        <th class="border-0">Hizmet</th>
                                        <th class="text-center border-0" style="width: 100px;">Adet</th>
                                        <th class="text-right border-0" style="width: 130px;">Birim Fiyat</th>
                                        <th class="text-right border-0" style="width: 130px; border-radius: 0 6px 6px 0;">Toplam</th>
                                    </tr>
                                </thead>
                                <tbody id="modalProductsList" class="bg-white">
                                    <!-- Hizmet satırları buraya gelecek -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Toplam Bilgileri -->
                    <div class="row">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="card border-primary bg-light">
                                <div class="card-body p-3">
                                    <table class="table table-sm mb-0">
                                        <tr class="border-top-0">
                                            <td class="text-right border-0 font-weight-bold text-success" style="font-size: 1.1em;">
                                                <i class="fal fa-coins mr-2"></i>Toplam Tutar:
                                            </td>
                                            <td class="text-right border-0">
                                                <span id="modalTotal" class="font-weight-bold text-success" style="font-size: 1.2em;">₺0.00</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-right border-0 text-muted">
                                                <i class="fal fa-check-circle mr-2"></i>Ödenen:
                                            </td>
                                            <td class="text-right border-0 font-weight-semibold text-primary" id="modalPaid">₺0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right border-0 text-muted">
                                                <i class="fal fa-clock mr-2"></i>Kalan:
                                            </td>
                                            <td class="text-right border-0 font-weight-semibold text-warning" id="modalRemaining">₺0.00</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer bg-light border-0 justify-content-between py-3">
                <div>
                    <button type="button" class="btn btn-secondary btn-lg px-4 closeModal" data-dismiss="modal">
                        <i class="fal fa-times mr-2"></i> Kapat
                    </button>
                </div>
                <div>
                    <button type="submit" form="invoiceForm" class="btn btn-success btn-lg px-4">
                        <i class="fal fa-file-signature mr-2"></i> E-Fatura Kes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation ve submit işlemleri
document.addEventListener('DOMContentLoaded', function() {
    const invoiceForm = document.getElementById('invoiceForm');
    
    if (invoiceForm) {
        invoiceForm.addEventListener('submit', function(e) {
            // Form validation
            const invoiceType = document.getElementById('invoiceType').value;
            const invoiceScenario = document.getElementById('invoiceScenario').value;
            const saleId = document.getElementById('hiddenSaleId').value;
            
            // Gerekli alanları kontrol et
            if (!invoiceType || !invoiceScenario) {
                e.preventDefault();
                alert('Lütfen Fatura Türü ve Senaryo seçiniz!');
                return false;
            }
            
            if (!saleId) {
                e.preventDefault();
                alert('Satış ID bulunamadı! Lütfen sayfayı yenileyin.');
                return false;
            }
            
            return true;
        });
    }
    
    // Modal kapandığında form'u reset et
    $('#saleDetailsModal').on('hidden.bs.modal', function() {
        const invoiceForm = document.getElementById('invoiceForm');
        if (invoiceForm) {
            invoiceForm.reset();
        }
    });
});
</script> 