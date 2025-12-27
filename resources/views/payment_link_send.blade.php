@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">
    <div class="row">
        <div class="col-xl-8">
            <div id="panel-1" class="panel shadow-sm">
                <div class="panel-hdr bg-primary-700 text-white">
                    <h2><i class="fal fa-link mr-2"></i>Yeni Ödeme Linki</h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-panel hover-effect-dot waves-effect waves-themed"
                            data-action="panel-collapse"></button>
                        <button class="btn btn-panel hover-effect-dot waves-effect waves-themed"
                            data-action="panel-fullscreen"></button>
                    </div>
                </div>

                <div class="panel-container show">
                    <div class="panel-content">
                        <form id="payment-link-form" action="" method="POST" class="needs-validation" novalidate>
                            @csrf

                            <!-- Müşteri Seçimi -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fal fa-user-circle mr-2"></i>Müşteri Bilgileri</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label" for="customer_select">
                                                MÜŞTERİ <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-user"></i></span>
                                                </div>
                                                <select class="form-control custom-select select2" id="customer_select" name="customer_id" required>
                                                    <option value="">Müşteri Seçin...</option>
                                                    @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}" data-phone="{{ $customer->phone }}"
                                                        data-email="{{ $customer->email }}">{{ $customer->first_name }}
                                                        {{ $customer->last_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Müşteri seçimi zorunludur!</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Müşteri Bilgi Kartı -->
                                    <div class="row" id="customer_info" style="display: none;">
                                        <div class="col-md-12">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-md-4 border-right">
                                                            <i class="fal fa-phone fa-2x mb-2 text-primary"></i>
                                                            <h6 class="mb-0" id="customer_phone"></h6>
                                                            <small class="text-muted">Telefon</small>
                                                        </div>
                                                        <div class="col-md-4 border-right">
                                                            <i class="fal fa-calendar-alt fa-2x mb-2 text-primary"></i>
                                                            <h6 class="mb-0">15.03.2024</h6>
                                                            <small class="text-muted">Son Ziyaret</small>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <i class="fal fa-envelope fa-2x mb-2 text-primary"></i>
                                                            <h6 class="mb-0" id="customer_email"></h6>
                                                            <small class="text-muted">E-posta</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mevcut Borç Uyarısı -->
                                    <div id="debt_container" style="display: none;">
                                        <div class="alert alert-primary" role="alert">
                                            <div class="d-flex align-items-center">
                                                <i class="fal fa-info-circle mr-2"></i>
                                                <div class="flex-grow-1">
                                                    <strong>Mevcut Borç: <span id="debt_amount">350 ₺</span></strong>
                                                </div>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="use_existing_debt" name="use_existing_debt">
                                                    <label class="custom-control-label" for="use_existing_debt">Bu borç için link oluştur</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ödeme Detayları -->
                            <div class="card mb-4" id="custom_amount_container">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fal fa-money-bill-alt mr-2"></i>Ödeme Detayları</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="custom_amount">
                                                TUTAR <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-lira-sign"></i></span>
                                                </div>
                                                <input type="number" class="form-control" id="custom_amount" name="amount" 
                                                       step="0.01" min="0" placeholder="0.00" required>
                                                <div class="invalid-feedback">Geçerli bir tutar giriniz!</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="due_date">
                                                SON ÖDEME TARİHİ
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="date" class="form-control" id="due_date" name="due_date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label" for="description">
                                                AÇIKLAMA
                                            </label>
                                            <textarea class="form-control" id="description" name="description" rows="3"
                                                placeholder="Ödeme için açıklama girin"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bildirim Tercihleri -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fal fa-bell mr-2"></i>Bildirim Tercihleri</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="send_sms" name="send_sms" checked>
                                                <label class="custom-control-label" for="send_sms">
                                                    <i class="fal fa-mobile mr-1"></i> SMS Gönder
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="send_email" name="send_email">
                                                <label class="custom-control-label" for="send_email">
                                                    <i class="fal fa-envelope mr-1"></i> E-posta Gönder
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="send_whatsapp" name="send_whatsapp" checked>
                                                <label class="custom-control-label" for="send_whatsapp">
                                                    <i class="fab fa-whatsapp mr-1"></i> WhatsApp Gönder
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Butonları -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <button type="submit" id="submitBtn" class="btn btn-lg btn-primary waves-effect waves-themed px-5">
                                                <i class="fal fa-check mr-1"></i> Ödeme Linki Oluştur
                                            </button>
                                            <button type="button" class="btn btn-lg btn-outline-danger waves-effect waves-themed px-5 resetBtn ml-2">
                                                <i class="fal fa-undo mr-1"></i> Sıfırla
                                            </button>
                                        </div>
                                        <div>
                                            <a href="javascript:void(0);" class="btn btn-lg btn-outline-info waves-effect waves-themed px-5">
                                                <i class="fal fa-list mr-1"></i> Ödeme Linkleri
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div id="panel-2" class="panel shadow-sm">
                <div class="panel-hdr bg-success-700 text-white">
                    <h2><i class="fal fa-history mr-2"></i>Son Ödeme Linkleri</h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-panel hover-effect-dot waves-effect waves-themed"
                            data-action="panel-collapse"></button>
                    </div>
                </div>
                <div class="panel-container show">
                    <div class="panel-content p-0">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-success mr-3">
                                        <i class="fal fa-check"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <a href="javascript:void(0);" class="font-weight-bold">Ahmet Yılmaz</a>
                                            <small class="text-muted">3 dk önce</small>
                                        </div>
                                        <div class="text-muted">
                                            <span class="badge badge-success">Ödendi</span>
                                            <span class="ml-2">250 ₺</span>
                                            <br>
                                            <small>Saç Kesimi + Sakal Tıraşı</small>
                                        </div>
                                    </div>
                                    <div class="dropdown ml-2">
                                        <button type="button" class="btn btn-icon rounded-circle" data-toggle="dropdown">
                                            <i class="fal fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <i class="fal fa-eye mr-1"></i> Detaylar
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <i class="fal fa-redo mr-1"></i> Yeni Link
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <i class="fal fa-file-pdf mr-1"></i> PDF İndir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-warning mr-3">
                                        <i class="fal fa-clock"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <a href="javascript:void(0);" class="font-weight-bold">Ayşe Demir</a>
                                            <small class="text-muted">1 saat önce</small>
                                        </div>
                                        <div class="text-muted">
                                            <span class="badge badge-warning">Bekliyor</span>
                                            <span class="ml-2">450 ₺</span>
                                            <br>
                                            <small>Saç Boyama</small>
                                        </div>
                                    </div>
                                    <div class="dropdown ml-2">
                                        <button type="button" class="btn btn-icon rounded-circle" data-toggle="dropdown">
                                            <i class="fal fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <i class="fal fa-eye mr-1"></i> Detaylar
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <i class="fal fa-share-alt mr-1"></i> Tekrar Gönder
                                            </a>
                                            <a class="dropdown-item text-danger" href="javascript:void(0);">
                                                <i class="fal fa-times mr-1"></i> İptal Et
                                            </a>
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
</main>

@include('layouts.footer')

<!-- Ödeme Linki Göster Modalı (1. adım) -->
<div class="modal fade" id="showLinkModal" tabindex="-1" role="dialog" aria-labelledby="showLinkModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="showLinkModalLabel"><i class="fal fa-link mr-2"></i> Ödeme Linki</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="showLinkModalBody">
        <!-- Dinamik içerik buraya gelecek -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
        <button type="button" class="btn btn-primary" id="goSendLinkBtn"><i class="fal fa-paper-plane mr-1"></i> Gönder</button>
      </div>
    </div>
  </div>
</div>
<!-- Ödeme Linki Gönder Modalı (2. adım) -->
<div class="modal fade" id="sendLinkModal" tabindex="-1" role="dialog" aria-labelledby="sendLinkModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sendLinkModalLabel"><i class="fal fa-paper-plane mr-2"></i> Linki Gönder</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="sendLinkModalBody">
        <!-- Dinamik içerik buraya gelecek -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
        <button type="button" class="btn btn-primary" id="sendLinkBtn"><i class="fal fa-check mr-1"></i> Onayla ve Gönder</button>
      </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function() {
        // Laravel flash messages
        @if(session('success'))
            showSuccess('{{ session('success') }}');
        @endif
        
        @if(session('error'))
            showError('{{ session('error') }}');
        @endif

        @if($errors->any())
            showError('{{ implode("<br>", $errors->all()) }}');
        @endif

        // Initialize Select2
        $('#customer_select').select2({
            placeholder: "Müşteri ara...",
            allowClear: true,
            theme: "bootstrap4",
            templateResult: formatCustomer,
            templateSelection: formatCustomer
        });

        // Customer select change handler
        $('#customer_select').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const phone = selectedOption.data('phone');
            const email = selectedOption.data('email');

            if ($(this).val()) {
                $('#customer_phone').text(phone || '-');
                $('#customer_email').text(email || '-');
                $('#customer_info').slideDown(300);
                $('#debt_container').slideDown(300);
            } else {
                $('#customer_info').slideUp(300);
                $('#debt_container').slideUp(300);
            }
        });

        // Set minimum date for due date
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        $('#due_date').attr('min', tomorrow.toISOString().split('T')[0]);

        // Debt checkbox handler with animation
        $('#use_existing_debt').on('change', function() {
            $('#custom_amount_container').slideToggle(!this.checked);
        });

        // Form sıfırla
        $('.resetBtn').click(function() {
            showConfirm('Form içeriği sıfırlanacak. Emin misiniz?').then((result) => {
                if (result.isConfirmed) {
                    $('#payment-link-form')[0].reset();
                    $('.custom-select').val('');
                    $('#customer_info').hide();
                    $('#debt_container').hide();
                    $('#custom_amount_container').show();
                    showSuccess('Form başarıyla sıfırlandı');
                }
            });
        });

        // Form validasyonu ve gönderim
        $('#payment-link-form').on('submit', function(e) {
            const form = this;
            
            // Gerekli alanları kontrol et
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                $(form).addClass('was-validated');
                return false;
            }

            // En az bir bildirim kanalı kontrolü
            const sendSMS = $('#send_sms').is(':checked');
            const sendEmail = $('#send_email').is(':checked');
            const sendWhatsApp = $('#send_whatsapp').is(':checked');

            if (!sendSMS && !sendEmail && !sendWhatsApp) {
                e.preventDefault();
                showError('En az bir bildirim kanalı seçmelisiniz!');
                return false;
            }

            // Submit butonunu değiştir
            $('#submitBtn').prop('disabled', true)
                          .html('<i class="fal fa-spinner fa-spin mr-1"></i> İşleniyor...');
        });
    });

    // Enhanced customer format for Select2
    function formatCustomer(customer) {
        if (!customer.id) return customer.text;

        return $(`
            <div class="d-flex align-items-center">
                <div class="avatar-icon-wrapper mr-2">
                    <span class="avatar-icon rounded-circle">
                        <i class="fal fa-user"></i>
                    </span>
                </div>
                <div>
                    <span class="font-weight-bold">${customer.text}</span>
                    ${customer.element ? `<br><small class="text-muted">${$(customer.element).data('phone')}</small>` : ''}
                </div>
            </div>
        `);
    }
</script>
