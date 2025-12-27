<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Eğer sadece rıza metni güncelleniyorsa (CSRF + consent_approved_text)
        if ($this->has('consent_approved_text') && !$this->has('salon_name') && !$this->has('phone_number') && !$this->has('email')) {
            return [
                'consent_approved_text' => 'nullable|string',
            ];
        }

        // Normal ayarlar güncellemesi
        return [
            'salon_name' => 'sometimes|required|string|max:255',
            'phone_number' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|max:255',
            'address' => 'sometimes|required|string',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'work_start' => 'sometimes|string',
            'work_end' => 'sometimes|string',
            'room_based_working' => 'sometimes|boolean',
            'parapuan' => 'sometimes|numeric|min:0',
            'parapuan_system_enabled' => 'sometimes|boolean',
            'employee_commission' => 'sometimes|numeric|min:0|max:100',
            'consent_approved_text' => 'nullable|string',
            'tax_office' => 'sometimes|string|max:255',
            'tax_number' => 'sometimes|string|max:20',
            
            // Yeni bildirim ayarları
            'notification_sms_campaign' => 'sometimes|boolean',
            'notification_sms_birthday' => 'sometimes|boolean',
            'notification_sms_appointment' => 'sometimes|boolean',
            
            // SMS mesaj ayarları
            'booking_sms_message' => 'sometimes|string|max:500',
            'reminder_sms_message' => 'sometimes|string|max:500',
            'updater_sms_message' => 'sometimes|string|max:500',
            'delete_sms_message' => 'sometimes|string|max:500',
            'link_sms_message' => 'sometimes|string|max:500',
            
            // SMS hesap bilgileri
            'sms_username' => 'sometimes|string|max:100',
            'sms_password' => 'sometimes|string|max:100',
            'sms_header' => 'sometimes|string|max:50',
            'remaining_sms_limit' => 'sometimes|numeric|min:0',
            
            // Yeni eklenen alanlar
            'currency' => 'sometimes|string|max:10',
            'currency_symbol' => 'sometimes|string|max:10',
            'social_media_links' => 'sometimes|string',
            'area_code' => 'sometimes|string|max:10',
            'number_length' => 'sometimes|numeric|min:0|max:20',
            'interval_time' => 'sometimes|numeric|min:0|max:1440',
        ];
    }

    public function messages()
    {
        return [
            'salon_name.required' => 'Salon adı zorunludur.',
            'salon_name.max' => 'Salon adı en fazla 255 karakter olabilir.',
            'phone_number.required' => 'Telefon numarası zorunludur.',
            'phone_number.max' => 'Telefon numarası en fazla 20 karakter olabilir.',
            'email.required' => 'E-posta adresi zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.max' => 'E-posta adresi en fazla 255 karakter olabilir.',
            'address.required' => 'Adres zorunludur.',
            'logo.image' => 'Logo bir resim dosyası olmalıdır.',
            'logo.mimes' => 'Logo dosyası jpeg, png, jpg veya gif formatında olmalıdır.',
            'logo.max' => 'Logo dosyası en fazla 2MB olabilir.',
            'parapuan.numeric' => 'Parapuan değeri sayısal olmalıdır.',
            'parapuan.min' => 'Parapuan değeri 0\'dan küçük olamaz.',
            'employee_commission.numeric' => 'Komisyon oranı sayısal olmalıdır.',
            'employee_commission.min' => 'Komisyon oranı 0\'dan küçük olamaz.',
            'employee_commission.max' => 'Komisyon oranı 100\'den büyük olamaz.',
            'tax_office.max' => 'Vergi dairesi en fazla 255 karakter olabilir.',
            'tax_number.max' => 'Vergi numarası en fazla 20 karakter olabilir.',
            
            // SMS mesaj validasyonları
            'booking_sms_message.max' => 'Rezervasyon SMS mesajı en fazla 500 karakter olabilir.',
            'reminder_sms_message.max' => 'Hatırlatma SMS mesajı en fazla 500 karakter olabilir.',
            'updater_sms_message.max' => 'Güncelleme SMS mesajı en fazla 500 karakter olabilir.',
            'delete_sms_message.max' => 'İptal SMS mesajı en fazla 500 karakter olabilir.',
        ];
    }
} 