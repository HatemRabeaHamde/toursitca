<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_first_name' => ['required', 'string', 'max:100'],
            'contact_last_name' => ['required', 'string', 'max:100'],
            'contact_email' => ['required', 'email:rfc', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_country' => ['nullable', 'string', 'max:80'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
