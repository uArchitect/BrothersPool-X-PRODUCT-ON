<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'first_name' => 'required|string|max:255|regex:/^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]+$/',
            'phone' => 'required|string|max:20|regex:/^[0-9+\-\s\(\)]+$/',
            'email' => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address_line1' => 'nullable|string|max:500',
            'address_line2' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100|regex:/^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]+$/',
            'state' => 'nullable|string|max:100|regex:/^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]+$/',
            'postal_code' => 'nullable|string|max:10|regex:/^[0-9]+$/',
            'country' => 'nullable|string|max:100|regex:/^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]+$/',
            'is_vip' => 'nullable|boolean',
            'allergy' => 'nullable|boolean',
            'allergy_note' => 'nullable|string|max:1000',
            'parapuan' => 'nullable|numeric|min:0|max:999999',
            'tax_number' => 'nullable|string|max:20|regex:/^[0-9]+$/',
            'identity' => 'nullable|string|max:20|regex:/^[0-9]+$/',
            'notes' => 'nullable|string|max:2000',
        ];

        // For update requests (when editing existing customer)
        if ($this->isMethod('post') && $this->input('id')) {
            // This is an update request
            $customerId = $this->input('id');
            $rules['phone'] .= '|unique:customers,phone,' . $customerId;
            $rules['email'] .= '|unique:customers,email,' . $customerId;
        } else {
            // This is a create request
            $rules['phone'] .= '|unique:customers,phone';
            $rules['email'] .= '|unique:customers,email';
        }

        return $rules;
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Ad alanı zorunludur.',
            'first_name.regex' => 'Ad sadece Türkçe ve İngilizce harfler içerebilir.',
            'first_name.max' => 'Ad en fazla 255 karakter olabilir.',
            
            'last_name.required' => 'Soyad alanı zorunludur.',
            'last_name.regex' => 'Soyad sadece Türkçe ve İngilizce harfler içerebilir.',
            'last_name.max' => 'Soyad en fazla 255 karakter olabilir.',
            
            'phone.required' => 'Telefon numarası zorunludur.',
            'phone.regex' => 'Geçerli bir telefon numarası giriniz (sadece rakam, +, -, boşluk, parantez).',
            'phone.unique' => 'Bu telefon numarası zaten kayıtlı.',
            'phone.max' => 'Telefon numarası en fazla 20 karakter olabilir.',
            
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
            'email.max' => 'E-posta adresi en fazla 255 karakter olabilir.',
            
            'date_of_birth.date' => 'Geçerli bir doğum tarihi giriniz.',
            'date_of_birth.before' => 'Doğum tarihi bugünden önce olmalıdır.',
            
            'gender.in' => 'Cinsiyet Erkek, Kadın veya Diğer olmalıdır.',
            
            'city.regex' => 'Şehir adı sadece harf içerebilir.',
            'city.max' => 'Şehir adı en fazla 100 karakter olabilir.',
            
            'state.regex' => 'İl/Bölge adı sadece harf içerebilir.',
            'state.max' => 'İl/Bölge adı en fazla 100 karakter olabilir.',
            
            'postal_code.regex' => 'Posta kodu sadece rakam içerebilir.',
            'postal_code.max' => 'Posta kodu en fazla 10 karakter olabilir.',
            
            'country.regex' => 'Ülke adı sadece harf içerebilir.',
            'country.max' => 'Ülke adı en fazla 100 karakter olabilir.',
            
            'parapuan.numeric' => 'Parapuan sayısal bir değer olmalıdır.',
            'parapuan.min' => 'Parapuan 0\'dan küçük olamaz.',
            'parapuan.max' => 'Parapuan 999999\'dan büyük olamaz.',
            
            'tax_number.regex' => 'Vergi numarası sadece rakam içerebilir.',
            'tax_number.max' => 'Vergi numarası en fazla 20 karakter olabilir.',
            
            'identity.regex' => 'Kimlik numarası sadece rakam içerebilir.',
            'identity.max' => 'Kimlik numarası en fazla 20 karakter olabilir.',
            
            'address_line1.max' => 'Adres satırı 1 en fazla 500 karakter olabilir.',
            'address_line2.max' => 'Adres satırı 2 en fazla 500 karakter olabilir.',
            'allergy_note.max' => 'Alerji notu en fazla 1000 karakter olabilir.',
            'notes.max' => 'Notlar en fazla 2000 karakter olabilir.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'first_name' => $this->sanitizeText($this->first_name),
            'last_name' => $this->sanitizeText($this->last_name),
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'phone' => $this->sanitizePhone($this->phone),
            'city' => $this->sanitizeText($this->city),
            'state' => $this->sanitizeText($this->state),
            'country' => $this->sanitizeText($this->country),
            'postal_code' => $this->postal_code ? preg_replace('/[^0-9]/', '', $this->postal_code) : null,
            'tax_number' => $this->tax_number ? preg_replace('/[^0-9]/', '', $this->tax_number) : null,
            'identity' => $this->identity ? preg_replace('/[^0-9]/', '', $this->identity) : null,
            'is_vip' => $this->is_vip ? (bool) $this->is_vip : 0,
            'allergy' => $this->allergy ? (bool) $this->allergy : 0,
            'parapuan' => $this->parapuan ? (float) $this->parapuan : 0,
        ]);
    }

    /**
     * Sanitize text input
     */
    private function sanitizeText($text)
    {
        if (!$text) return null;
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Sanitize phone number
     */
    private function sanitizePhone($phone)
    {
        if (!$phone) return null;
        return preg_replace('/[^0-9+\-\s\(\)]/', '', trim($phone));
    }
}
