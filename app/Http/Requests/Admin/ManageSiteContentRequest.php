<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteContent;
use Illuminate\Foundation\Http\FormRequest;

final class ManageSiteContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage', SiteContent::class) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
