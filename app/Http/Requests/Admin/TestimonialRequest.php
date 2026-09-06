<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'author_name' => ['required', 'array', 'min:1'],
            'author_name.*' => ['required', 'string', 'max:120'],
            'author_country' => ['nullable', 'string', 'max:120'],
            'body' => ['required', 'array', 'min:1'],
            'body.*' => ['required', 'string', 'max:2000'],
            'experience_title' => ['nullable', 'string', 'max:160'],
            'rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'avatar_url' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
