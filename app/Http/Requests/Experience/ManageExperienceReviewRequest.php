<?php

namespace App\Http\Requests\Experience;

use Illuminate\Foundation\Http\FormRequest;

class ManageExperienceReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $experience = $this->route('experience');

        return $this->user() && ($this->user()->hasRole('admin') || $this->user()->agency?->id === $experience->agency_id);
    }

    public function rules(): array
    {
        return [];
    }
}
