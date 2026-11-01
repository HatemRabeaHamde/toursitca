<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Agency\HostProfileRequest;

/** The admin edits the same profile and also decides ownership, visibility and verification. */
class HostStoreRequest extends HostProfileRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'agency_id' => ['required', 'integer', 'exists:agencies,id'],
            'is_active' => ['nullable', 'boolean'],
            'is_verified' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
