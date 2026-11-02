<?php

namespace App\Http\Requests\Experience;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return ['decision' => ['required', Rule::in(['approved', 'rejected', 'changes_requested'])], 'reason' => ['nullable', 'required_unless:decision,approved', 'string', 'max:2000']];
    }
}
