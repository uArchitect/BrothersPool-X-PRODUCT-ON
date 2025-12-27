<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CampaignRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'campaign_name' => 'required|string|max:255',
            'campaign_details' => 'required|string|max:1000',
            'send_type' => 'required|string|in:sms,app',
            'customer_type' => 'required|string|in:all,specific,vip',
            'customers' => 'required_if:customer_type,specific|array',
            'customers.*' => 'string',
            'date' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'campaign_name.required' => 'Kampanya adı zorunludur.',
            'campaign_name.max' => 'Kampanya adı en fazla 255 karakter olabilir.',
            'campaign_details.required' => 'Kampanya detayları zorunludur.',
            'campaign_details.max' => 'Kampanya detayları en fazla 1000 karakter olabilir.',
            'send_type.required' => 'Gönderim türü seçimi zorunludur.',
            'send_type.in' => 'Gönderim türü SMS veya Uygulama olmalıdır.',
            'customer_type.required' => 'Müşteri türü seçimi zorunludur.',
            'customer_type.in' => 'Müşteri türü geçersiz.',
            'customers.required_if' => 'Belirli müşteriler seçili iken müşteri listesi zorunludur.',
            'customers.array' => 'Müşteri listesi geçersiz format.',
            'date.required' => 'Kampanya tarihi zorunludur.',
            'date.date' => 'Geçerli bir tarih giriniz.',
        ];
    }
} 