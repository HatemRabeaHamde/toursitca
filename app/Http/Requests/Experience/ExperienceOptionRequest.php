<?php

namespace App\Http\Requests\Experience;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExperienceOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'array'],
            'title.*' => ['nullable', 'string', 'max:150'],
            'title.en' => ['required', 'string', 'max:150'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'pickup_enabled' => ['nullable', 'boolean'],
            'private_available' => ['nullable', 'boolean'],
            'pay_later_enabled' => ['nullable', 'boolean'],
            'cancellation_hours' => ['nullable', 'integer', 'min:0', 'max:720'],
            'price_type' => ['required', Rule::in(['per_person', 'per_group'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'adult_price' => ['required', 'numeric', 'min:0'],
            'adult_original_price' => ['nullable', 'numeric', 'min:0'],
            'child_price' => ['nullable', 'numeric', 'min:0'],
            'child_original_price' => ['nullable', 'numeric', 'min:0'],
            'languages_text' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            foreach (array_keys(config('locales.supported')) as $locale) {
                if (filled($this->input("title.$locale"))) {
                    return;
                }
            }

            $validator->errors()->add('title', __('validation.required', ['attribute' => __('ui.fields.title')]));
        });
    }

    public function optionAttributes(): array
    {
        return [
            'title' => $this->localizedArray('title'),
            'duration_minutes' => $this->filled('duration_minutes') ? $this->integer('duration_minutes') : null,
            'pickup_enabled' => $this->boolean('pickup_enabled'),
            'private_available' => $this->boolean('private_available'),
            'pay_later_enabled' => $this->boolean('pay_later_enabled'),
            'cancellation_hours' => $this->filled('cancellation_hours') ? $this->integer('cancellation_hours') : null,
            'price_type' => $this->string('price_type')->toString(),
            'status' => $this->string('status')->toString(),
        ];
    }

    public function priceRows(): array
    {
        $rows = [
            [
                'participant_type' => 'adult',
                'min_age' => 13,
                'max_age' => 99,
                'price' => $this->string('adult_price')->toString(),
                'original_price' => $this->filled('adult_original_price') ? $this->string('adult_original_price')->toString() : null,
                'currency' => config('payment.currency', 'MAD'),
            ],
        ];

        if ($this->filled('child_price')) {
            $rows[] = [
                'participant_type' => 'child',
                'min_age' => 5,
                'max_age' => 12,
                'price' => $this->string('child_price')->toString(),
                'original_price' => $this->filled('child_original_price') ? $this->string('child_original_price')->toString() : null,
                'currency' => config('payment.currency', 'MAD'),
            ];
        }

        return $rows;
    }

    public function languageRows(): array
    {
        if (! $this->filled('languages_text')) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n|,/', $this->string('languages_text')->toString()))
            ->map(fn (string $code): string => trim($code))
            ->filter()
            ->unique()
            ->map(fn (string $code): array => [
                'language_code' => $code,
                'type' => 'live_guide',
            ])
            ->values()
            ->all();
    }

    private function localizedArray(string $key): array
    {
        return collect(config('locales.supported'))
            ->keys()
            ->mapWithKeys(fn (string $locale): array => [$locale => $this->input("$key.$locale") ?: $this->firstFilledLocaleValue($key)])
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
