@include('layouts.header')

<!-- Choices.js CSS -->
<link href="https://cdn.jsdelivr.net/npm/choices.js@9.0.1/public/assets/styles/choices.min.css" rel="stylesheet" />

<style>
/* Modern Choices.js Styling */
.choices {
    position: relative;
    margin-bottom: 0;
}

.choices__inner {
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 8px 12px;
    min-height: 44px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.choices:not(.is-disabled).is-focused .choices__inner,
.choices:not(.is-disabled).is-open .choices__inner {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.choices__list--multiple .choices__item {
    background-color: #007bff;
    border: 1px solid #007bff;
    color: white;
    border-radius: 20px;
    padding: 4px 12px;
    margin: 2px 4px 2px 0;
    font-size: 13px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.choices__list--multiple .choices__item.is-highlighted {
    background-color: #dc3545;
    border-color: #dc3545;
}

.choices__button {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'%3e%3cpath d='m.293.293.004-.003c.076-.078.156-.135.237-.193.088-.064.18-.104.274-.127C.96-.057 1.117.025 1.266.25.713.25 1.147.437 1.502.793L8 7.293l6.498-6.498.005-.005c.334-.334.768-.584 1.235-.584C15.833.25 16 .417 16 .583c0 .467-.25.901-.584 1.235L9.293 8l6.498 6.498c.334.334.584.768.584 1.235 0 .166-.167.333-.333.333-.467 0-.901-.25-1.235-.584L8 8.707l-6.498 6.498c-.334.334-.768.584-1.235.584-.166 0-.333-.167-.333-.333 0-.467.25-.901.584-1.235L6.707 8 .209 1.502C-.125 1.168-.375.734-.375.267-.375.101-.208-.064-.042-.064c.467 0 .901.25 1.235.584L7.293 8 .795 1.502z'/%3e%3c/svg%3e");
    background-size: 10px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.3);
    opacity: 1;
    transition: background-color 0.2s ease;
}

.choices__button:hover {
    background-color: rgba(255, 255, 255, 0.5);
}

.choices__list--dropdown .choices__item--selectable {
    padding: 12px 16px;
    border-radius: 0;
    transition: background-color 0.2s ease;
}

.choices__list--dropdown .choices__item--selectable.is-highlighted {
    background-color: #f8f9fa;
    color: #007bff;
}

.choices__input {
    background-color: transparent;
    margin: 0;
    border: 0;
    border-radius: 0;
    max-width: 100%;
    padding: 4px 0;
    font-size: 14px;
}

.choices__list--dropdown {
    border: 1px solid #ddd;
    border-top: 0;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-height: 200px;
    overflow-y: auto;
}

.choices.is-open .choices__inner {
    border-radius: 8px 8px 0 0;
}

.choices__placeholder {
    color: #6c757d;
    opacity: 1;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .choices__inner {
        background-color: #2d3748;
        border-color: #4a5568;
        color: #e2e8f0;
    }
    
    .choices__list--dropdown {
        background-color: #2d3748;
        border-color: #4a5568;
    }
    
    .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background-color: #4a5568;
        color: #90cdf4;
    }
}

/* Table Modern Styles */
.table-modern th, .table-modern td {
    vertical-align: middle !important;
}
.table-modern .sms-content {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 320px;
    display: block;
}
.table-modern .badge-info {
    background: linear-gradient(90deg, #36cfc9 0%, #007bff 100%);
    color: #fff;
    font-size: 1em;
    font-weight: 500;
    letter-spacing: 0.02em;
}
.table-modern tbody tr:hover {
    background-color: #f0f8ff !important;
    transition: background 0.2s;
}
</style>

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>Toplu SMS Yönetimi <span class="fw-300"><i>Yönetim</i></span></h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Küçült"></button>
                        <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Tam Ekran"></button>
                    </div>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs nav-tabs-clean" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tab_sms_olustur" role="tab">
                                    <i class="fal fa-plus mr-1"></i> SMS Oluştur
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_sms_gecmisi" role="tab">
                                    <i class="fal fa-history mr-1"></i> SMS Geçmişi
                                </a>
                            </li>
                        </ul>

                        <!-- Tab içerikleri -->
                        <div class="tab-content p-3">
                            <!-- SMS Oluştur Tabı -->
                            <div class="tab-pane fade show active" id="tab_sms_olustur" role="tabpanel">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div id="sms-message"></div>
                                <form id="campaignAddForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div class="panel">
                                                <div class="panel-hdr">
                                                    <h2>SMS Oluştur</h2>
                                                </div>
                                                <div class="panel-container">
                                                    <div class="panel-content">
                                                        <div class="form-group">
                                                            <label for="campaignName" class="form-label">SMS Adı</label>
                                                            <input type="text" class="form-control" id="campaignName" name="campaign_name" placeholder="SMS adını girin" required value="{{ old('campaign_name') }}">
                                                            @error('campaign_name')<span class="help-block">{{ $message }}</span>@enderror
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="sendType" class="form-label">Gönderim Tipi:</label>
                                                            <select name="send_type" id="sendType" class="form-control" required>
                                                                <option value="sms" selected>SMS</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="customerType" class="form-label">Müşteri Türü Seçin:</label>
                                                            <select class="custom-select" id="customerType" name="customer_type">
                                                                <option value="all">Tüm Müşteriler</option>
                                                                <option value="specific">Belirli Müşteriler</option>
                                                                <option value="vip">VIP Müşteriler</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group d-none" id="specificCustomers">
                                                            <label class="form-label mb-2">
                                                                <i class="fal fa-users text-primary mr-2"></i>
                                                                Müşteri Seçin
                                                            </label>
                                                            <select class="form-control" id="customersSelect" name="customers[]" multiple="multiple">
                                                                @foreach($customers as $customer)
                                                                    <option value="{{ $customer->name }}" data-id="{{ $customer->id }}">
                                                                        {{ $customer->name }}@if($customer->phone) - {{ $customer->phone }}@endif
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <div class="mt-2 d-flex align-items-center text-muted">
                                                                <i class="fal fa-lightbulb text-warning mr-1"></i>
                                                                <small>
                                                                    Müşteri aramak için yazmaya başlayın • Birden fazla seçim yapabilirsiniz
                                                                </small>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="campaignDetails" class="form-label">SMS İçeriği:</label>
                                                            <textarea class="form-control" id="campaignDetails" name="campaign_details" rows="3" placeholder="SMS içeriğini girin..." required></textarea>
                                                            <small class="text-muted d-block mt-1">
                                                                <span id="campaignDetailsCharCount">0</span> karakter 
                                                                <span class="text-info ml-2">(<span id="smsCount">0</span> SMS)</span>
                                                            </small>
                                                            
                                                            <!-- SMS Değişken Bilgilendirmesi -->
                                                            <div class="alert alert-info mt-2 d-none" id="smsVariablesInfo">
                                                                <h6><i class="fal fa-info-circle mr-1"></i> SMS Mesajında Kullanabileceğiniz Değişkenler:</h6>
                                                                <small>
                                                                    <strong>[MÜŞTERI ADI]</strong> - Müşteri adı soyadı<br>
                                                                    <strong>[TELEFON NUMARASI]</strong> - Şirket telefon numarası<br>
                                                                    <strong>[TARIH]</strong> - Günün tarihi<br>
                                                                    <strong>[SAAT]</strong> - Günün saati
                                                                </small>
                                                            </div>
                                                            
                                                            @error('campaign_details')<span class="help-block">{{ $message }}</span>@enderror
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="datetime" class="form-label">SMS Tarihi ve Saati:</label>
                                                            <input 
                                                                type="datetime-local" 
                                                                class="form-control" 
                                                                id="datetime" 
                                                                name="datetime" 
                                                                required
                                                                value="{{ old('datetime', \Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}"
                                                            >
                                                            @error('datetime')<span class="help-block">{{ $message }}</span>@enderror
                                                        </div>

                                                        <button type="submit" class="btn btn-success">SMS Oluştur</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- SMS Geçmişi Tabı -->
                            <div class="tab-pane fade" id="tab_sms_gecmisi" role="tabpanel">
                                <div id="sms-history-message"></div>
                                <div class="panel">
                                    <div class="panel-hdr">
                                        <h2>SMS Geçmişi</h2>
                                    </div>
                                    <div class="panel-container">
                                        <div class="panel-content">
                                            <table id="campaigns-table" class="table table-hover table-modern table-striped align-middle w-100">
                                                <thead class="bg-primary text-white">
                                                    <tr>
                                                        <th>SMS Adı</th>
                                                        <th>SMS İçeriği</th>
                                                        <th>Tarih</th>
                                                        <th>İşlemler</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($campaigns as $campaign)
                                                        <tr>
                                                            <td>
                                                                <span class="badge badge-pill badge-info p-2" data-toggle="tooltip" title="SMS Adı">{{ $campaign->campaign_name }}</span>
                                                            </td>
                                                            <td>
                                                                <div class="sms-content text-truncate" style="max-width: 320px;" data-toggle="tooltip" title="{{ $campaign->campaign_details }}">
                                                                    {{ $campaign->campaign_details }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="text-nowrap"><i class="fal fa-calendar-alt mr-1"></i>{{ \Carbon\Carbon::parse($campaign->date)->locale('tr')->diffForHumans() }}</span>
                                                            </td>
                                                            <td>
                                                                <button type="button" data-id="{{ $campaign->id }}" class="btn btn-danger btn-sm delete-campaign-btn" data-toggle="tooltip" title="Sil">
                                                                    <i class="fal fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end SMS Geçmişi Tabı -->
                        </div> <!-- end Tab içerikleri -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Choices.js -->
<script src="https://cdn.jsdelivr.net/npm/choices.js@9.0.1/public/assets/scripts/choices.min.js"></script>

<script>
    // Safe DOM ready function that handles errors
    function safeReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }
    
    // Form sıfırlama fonksiyonu
    function resetSMSForm(form) {
        try {
            // Input alanlarını temizle
            form.reset();
            
            // Select elementlerini sıfırla
            const sendTypeSelect = document.getElementById('sendType');
            const customerTypeSelect = document.getElementById('customerType');
            
            if (sendTypeSelect) {
                sendTypeSelect.value = '';
            }
            
            if (customerTypeSelect) {
                customerTypeSelect.value = 'all';
            }
            
            // Belirli müşteriler bölümünü gizle
            const specificCustomersDiv = document.getElementById('specificCustomers');
            if (specificCustomersDiv) {
                specificCustomersDiv.classList.add('d-none');
            }
            
            // Choices.js müşteri seçimini temizle
            if (window.customersChoices) {
                window.customersChoices.removeActiveItems();
            }
        } catch (error) {
            console.warn('Form sıfırlama hatası:', error);
        }
    }
    
    // SMS tablosuna yeni satır ekleme fonksiyonu
    function addSMSToTable(campaign) {
        try {
            const campaignsTable = document.getElementById('campaigns-table');
            if (!campaignsTable) return;
            
            // DataTable kullanılıyorsa
            if (typeof $ !== 'undefined' && $.fn.DataTable && $.fn.DataTable.isDataTable('#campaigns-table')) {
                try {
                    // DataTable'a veri array'i olarak ekle
                    const table = $('#campaigns-table').DataTable();
                    const newRowData = [
                        campaign.campaign_name,
                        campaign.campaign_details, // Yeni sütuna ekle
                        campaign.date_formatted || campaign.date,
                        `<button type="button" data-id="${campaign.id}" class="btn btn-danger btn-sm delete-campaign-btn">
                            <i class="fal fa-trash"></i>
                        </button>`
                    ];
                    
                    const newRow = table.row.add(newRowData).draw();
                    
                    // Yeni eklenen satırdaki silme butonuna event listener ekle
                    const rowNode = newRow.node();
                    const deleteBtn = rowNode.querySelector('.delete-campaign-btn');
                    if (deleteBtn) {
                        addDeleteEventListener(deleteBtn);
                    }
                    
                    console.log('SMS başarıyla DataTable\'a eklendi');
                    return;
                } catch (e) {
                    console.warn('DataTable ekleme hatası:', e);
                }
            }
            
            // DataTable yoksa normal DOM'a ekle
            const tbody = campaignsTable.querySelector('tbody');
            if (tbody) {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>
                        <span class="badge badge-pill badge-info p-2" data-toggle="tooltip" title="SMS Adı">${campaign.campaign_name}</span>
                    </td>
                    <td>
                        <div class="sms-content text-truncate" style="max-width: 320px;" data-toggle="tooltip" title="${campaign.campaign_details}">
                            ${campaign.campaign_details}
                        </div>
                    </td>
                    <td>
                        <span class="text-nowrap"><i class="fal fa-calendar-alt mr-1"></i>${campaign.date_formatted || campaign.date}</span>
                    </td>
                    <td>
                        <button type="button" data-id="${campaign.id}" class="btn btn-danger btn-sm delete-campaign-btn">
                            <i class="fal fa-trash"></i>
                        </button>
                    </td>
                `;
                
                tbody.appendChild(newRow);
                
                // Yeni satırdaki silme butonuna event listener ekle
                const deleteBtn = newRow.querySelector('.delete-campaign-btn');
                if (deleteBtn) {
                    addDeleteEventListener(deleteBtn);
                }
                
                console.log('SMS normal DOM\'a eklendi');
            }
        } catch (error) {
            console.error('Tablo güncelleme hatası:', error);
        }
    }
    
    // Silme butonu event listener'ı
    function addDeleteEventListener(deleteBtn) {
    deleteBtn.addEventListener('click', function () {
        const campaignId = this.dataset.id;
        const buttonElement = this;
        const row = buttonElement.closest('tr');

        // Silme işlemini direkt yap
        buttonElement.disabled = true;
        buttonElement.innerHTML = '<i class="fal fa-spinner fa-spin"></i>';

        if (typeof axios !== 'undefined') {
            axios.post("{{ route('campaigns.delete') }}", {
                id: campaignId,
                _token: document.querySelector('meta[name="csrf-token"]').content
            })
            .then(res => {
                showSuccess('SMS başarıyla silindi!');

                if (typeof $ !== 'undefined' && $.fn.DataTable && $('#campaigns-table').DataTable) {
                    try {
                        $('#campaigns-table').DataTable().row(row).remove().draw();
                    } catch (e) {
                        row.remove();
                    }
                } else {
                    row.remove();
                }
            })
            .catch(err => {
                let msg = 'SMS silinirken bir hata oluştu';
                if (err.response?.data?.message) {
                    msg = err.response.data.message;
                } else if (err.response?.data?.errors) {
                    msg = Object.values(err.response.data.errors).flat().join('<br>');
                }

                showError(msg);

                // Buton eski haline getir
                if (buttonElement) {
                    buttonElement.disabled = false;
                    buttonElement.innerHTML = '<i class="fal fa-trash"></i>';
                }
            });
        } else {
            showError('Axios kütüphanesi yüklenemedi. Lütfen sayfayı yenileyin.');
            if (buttonElement) {
                buttonElement.disabled = false;
                buttonElement.innerHTML = '<i class="fal fa-trash"></i>';
            }
        }
    });
}


    safeReady(function() {
        try {
            // Test notification to verify SweetAlert2 is working
            console.log('SweetAlert2 test:', typeof Swal);
            
            // Müşteri türü değişikliği event handler
            const customerTypeSelect = document.getElementById('customerType');
            const specificCustomersDiv = document.getElementById('specificCustomers');
            
            if (customerTypeSelect && specificCustomersDiv) {
                customerTypeSelect.addEventListener('change', function() {
                    if (this.value === 'specific') {
                        specificCustomersDiv.classList.remove('d-none');
                        // Choices.js'yi initialize et (eğer henüz edilmediyse)
                        initializeCustomerSelect();
                    } else {
                        specificCustomersDiv.classList.add('d-none');
                        // Choices.js seçimini temizle
                        if (window.customersChoices) {
                            window.customersChoices.removeActiveItems();
                        }
                    }
                });
            }

            // Gönderim türü değişimi kontrolü - SMS seçilince değişken bilgilerini göster
            const sendTypeSelect = document.getElementById('sendType');
            const smsVariablesInfo = document.getElementById('smsVariablesInfo');
            
            if (sendTypeSelect && smsVariablesInfo) {
                sendTypeSelect.addEventListener('change', function() {
                    if (this.value === 'sms') {
                        smsVariablesInfo.classList.remove('d-none');
                    } else {
                        smsVariablesInfo.classList.add('d-none');
                    }
                });
            }

            // Choices.js initialization function
            function initializeCustomerSelect() {
                const customerSelectElement = document.getElementById('customersSelect');
                if (customerSelectElement && !window.customersChoices) {
                    window.customersChoices = new Choices(customerSelectElement, {
                        removeItemButton: true,
                        placeholder: true,
                        placeholderValue: '🔍 Müşteri arayın veya seçin...',
                        searchPlaceholderValue: 'Müşteri adı yazın...',
                        noResultsText: '🚫 Sonuç bulunamadı',
                        noChoicesText: '📋 Tüm müşteriler seçildi',
                        itemSelectText: '👆 Seçmek için tıklayın',
                        maxItemText: (maxItemCount) => {
                            return `⚠️ En fazla ${maxItemCount} müşteri seçebilirsiniz`;
                        },
                        searchEnabled: true,
                        searchResultLimit: 10,
                        shouldSort: false,
                        position: 'bottom',
                        classNames: {
                            containerOuter: 'choices',
                            containerInner: 'choices__inner form-control',
                            input: 'choices__input',
                            inputCloned: 'choices__input--cloned',
                            list: 'choices__list',
                            listItems: 'choices__list--multiple',
                            listSingle: 'choices__list--single',
                            listDropdown: 'choices__list--dropdown',
                            item: 'choices__item',
                            itemSelectable: 'choices__item--selectable',
                            itemDisabled: 'choices__item--disabled',
                            itemChoice: 'choices__item--choice',
                            placeholder: 'choices__placeholder',
                            group: 'choices__group',
                            groupHeading: 'choices__heading',
                            button: 'choices__button',
                            activeState: 'is-active',
                            focusState: 'is-focused',
                            openState: 'is-open',
                            disabledState: 'is-disabled',
                            highlightedState: 'is-highlighted',
                            selectedState: 'is-selected',
                            flippedState: 'is-flipped',
                            loadingState: 'is-loading'
                        }
                    });
                }
            }

            // DataTable ayarları
            if (typeof $ !== 'undefined' && $.fn && $.fn.DataTable) {
                $('#campaigns-table').DataTable({
                    responsive: true,
                    autoWidth: false,
                    language: {
                        paginate: {
                            first: "İlk",
                            last: "Son", 
                            next: "Sonraki",
                            previous: "Önceki"
                        },
                        info: "Gösterilen: _START_ - _END_ / _TOTAL_",
                        infoEmpty: "Gösterilecek kayıt yok",
                        zeroRecords: "Eşleşen kayıt bulunamadı"
                    },
                    columnDefs: [
                        { targets: 1, className: 'sms-content-col' },
                        { targets: 2, className: 'text-nowrap' },
                        { targets: 3, orderable: false, searchable: false }
                    ]
                });
                // Tooltipleri aktif et
                $('[data-toggle="tooltip"]').tooltip();
            }

            // Karakter sayacı - SMS Detayları
            const campaignDetailsInput = document.getElementById('campaignDetails');
            const campaignDetailsCharCount = document.getElementById('campaignDetailsCharCount');
            const smsCountSpan = document.getElementById('smsCount');
            
            if (campaignDetailsInput && campaignDetailsCharCount && smsCountSpan) {
                function updateCampaignDetailsCharCount() {
                    const charCount = campaignDetailsInput.value.length;
                    campaignDetailsCharCount.textContent = charCount;
                    
                    // SMS sayısını hesapla (1-155 karakter = 1 SMS, 156-306 = 2 SMS, 307-459 = 3 SMS)
                    let smsCount = 1;
                    if (charCount > 153) {
                        smsCount = Math.ceil((charCount - 153) / 153) + 1;
                    }
                    smsCountSpan.textContent = smsCount;
                    
                    // Renk değişimi
                    if (smsCount > 1) {
                        smsCountSpan.className = 'text-warning font-weight-bold';
                    } else {
                        smsCountSpan.className = 'text-info';
                    }
                }
                campaignDetailsInput.addEventListener('input', updateCampaignDetailsCharCount);
                updateCampaignDetailsCharCount();
            }
        } catch (error) {
            console.warn('Plugin initialization error:', error);
        }

        // Form submit handling
        const form = document.getElementById('campaignAddForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitButton = form.querySelector('button[type="submit"]');
                
                // Submit button güvenlik kontrolü
                if (!submitButton) {
                    console.error('Submit button not found');
                    return;
                }
                
                const originalText = submitButton.innerHTML || 'SMS Oluştur';
                
                // Müşteri verilerini topla
                const customerType = form.customer_type.value;
                let selectedCustomers = [];
                
                if (customerType === 'specific') {
                    // Choices.js'den seçili değerleri al
                    if (window.customersChoices) {
                        selectedCustomers = window.customersChoices.getValue(true) || [];
                    }
                }
                
                const data = {
                    campaign_name: form.campaignName.value,
                    campaign_details: form.campaignDetails.value,
                    send_type: form.sendType.value,
                    customer_type: customerType,
                    customers: selectedCustomers,
                    date: form.datetime.value,
                    _token: document.querySelector('meta[name="csrf-token"]').content
                };
                
                // Submit button'u devre dışı bırak
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fal fa-spinner fa-spin mr-1"></i> SMS Oluşturuluyor...';
                
                if (typeof axios !== 'undefined') {
                    axios.post("{{ route('campaigns.add') }}", data)
                        .then(res => {
                            let successMessage = 'SMS başarıyla oluşturuldu!';
                            if (res.data.sms_result && res.data.sms_result.message) {
                                successMessage = res.data.sms_result.message;
                            }
                            showSuccess(successMessage);
                            
                            // Form sıfırla ve DOM güncelle
                            resetSMSForm(form);
                            
                            // Eğer yeni SMS verisi gelirse tabloyu güncelle
                            if (res.data.campaign) {
                                console.log('Backend\'den gelen SMS verisi:', res.data.campaign);
                                addSMSToTable(res.data.campaign);
                                
                                // SMS eklendikten sonra SMS geçmişi tabına geç
                                setTimeout(() => {
                                    const historyTab = document.querySelector('a[href="#tab_sms_gecmisi"]');
                                    if (historyTab) {
                                        historyTab.click();
                                        showInfo('Yeni SMS\'iniz "SMS Geçmişi" tabında görebilirsiniz.');
                                    }
                                }, 1000);
                            } else {
                                console.warn('Backend\'den SMS verisi gelmedi:', res.data);
                            }
                        })
                        .catch(err => {
                            let msg = 'SMS oluşturulurken bir hata oluştu';
                            if (err.response && err.response.data && err.response.data.errors) {
                                msg = Object.values(err.response.data.errors).flat().join('<br>');
                            } else if (err.response && err.response.data && err.response.data.message) {
                                msg = err.response.data.message;
                            }
                            showError(msg);
                        })
                        .finally(() => {
                            // Submit button'u tekrar aktif et with null check
                            if (submitButton) {
                                submitButton.disabled = false;
                                submitButton.innerHTML = originalText;
                            }
                        });
                } else {
                    showError('Axios kütüphanesi yüklenemedi. Lütfen sayfayı yenileyin.');
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    }
                }
            });
        }

        // Delete button handling - mevcut butonlar için
        document.querySelectorAll('.delete-campaign-btn').forEach(btn => {
            addDeleteEventListener(btn);
        });
    });
</script>

@include('layouts.footer')