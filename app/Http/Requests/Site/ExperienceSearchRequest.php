<?php

namespace App\Http\Requests\Site;

use App\Domain\Experience\DTOs\ExperienceSearchData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExperienceSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'array'],
            'category.*' => ['string', 'max:80'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'participants' => ['nullable', 'integer', 'min:1', 'max:'.config('booking.max_participants')],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            // Only enforce gte when min_price is actually present, otherwise a
            // lone max_price (e.g. the "0–50" price pill) fails validation.
            'max_price' => ['nullable', 'numeric', 'min:0', Rule::when($this->filled('min_price'), ['gte:min_price'])],
            'private_available' => ['nullable', 'boolean'],
            'pickup_available' => ['nullable', 'boolean'],
            'duration' => ['nullable', Rule::in(['half', 'full', 'multi'])],
            'sort' => ['nullable', Rule::in(['recommended', 'price_low', 'price_high', 'rating', 'newest'])],
        ];
    }

    public function toData(): ExperienceSearchData
    {
        return new ExperienceSearchData(
            search: $this->filled('search') ? $this->string('search')->trim()->toString() : null,
            city: $this->filled('city') ? $this->string('city')->trim()->toString() : null,
            categories: $this->categories(),
            date: $this->filled('date') ? $this->date('date')->toDateString() : null,
            participants: $this->filled('participants') ? $this->integer('participants') : null,
            minPrice: $this->filled('min_price') ? $this->string('min_price')->toString() : null,
            maxPrice: $this->filled('max_price') ? $this->string('max_price')->toString() : null,
            privateAvailable: $this->boolean('private_available'),
            pickupAvailable: $this->boolean('pickup_available'),
            duration: $this->filled('duration') ? $this->string('duration')->toString() : null,
            sort: $this->string('sort', 'recommended')->toString(),
        );
    }

    /**
     * Selected categories, trimmed and de-duplicated, empty values dropped.
     *
     * @return list<string>
     */
    private function categories(): array
    {
        return collect($this->input('category', []))
            ->filter(fn ($value): bool => is_string($value) && trim($value) !== '')
            ->map(fn (string $value): string => trim($value))
            ->unique()
            ->values()
            ->all();
    }
}
