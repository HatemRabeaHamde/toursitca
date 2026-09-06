<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LandmarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $landmark = $this->route('landmark');

        return [
            'slug' => ['required', 'string', 'max:80', Rule::unique('landmarks', 'slug')->ignore($landmark?->id)],
            'name' => ['required', 'array', 'min:1'],
            'name.*' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'category' => ['nullable', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
