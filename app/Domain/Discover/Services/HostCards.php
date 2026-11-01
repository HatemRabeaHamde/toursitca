<?php

namespace App\Domain\Discover\Services;

use App\Domain\Discover\Models\Host;
use Illuminate\Database\Eloquent\Builder;

/**
 * Host cards (Figma "Host card" column/row).
 *
 * @phpstan-type HostCard array{href: string, name: string, role: string, image: string, bio: ?string, quote: ?string, trips: int, verified: bool}
 */
final class HostCards
{
    /** @return list<HostCard> */
    public function featured(int $limit = 4): array
    {
        return $this->cards(Host::query(), $limit);
    }

    /** Hosts living in, or leading experiences in, a city. @return list<HostCard> */
    public function inCity(string $city, int $limit = 2): array
    {
        $city = mb_strtolower($city);

        return $this->cards(
            Host::query()->where(fn (Builder $query) => $query
                ->whereRaw('LOWER(city) = ?', [$city])
                ->orWhereHas('experiences', fn (Builder $experiences) => $experiences->published()->whereRaw('LOWER(location_city) = ?', [$city]))),
            $limit,
        );
    }

    /** Hosts who lead experiences in the given categories. @param list<string> $categories @return list<HostCard> */
    public function forCategories(array $categories, int $limit = 2): array
    {
        return $this->cards(
            Host::query()->whereHas('experiences', fn (Builder $query) => $query->published()->whereIn('category', $categories)),
            $limit,
        );
    }

    /** Verified hosts for the landing "people" chapter (portrait, quote, how many trips they lead). @return list<HostCard> */
    public function verified(int $limit = 3): array
    {
        return $this->cards(Host::query()->where('is_verified', true)->withCount(['experiences as trips_total' => fn (Builder $query) => $query->published()]), $limit);
    }

    /** The next hosts for "More local hosts" on a profile. @return list<HostCard> */
    public function others(Host $host, int $limit = 3): array
    {
        return $this->cards(Host::query()->whereKeyNot($host->getKey()), $limit);
    }

    /** @return list<HostCard> */
    private function cards(Builder $query, int $limit): array
    {
        $locale = app()->getLocale();

        // Portrait cards need a photo; hosts without one stay off the discovery pages.
        return $query->active()->whereNotNull('photo_url')->sorted()->limit($limit)->get()
            ->map(fn (Host $host): array => [
                'href' => route('site.hosts.show', ['locale' => $locale, 'host' => $host->slug]),
                'name' => $host->name,
                'role' => trim($host->getTranslation('role', $locale, true).($host->city ? ' · '.$host->city : '')),
                'image' => (string) ($host->photo_url ?: $host->avatar_url),
                'bio' => $host->getTranslation('bio', $locale, true) ?: null,
                'quote' => $host->getTranslation('quote', $locale, true) ?: null,
                'trips' => (int) ($host->trips_total ?? 0),
                'verified' => $host->is_verified,
            ])
            ->all();
    }
}
