<?php

namespace App\Http\Requests\Site;

use App\Domain\Discover\DTOs\HostDirectoryFilters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HostDirectoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'region' => ['nullable', 'string', 'max:80', 'alpha_dash'],
            'specialty' => ['nullable', 'string', 'max:80', 'alpha_dash'],
            'q' => ['nullable', 'string', 'max:80'],
            'sort' => ['nullable', Rule::in(HostDirectoryFilters::SORTS)],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function toFilters(): HostDirectoryFilters
    {
        return new HostDirectoryFilters(
            region: $this->filled('region') ? $this->string('region')->toString() : null,
            specialty: $this->filled('specialty') ? $this->string('specialty')->toString() : null,
            search: $this->filled('q') ? $this->string('q')->squish()->toString() : null,
            sort: $this->string('sort', 'top_rated')->toString(),
        );
    }
}
