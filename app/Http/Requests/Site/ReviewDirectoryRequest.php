<?php

namespace App\Http\Requests\Site;

use App\Domain\Review\DTOs\ReviewDirectoryFilters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewDirectoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'interest' => ['nullable', 'string', 'max:80', 'alpha_dash'],
            'experience' => ['nullable', 'string', 'max:160', 'alpha_dash'],
            'host' => ['nullable', 'string', 'max:80', 'alpha_dash'],
            'rating' => ['nullable', 'integer', Rule::in(ReviewDirectoryFilters::RATINGS)],
            'photos' => ['nullable', 'boolean'],
            'sort' => ['nullable', Rule::in(ReviewDirectoryFilters::SORTS)],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function toFilters(): ReviewDirectoryFilters
    {
        return new ReviewDirectoryFilters(
            interest: $this->filled('interest') ? $this->string('interest')->toString() : null,
            experience: $this->filled('experience') ? $this->string('experience')->toString() : null,
            host: $this->filled('host') ? $this->string('host')->toString() : null,
            rating: $this->filled('rating') ? $this->integer('rating') : null,
            withPhotos: $this->boolean('photos'),
            sort: $this->string('sort', 'recent')->toString(),
        );
    }
}
