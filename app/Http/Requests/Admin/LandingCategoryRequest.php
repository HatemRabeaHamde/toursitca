<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LandingCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'slug' => ['required', 'string', 'max:80', Rule::unique('experience_categories', 'slug')->ignore($category?->id)],
            'name' => ['required', 'array', 'min:1'],
            'name.*' => ['required', 'string', 'max:120'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
