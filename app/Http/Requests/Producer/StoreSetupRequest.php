<?php

namespace App\Http\Requests\Producer;

use Illuminate\Foundation\Http\FormRequest;

class StoreSetupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'farm_name'     => ['required', 'string', 'max:255'],
            'city_id'       => ['required', 'exists:cities,id'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'phone'         => ['nullable', 'string', 'regex:/^\(\d{2}\) \d{4,5}-\d{4}$/'],
            'whatsapp'      => ['nullable', 'string', 'regex:/^\(\d{2}\) \d{4,5}-\d{4}$/'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'photo'         => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex'    => 'Informe um telefone no formato (00) 0000-0000.',
            'whatsapp.regex' => 'Informe um WhatsApp no formato (00) 0000-0000.',
        ];
    }
}
