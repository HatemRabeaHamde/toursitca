<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $destination = $this->route('destination');

        return [
            'slug' => ['required', 'string', 'max:80', Rule::unique('destinations', 'slug')->ignore($destination?->id)],
            'name' => ['required', 'array', 'min:1'],
            'name.*' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
