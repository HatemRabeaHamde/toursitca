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
            'title'        => ['required', 'array'],
            'title.en'     => ['required', 'string', 'max:150'],
            'title.fr'     => ['nullable', 'string', 'max:150'],
            'title.nl'     => ['nullable', 'string', 'max:150'],
            'description'  => ['required', 'array'],
            'description.en' => ['required', 'string', 'max:5000'],
            'description.fr' => ['nullable', 'string', 'max:5000'],
            'description.nl' => ['nullable', 'string', 'max:5000'],
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
            'itinerary' => ['nullable', 'array', 'max:20'],
            'itinerary.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:200'],
            'itinerary.*.type' => ['nullable', Rule::in(['start', 'transport', 'stop', 'activity', 'dropoff'])],
            'itinerary.*.title' => ['nullable', 'array'],
            'itinerary.*.title.en' => ['nullable', 'string', 'max:160'],
            'itinerary.*.title.fr' => ['nullable', 'string', 'max:160'],
            'itinerary.*.title.nl' => ['nullable', 'string', 'max:160'],
            'itinerary.*.description' => ['nullable', 'array'],
            'itinerary.*.description.en' => ['nullable', 'string', 'max:500'],
            'itinerary.*.description.fr' => ['nullable', 'string', 'max:500'],
            'itinerary.*.description.nl' => ['nullable', 'string', 'max:500'],
            'itinerary.*.duration_minutes' => ['nullable', 'integer', 'min:1', 'max:10080'],
            'itinerary.*.location_name' => ['nullable', 'array'],
            'itinerary.*.location_name.en' => ['nullable', 'string', 'max:160'],
            'itinerary.*.location_name.fr' => ['nullable', 'string', 'max:160'],
            'itinerary.*.location_name.nl' => ['nullable', 'string', 'max:160'],
            'itinerary.*.location_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'itinerary.*.location_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'itinerary.*.is_main_stop' => ['nullable', 'boolean'],
            'itinerary.*.icon' => ['nullable', Rule::in(['pin', 'walk', 'car', 'bus', 'boat', 'food', 'photo', 'camp', 'finish'])],
        ];
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
            itinerary: $this->itineraryItems(),
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

    private function itineraryItems(): array
    {
        return collect($this->input('itinerary', []))
            ->map(function (array $item, int $index): array {
                return [
                    'sort_order' => $item['sort_order'] ?? ($index + 1),
                    'type' => $item['type'] ?? 'stop',
                    'title' => $this->localizedItemArray($item, 'title'),
                    'description' => $this->localizedItemArray($item, 'description'),
                    'duration_minutes' => $item['duration_minutes'] ?? null,
                    'location_name' => $this->localizedItemArray($item, 'location_name'),
                    'location_lat' => $item['location_lat'] ?? null,
                    'location_lng' => $item['location_lng'] ?? null,
                    'is_main_stop' => (bool) ($item['is_main_stop'] ?? false),
                    'icon' => $item['icon'] ?? null,
                ];
            })
            ->values()
            ->all();
    }

    private function localizedItemArray(array $item, string $key): array
    {
        $values = (array) ($item[$key] ?? []);
        $fallback = collect(config('locales.supported'))
            ->keys()
            ->map(fn (string $locale) => $values[$locale] ?? null)
            ->first(fn ($value) => filled($value), '');

        return collect(config('locales.supported'))
            ->keys()
            ->mapWithKeys(fn (string $locale): array => [$locale => $values[$locale] ?? $fallback])
            ->all();
    }
}
