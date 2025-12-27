@include('layouts.header')
<link rel="stylesheet" href="{{ asset('css/formplugins/summernote/summernote.css') }}" media="screen, print">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ config('app.name') }}</a></li>
        <li class="breadcrumb-item">Ayarlar</li>
        <li class="breadcrumb-item active">Restoran Ayarları</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>Restoran Yönetim Sistemi <span class="fw-300"><i>Ayarlar</i></span></h2>
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
                                <a class="nav-link active" data-toggle="tab" href="#tab_restaurant_info">
                                    <i class="fal fa-store mr-1"></i>Restoran Bilgileri
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_operating_hours">
                                    <i class="fal fa-clock mr-1"></i>Çalışma Saatleri
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_pricing_tax">
                                    <i class="fal fa-money-bill-wave mr-1"></i>Fiyatlandırma & Vergi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_restaurant_features">
                                    <i class="fal fa-utensils mr-1"></i>Restoran Özellikleri
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_payment_billing">
                                    <i class="fal fa-credit-card mr-1"></i>Ödeme & Faturalama
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_notifications">
                                    <i class="fal fa-bell mr-1"></i>Bildirimler
                                </a>
                            </li>
                        </ul>

                        <form id="restaurantSettingsForm" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content p-3">
                                
                                <!-- Restaurant Information Tab -->
                                <div class="tab-pane fade show active" id="tab_restaurant_info">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="panel">
                                                <div class="panel-hdr">
                                                    <h2>Temel Restoran Bilgileri</h2>
                                                </div>
                                                <div class="panel-container">
                                                    <div class="panel-content">
                                                        <div class="form-group">
                                                            <label class="form-label" for="restaurant_name">
                                                                Restoran Adı <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control form-control-lg" id="restaurant_name" name="restaurant_name" value="{{ old('restaurant_name', $settings->restaurant_name ?? $settings->salon_name ?? '') }}" placeholder="Restoran adını giriniz" required>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label" for="restaurant_type">Restoran Tipi</label>
                                                            <select class="form-control" id="restaurant_type" name="restaurant_type">
                                                                <option value="restaurant" {{ ($settings->restaurant_type ?? 'restaurant') == 'restaurant' ? 'selected' : '' }}>Restoran</option>
                                                                <option value="cafe" {{ ($settings->restaurant_type ?? '') == 'cafe' ? 'selected' : '' }}>Kafe</option>
                                                                <option value="bar" {{ ($settings->restaurant_type ?? '') == 'bar' ? 'selected' : '' }}>Bar</option>
                                                                <option value="fast_food" {{ ($settings->restaurant_type ?? '') == 'fast_food' ? 'selected' : '' }}>Fast Food</option>
                                                                <option value="fine_dining" {{ ($settings->restaurant_type ?? '') == 'fine_dining' ? 'selected' : '' }}>Fine Dining</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label" for="phone_number">Telefon Numarası</label>
                                                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', $settings->phone_number ?? '') }}" placeholder="Telefon numaranızı giriniz">
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label" for="email">E-posta Adresi</label>
                                                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $settings->email ?? '') }}" placeholder="E-posta adresinizi giriniz">
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label" for="address">Adres</label>
                                                            <textarea class="form-control" id="address" name="address" rows="3" placeholder="Restoran adresini giriniz">{{ old('address', $settings->address ?? '') }}</textarea>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="city">Şehir</label>
                                                                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $settings->city ?? '') }}" placeholder="Şehir">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="postal_code">Posta Kodu</label>
                                                                    <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ old('postal_code', $settings->postal_code ?? '') }}" placeholder="Posta kodu">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-6">
                                            <div class="panel">
                                                <div class="panel-hdr">
                                                    <h2>İş Bilgileri & Logolar</h2>
                                                </div>
                                                <div class="panel-container">
                                                    <div class="panel-content">
                                                        <div class="form-group">
                                                            <label class="form-label" for="business_license">İş Lisansı</label>
                                                            <input type="text" class="form-control" id="business_license" name="business_license" value="{{ old('business_license', $settings->business_license ?? '') }}" placeholder="İş lisansı numarası">
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label" for="tax_office">Vergi Dairesi</label>
                                                            <input type="text" class="form-control" id="tax_office" name="tax_office" value="{{ old('tax_office', $settings->tax_office ?? '') }}" placeholder="Vergi dairesi">
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label" for="tax_number">Vergi Numarası</label>
                                                            <input type="text" class="form-control" id="tax_number" name="tax_number" value="{{ old('tax_number', $settings->tax_number ?? '') }}" placeholder="Vergi numarası">
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label" for="vat_number">KDV Numarası</label>
                                                            <input type="text" class="form-control" id="vat_number" name="vat_number" value="{{ old('vat_number', $settings->vat_number ?? '') }}" placeholder="KDV numarası">
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label w-100">Restoran Logosu</label>
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input" id="restaurant_logo" name="restaurant_logo" accept="image/*">
                                                                <label class="custom-file-label" for="restaurant_logo" id="restaurantLogoLabel">Dosya seçin...</label>
                                                            </div>
                                                            @if(($settings->restaurant_logo ?? $settings->logo ?? null))
                                                                <div class="mt-2">
                                                                    <img src="{{ asset('images/' . ($settings->restaurant_logo ?? $settings->logo)) }}" alt="Mevcut Logo" class="img-thumbnail" style="max-width: 100px;">
                                                                    <span class="d-block mt-1 text-muted small">Mevcut Logo</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Operating Hours Tab -->
                                <div class="tab-pane fade" id="tab_operating_hours">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="panel">
                                                <div class="panel-hdr">
                                                    <h2>Günlük Çalışma Saatleri</h2>
                                                </div>
                                                <div class="panel-container">
                                                    <div class="panel-content">
                                                        <div class="form-group">
                                                            <label class="form-label d-block">Çalışma Saatleri</label>
                                                            <div class="row">
                                                                <div class="col-md-6 mb-2">
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text">Açılış</span>
                                                                        </div>
                                                                        <input type="time" class="form-control" name="opening_time" value="{{ $settings->opening_time ?? $settings->work_start ?? '09:00' }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text">Kapanış</span>
                                                                        </div>
                                                                        <input type="time" class="form-control" name="closing_time" value="{{ $settings->closing_time ?? $settings->work_end ?? '22:00' }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="is_24_hours" name="is_24_hours" value="1" {{ ($settings->is_24_hours ?? false) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="is_24_hours">24 Saat Açık</label>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label" for="timezone">Saat Dilimi</label>
                                                            <select class="form-control" id="timezone" name="timezone">
                                                                <option value="Europe/Istanbul" {{ ($settings->timezone ?? 'Europe/Istanbul') == 'Europe/Istanbul' ? 'selected' : '' }}>Türkiye (GMT+3)</option>
                                                                <option value="Europe/London" {{ ($settings->timezone ?? '') == 'Europe/London' ? 'selected' : '' }}>Londra (GMT+0)</option>
                                                                <option value="America/New_York" {{ ($settings->timezone ?? '') == 'America/New_York' ? 'selected' : '' }}>New York (GMT-5)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-6">
                                            <div class="panel">
                                                <div class="panel-hdr">
                                                    <h2>Haftalık Program</h2>
                                                </div>
                                                <div class="panel-container">
                                                    <div class="panel-content">
                                                        <div class="alert alert-info">
                                                            <i class="fal fa-info-circle mr-2"></i>
                                                            Haftalık çalışma programı ayarları burada yapılacak.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing & Tax Tab -->
                                <div class="tab-pane fade" id="tab_pricing_tax">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="panel">
                                                <div class="panel-hdr">
                                                    <h2>Para Birimi & Vergi</h2>
                                                </div>
                                                <div class="panel-container">
                                                    <div class="panel-content">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="currency">Para Birimi</label>
                                                                    <select class="form-control" id="currency" name="currency">
                                                                        <option value="TRY" {{ ($settings->currency ?? 'TRY') == 'TRY' ? 'selected' : '' }}>Türk Lirası (₺)</option>
                                                                        <option value="USD" {{ ($settings->currency ?? '') == 'USD' ? 'selected' : '' }}>Amerikan Doları ($)</option>
                                                                        <option value="EUR" {{ ($settings->currency ?? '') == 'EUR' ? 'selected' : '' }}>Euro (€)</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="currency_symbol">Para Birimi Simgesi</label>
                                                                    <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="{{ $settings->currency_symbol ?? '₺' }}" placeholder="₺">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label" for="default_tax_rate">Varsayılan Vergi Oranı (%)</label>
                                                            <input type="number" class="form-control" id="default_tax_rate" name="default_tax_rate" value="{{ $settings->default_tax_rate ?? 18.00 }}" min="0" max="100" step="0.01">
                                                        </div>

                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="tax_inclusive_pricing" name="tax_inclusive_pricing" value="1" {{ ($settings->tax_inclusive_pricing ?? true) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="tax_inclusive_pricing">Fiyatlar Vergi Dahil</label>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="service_charge_enabled" name="service_charge_enabled" value="1" {{ ($settings->service_charge_enabled ?? false) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="service_charge_enabled">Servis Ücreti Aktif</label>
                                                            </div>
                                                        </div>

                                                        <div class="form-group" id="service_charge_rate_group" style="{{ ($settings->service_charge_enabled ?? false) ? '' : 'display: none;' }}">
                                                            <label class="form-label" for="service_charge_rate">Servis Ücreti Oranı (%)</label>
                                                            <input type="number" class="form-control" id="service_charge_rate" name="service_charge_rate" value="{{ $settings->service_charge_rate ?? 10.00 }}" min="0" max="100" step="0.01">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-6">
                                            <div class="panel">
                                                <div class="panel-hdr">
                                                    <h2>Loyalty Program</h2>
                                                </div>
                                                <div class="panel-container">
                                                    <div class="panel-content">
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="loyalty_program_enabled" name="loyalty_program_enabled" value="1" {{ ($settings->loyalty_program_enabled ?? false) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="loyalty_program_enabled">Sadakat Programı Aktif</label>
                                                            </div>
                                                        </div>

                                                        <div id="loyalty_settings" style="{{ ($settings->loyalty_program_enabled ?? false) ? '' : 'display: none;' }}">
                                                            <div class="form-group">
                                                                <label class="form-label" for="loyalty_points_rate">Puan Oranı (1₺ = ? Puan)</label>
                                                                <input type="number" class="form-control" id="loyalty_points_rate" name="loyalty_points_rate" value="{{ $settings->loyalty_points_rate ?? 1.00 }}" min="0" step="0.01">
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label" for="loyalty_points_threshold">Ödül Eşiği (Puan)</label>
                                                                <input type="number" class="form-control" id="loyalty_points_threshold" name="loyalty_points_threshold" value="{{ $settings->loyalty_points_threshold ?? 100 }}" min="1">
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label" for="loyalty_reward_value">Ödül Değeri (₺)</label>
                                                                <input type="number" class="form-control" id="loyalty_reward_value" name="loyalty_reward_value" value="{{ $settings->loyalty_reward_value ?? 10.00 }}" min="0" step="0.01">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Restaurant Features Tab -->
                                <div class="tab-pane fade" id="tab_restaurant_features">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="panel">
                                                <div class="panel-hdr">
                                                    <h2>Hizmet Türleri</h2>
                                                </div>
                                                <div class="panel-container">
                                                    <div class="panel-content">
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="dine_in_enabled" name="dine_in_enabled" value="1" {{ ($settings->dine_in_enabled ?? true) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="dine_in_enabled">Restoranda Yeme</label>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="takeaway_enabled" name="takeaway_enabled" value="1" {{ ($settings->takeaway_enabled ?? true) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="takeaway_enabled">Paket Servis</label>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="delivery_enabled" name="delivery_enabled" value="1" {{ ($settings->delivery_enabled ?? false) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="delivery_enabled">Teslimat</label>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="reservation_enabled" name="reservation_enabled" value="1" {{ ($settings->reservation_enabled ?? true) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="reservation_enabled">Rezervasyon Sistemi</label>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="online_ordering_enabled" name="online_ordering_enabled" value="1" {{ ($settings->online_ordering_enabled ?? false) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="online_ordering_enabled">Online Sipariş</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-6">
                                            <div class="panel">
                                                <div class="panel-hdr">
                                                    <h2>Masa & Kapasite Ayarları</h2>
                                                </div>
                                                <div class="panel-container">
                                                    <div class="panel-content">
                                                        <div class="form-group">
                                                            <label class="form-label" for="max_table_capacity">Maksimum Masa Kapasitesi</label>
                                                            <input type="number" class="form-control" id="max_table_capacity" name="max_table_capacity" value="{{ $settings->max_table_capacity ?? 4 }}" min="1" max="20">
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label" for="reservation_advance_days">Rezervasyon İleri Tarih (Gün)</label>
                                                            <input type="number" class="form-control" id="reservation_advance_days" name="reservation_advance_days" value="{{ $settings->reservation_advance_days ?? 30 }}" min="1" max="365">
                                                        </div>

                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="auto_assign_waiter" name="auto_assign_waiter" value="1" {{ ($settings->auto_assign_waiter ?? false) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="auto_assign_waiter">Otomatik Garson Atama</label>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label" for="max_orders_per_waiter">Garson Başına Maksimum Sipariş</label>
                                                            <input type="number" class="form-control" id="max_orders_per_waiter" name="max_orders_per_waiter" value="{{ $settings->max_orders_per_waiter ?? 10 }}" min="1" max="50">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment & Billing Tab -->
                                <div class="tab-pane fade" id="tab_payment_billing">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="panel">
                                                <div class="panel-hdr">
                                                    <h2>Ödeme Yöntemleri</h2>
                                                </div>
                                                <div class="panel-container">
                                                    <div class="panel-content">
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="split_bill_enabled" name="split_bill_enabled" value="1" {{ ($settings->split_bill_enabled ?? true) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="split_bill_enabled">Hesap Bölme</label>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="tip_enabled" name="tip_enabled" value="1" {{ ($settings->tip_enabled ?? true) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="tip_enabled">Bahşiş Sistemi</label>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="receipt_printer_enabled" name="receipt_printer_enabled" value="1" {{ ($settings->receipt_printer_enabled ?? false) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="receipt_printer_enabled">Fiş Yazıcısı</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-6">
                                            <div class="panel">
                                                <div class="panel-hdr">
                                                    <h2>Personel Komisyonu</h2>
                                                </div>
                                                <div class="panel-container">
                                                    <div class="panel-content">
                                                        <div class="form-group">
                                                            <label class="form-label" for="staff_commission_rate">Personel Komisyon Oranı (%)</label>
                                                            <input type="number" class="form-control" id="staff_commission_rate" name="staff_commission_rate" value="{{ $settings->staff_commission_rate ?? $settings->employee_commission ?? 5.00 }}" min="0" max="100" step="0.01">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notifications Tab -->
                                <div class="tab-pane fade" id="tab_notifications">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="panel">
                                                <div class="panel-hdr">
                                                    <h2>SMS Bildirimleri</h2>
                                                </div>
                                                <div class="panel-container">
                                                    <div class="panel-content">
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="sms_notifications_enabled" name="sms_notifications_enabled" value="1" {{ ($settings->sms_notifications_enabled ?? true) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="sms_notifications_enabled">SMS Bildirimleri Aktif</label>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label" for="remaining_sms_limit">Kalan SMS Kredisi</label>
                                                            <input type="number" class="form-control" id="remaining_sms_limit" name="remaining_sms_limit" value="{{ $settings->remaining_sms_limit ?? 0 }}" readonly>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label" for="booking_sms_template">Rezervasyon SMS Şablonu</label>
                                                            <textarea class="form-control" id="booking_sms_template" name="booking_sms_template" rows="3" placeholder="Rezervasyon onay SMS şablonu">{{ $settings->booking_sms_template ?? $settings->booking_sms_message ?? '' }}</textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label" for="reminder_sms_template">Hatırlatma SMS Şablonu</label>
                                                            <textarea class="form-control" id="reminder_sms_template" name="reminder_sms_template" rows="3" placeholder="Hatırlatma SMS şablonu">{{ $settings->reminder_sms_template ?? $settings->reminder_sms_message ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-6">
                                            <div class="panel">
                                                <div class="panel-hdr">
                                                    <h2>E-posta Bildirimleri</h2>
                                                </div>
                                                <div class="panel-container">
                                                    <div class="panel-content">
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="email_notifications_enabled" name="email_notifications_enabled" value="1" {{ ($settings->email_notifications_enabled ?? true) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="email_notifications_enabled">E-posta Bildirimleri Aktif</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Save Button -->
                        <div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center mt-4">
                            <button class="btn btn-primary ml-auto waves-effect waves-themed" type="button" id="saveRestaurantSettings">
                                <i class="fal fa-save mr-1"></i> Tüm Ayarları Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Service charge toggle
    const serviceChargeEnabled = document.getElementById('service_charge_enabled');
    const serviceChargeRateGroup = document.getElementById('service_charge_rate_group');
    
    if (serviceChargeEnabled && serviceChargeRateGroup) {
        serviceChargeEnabled.addEventListener('change', function() {
            serviceChargeRateGroup.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Loyalty program toggle
    const loyaltyProgramEnabled = document.getElementById('loyalty_program_enabled');
    const loyaltySettings = document.getElementById('loyalty_settings');
    
    if (loyaltyProgramEnabled && loyaltySettings) {
        loyaltyProgramEnabled.addEventListener('change', function() {
            loyaltySettings.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Save settings
    const saveBtn = document.getElementById('saveRestaurantSettings');
    const form = document.getElementById('restaurantSettingsForm');
    
    if (saveBtn && form) {
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const submitButton = this;
            const originalText = submitButton.innerHTML;
            
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fal fa-spinner fa-spin mr-1"></i> Kaydediliyor...';
            
            const formData = new FormData(form);
            
            // Handle checkboxes
            const checkboxes = form.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                formData.set(checkbox.name, checkbox.checked ? '1' : '0');
            });
            
            fetch("{{ route('settings.update') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Restoran ayarları başarıyla kaydedildi.');
                } else {
                    throw new Error(data.message || 'Kaydetme sırasında hata oluştu');
                }
            })
            .catch(error => {
                showError(error.message || 'Ayarlar kaydedilirken bir hata oluştu.');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        });
    }
});
</script>