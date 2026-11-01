<?php

namespace App\Domain\Discover\Services;

use App\Domain\Discover\Models\Interest;
use App\Domain\Landing\Models\Destination;

/**
 * Where the landing should send people inside Discover:
 * the hub, its anchors, and the guides that already exist. Cities and
 * categories without a guide keep their original experiences link.
 *
 * @phpstan-type Links array{hub: string, hosts: string, guide: string, cities: array<string, string>, categories: array<string, string>}
 */
final class LandingDiscoverLinks
{
    public function __construct(private readonly DiscoverUrls $urls) {}

    /** @return Links */
    public function build(): array
    {
        return [
            'hub' => $this->urls->hub(),
            'hosts' => route('site.hosts.index', ['locale' => app()->getLocale()]),
            'guide' => $this->urls->hub('know'),
            'cities' => $this->cities(),
            'categories' => $this->categories(),
        ];
    }

    /** @return array<string, string> lower-cased city name → destination guide */
    private function cities(): array
    {
        return Destination::query()
            ->where('is_active', true)
            ->whereNotNull('guide')
            ->get(['id', 'slug', 'name', 'city'])
            ->flatMap(fn (Destination $destination): array => array_fill_keys(
                array_unique([mb_strtolower((string) $destination->name), mb_strtolower((string) $destination->city)]),
                $this->urls->destination($destination->slug),
            ))
            ->all();
    }

    /** @return array<string, string> experience category slug → interest guide */
    private function categories(): array
    {
        return Interest::query()
            ->active()
            ->whereNotNull('guide')
            ->sorted()
            ->get(['id', 'slug', 'category_slugs'])
            ->reverse()
            ->flatMap(fn (Interest $interest): array => array_fill_keys(
                (array) $interest->category_slugs,
                $this->urls->interest($interest->slug),
            ))
            ->all();
    }
}
