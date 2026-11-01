<?php

namespace App\Domain\Discover\Services;

use App\Domain\Discover\Models\Host;
use App\Domain\Discover\Models\Interest;
use App\Domain\Landing\Models\Destination;
use Locale;

/** Choices for the host profile form and the "host" select on the experience form. */
final class HostFormOptions
{
    /** Spoken languages a host can list. */
    public const LANGUAGES = ['ar', 'fr', 'en', 'nl', 'es', 'de', 'it', 'zgh'];

    /** @return array{destinations: array<int, string>, interests: array<int, string>, languages: array<string, string>} */
    public function profile(): array
    {
        $locale = app()->getLocale();

        return [
            'destinations' => Destination::query()->active()->get()->mapWithKeys(fn (Destination $d): array => [$d->id => (string) $d->getTranslation('name', $locale, true)])->all(),
            'interests' => Interest::query()->active()->sorted()->get()->mapWithKeys(fn (Interest $i): array => [$i->id => (string) $i->getTranslation('name', $locale, true)])->all(),
            'languages' => collect(self::LANGUAGES)->mapWithKeys(fn (string $code): array => [$code => ucfirst(Locale::getDisplayLanguage($code, $locale))])->all(),
        ];
    }

    /** Hosts an agency can attach to an experience. @return array<int, string> */
    public function forAgency(int $agencyId): array
    {
        return Host::query()->ofAgency($agencyId)->orderBy('name')->pluck('name', 'id')->all();
    }

    /** Every host with its agency (admin experience form). @return array<int, string> */
    public function all(): array
    {
        return Host::query()->with('agency:id,name')->orderBy('name')->get()
            ->mapWithKeys(fn (Host $host): array => [$host->id => $host->name.' — '.($host->agency?->name ?? '—')])
            ->all();
    }
}
