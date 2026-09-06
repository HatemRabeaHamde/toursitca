<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

class NewsletterSubscribeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
            'source' => ['nullable', 'string', 'in:landing,experiences_cta'],
        ];
    }
}
