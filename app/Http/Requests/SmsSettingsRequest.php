<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SmsSettingsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'booking_sms_message' => 'required|string',
            'reminder_sms_message' => 'required|string',
            'updater_sms_message' => 'required|string',
            'delete_sms_message' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'booking_sms_message.required' => 'Rezervasyon SMS mesajı zorunludur.',
            'reminder_sms_message.required' => 'Hatırlatma SMS mesajı zorunludur.',
            'updater_sms_message.required' => 'Güncelleme SMS mesajı zorunludur.',
            'delete_sms_message.required' => 'Silme SMS mesajı zorunludur.',
        ];
    }
} 