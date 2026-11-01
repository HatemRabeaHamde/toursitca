<?php

namespace App\Http\Requests\Agency;

use App\Domain\Discover\DTOs\HostProfileData;
use App\Domain\Discover\Services\HostFormOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HostProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'role' => ['required', 'array'],
            'role.en' => ['required', 'string', 'max:120'],
            'role.fr' => ['nullable', 'string', 'max:120'],
            'role.nl' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:80'],
            'destination_id' => ['nullable', 'integer', 'exists:destinations,id'],
            'interest_id' => ['nullable', 'integer', 'exists:interests,id'],
            'bio.*' => ['nullable', 'string', 'max:2000'],
            'quote.*' => ['nullable', 'string', 'max:300'],
            'story_text.*' => ['nullable', 'string', 'max:4000'],
            'skills_text.*' => ['nullable', 'string', 'max:600'],
            'languages' => ['nullable', 'array'],
            'languages.*' => [Rule::in(HostFormOptions::LANGUAGES)],
            'partner_since' => ['nullable', 'integer', 'between:1950,'.now()->year],
            'licence_number' => ['nullable', 'string', 'max:40'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ];
    }

    public function toData(): HostProfileData
    {
        return new HostProfileData(
            name: trim($this->string('name')->toString()),
            role: $this->localized('role'),
            city: $this->filled('city') ? trim($this->string('city')->toString()) : null,
            destinationId: $this->filled('destination_id') ? $this->integer('destination_id') : null,
            interestId: $this->filled('interest_id') ? $this->integer('interest_id') : null,
            bio: $this->localized('bio'),
            quote: $this->localized('quote'),
            story: $this->localizedLines('story_text', paragraphs: true),
            skills: $this->localizedLines('skills_text'),
            languages: array_values((array) $this->input('languages', [])),
            partnerSince: $this->filled('partner_since') ? $this->integer('partner_since') : null,
            licenceNumber: $this->filled('licence_number') ? trim($this->string('licence_number')->toString()) : null,
            photo: $this->file('photo'),
            cover: $this->file('cover'),
        );
    }

    /** @return array<string, string> only the locales that were filled in */
    private function localized(string $key): array
    {
        return collect(array_keys(config('locales.supported')))
            ->mapWithKeys(fn (string $locale): array => [$locale => trim((string) $this->input("$key.$locale"))])
            ->filter()
            ->all();
    }

    /** @return array<string, list<string>> */
    private function localizedLines(string $key, bool $paragraphs = false): array
    {
        return collect(array_keys(config('locales.supported')))
            ->mapWithKeys(fn (string $locale): array => [$locale => collect(preg_split($paragraphs ? '/\R{2,}/' : '/\R/', trim((string) $this->input("$key.$locale"))))
                ->map(fn (string $line): string => trim($line))
                ->filter()
                ->values()
                ->all()])
            ->filter()
            ->all();
    }
}
