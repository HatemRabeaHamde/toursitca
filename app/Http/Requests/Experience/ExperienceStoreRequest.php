<?php

namespace App\Http\Requests\Experience;

use App\Domain\Experience\DTOs\ExperienceFormData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExperienceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $locales = array_keys(config('locales.supported'));

        return [
            'agency_id' => ['nullable', 'integer', 'exists:agencies,id'],
            'title' => ['required', 'array'],
            'description' => ['required', 'array'],
            'title.*' => ['nullable', 'string', 'max:150'],
            'description.*' => ['nullable', 'string', 'max:5000'],
            'category' => ['required', 'string', 'max:80'],
            'difficulty' => ['required', Rule::in(['easy', 'moderate', 'hard'])],
            'duration_hours' => ['required', 'numeric', 'min:0.5', 'max:99.9'],
            'max_group_size' => ['required', 'integer', 'min:1', 'max:'.config('booking.max_participants')],
            'price_per_person' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'deal_starts_at' => ['nullable', 'date'],
            'deal_ends_at' => ['nullable', 'date', 'after_or_equal:deal_starts_at'],
            'private_price' => ['nullable', 'numeric', 'min:0'],
            'pickup_enabled' => ['nullable', 'boolean'],
            'is_top_rated' => ['nullable', 'boolean'],
            'location_city' => ['required', 'string', 'max:80'],
            'location_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'location_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'meeting_point' => ['nullable', 'string', 'max:2000'],
            'inclusions_text' => ['nullable', 'string', 'max:3000'],
            'exclusions_text' => ['nullable', 'string', 'max:3000'],
            'images' => ['nullable', 'array', 'max:'.config('media.max_per_experience')],
            'images.*' => [
                'image',
                'mimes:'.implode(',', config('media.image_mimes')),
                'max:'.config('media.max_image_size'),
            ],
            'video_file' => [
                'nullable',
                'file',
                'mimes:'.implode(',', config('media.video_mimes')),
                'max:'.config('media.max_video_size'),
            ],
            'video_url' => ['nullable', 'url', 'max:2048'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            foreach (array_keys(config('locales.supported')) as $locale) {
                if (filled($this->input("title.$locale")) && filled($this->input("description.$locale"))) {
                    return;
                }
            }

            $validator->errors()->add('title', __('validation.required', ['attribute' => 'title']));
        });
    }

    public function toData(int $agencyId, int $createdBy): ExperienceFormData
    {
        return new ExperienceFormData(
            agencyId: $agencyId,
            createdBy: $createdBy,
            title: $this->localizedArray('title'),
            description: $this->localizedArray('description'),
            category: $this->string('category')->toString(),
            difficulty: $this->string('difficulty')->toString(),
            durationHours: $this->string('duration_hours')->toString(),
            maxGroupSize: $this->integer('max_group_size'),
            pricePerPerson: $this->string('price_per_person')->toString(),
            originalPrice: $this->filled('original_price') ? $this->string('original_price')->toString() : null,
            dealStartsAt: $this->filled('deal_starts_at') ? $this->date('deal_starts_at')->toDateTimeString() : null,
            dealEndsAt: $this->filled('deal_ends_at') ? $this->date('deal_ends_at')->toDateTimeString() : null,
            privatePrice: $this->filled('private_price') ? $this->string('private_price')->toString() : null,
            pickupEnabled: $this->boolean('pickup_enabled'),
            isTopRated: $this->boolean('is_top_rated'),
            locationCity: $this->string('location_city')->toString(),
            locationLat: $this->filled('location_lat') ? $this->string('location_lat')->toString() : null,
            locationLng: $this->filled('location_lng') ? $this->string('location_lng')->toString() : null,
            meetingPoint: $this->filled('meeting_point') ? $this->string('meeting_point')->toString() : null,
            inclusions: $this->localizedLines('inclusions_text'),
            exclusions: $this->localizedLines('exclusions_text'),
        );
    }

    private function localizedArray(string $key): array
    {
        return collect(config('locales.supported'))
            ->keys()
            ->mapWithKeys(fn (string $locale): array => [$locale => $this->input("$key.$locale") ?: $this->firstFilledLocaleValue($key)])
            ->all();
    }

    private function localizedLines(string $key): ?array
    {
        if (! $this->filled($key)) {
            return null;
        }

        $lines = collect(preg_split('/\r\n|\r|\n/', $this->string($key)->toString()))
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->values()
            ->all();

        return collect(config('locales.supported'))
            ->keys()
            ->mapWithKeys(fn (string $locale): array => [$locale => $lines])
            ->all();
    }

    private function firstFilledLocaleValue(string $key): string
    {
        foreach (array_keys(config('locales.supported')) as $locale) {
            if (filled($this->input("$key.$locale"))) {
                return $this->input("$key.$locale");
            }
        }

        return '';
    }
}
