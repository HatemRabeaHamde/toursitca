<?php

namespace App\Domain\Landing\ViewModels;

use App\Domain\Experience\DTOs\ExperienceCardData;
use App\Domain\Experience\DTOs\LandingPageData;
use App\ViewModels\SiteContentPage;
use Illuminate\Support\Collection;

/**
 * Pre-shapes LandingPageData for the editorial v2 homepage so the Blade
 * sections only render. No queries happen here — everything comes from
 * ExperienceLandingPageService.
 */
final readonly class HomeV2ViewModel
{
    /**
     * @param  array{hub?: string, hosts?: string, guide?: string, cities?: array<string, string>, categories?: array<string, string>}  $discover
     *                                                                                                                                             links into Discover (see LandingDiscoverLinks); empty keeps the experiences links
     */
    /**
     * @param  list<array{name: string, role: string, image: string, quote: ?string, trips: int, href: string}>  $hosts  verified hosts (see HostCards::verified)
     */
    public function __construct(private LandingPageData $data, private array $discover = [], private array $hosts = []) {}

    /** Discover hub / anchor link, falling back to the experiences listing. */
    public function discoverUrl(string $key = 'hub'): string
    {
        return $this->discover[$key] ?? route('site.experiences.index', ['locale' => app()->getLocale()]);
    }

    /** @return Collection<int, array{name: string, url: string, image_url: string}> */
    public function heroCities(): Collection
    {
        return $this->data->destinations
            ->take($this->limit('hero_cities'))
            ->values()
            ->map(fn (array $city): array => [
                'name' => $city['name'],
                'url' => $this->discover['cities'][mb_strtolower($city['name'])] ?? $city['url'],
                'image_url' => $city['image_url'],
            ]);
    }

    public function interests(): Collection
    {
        return $this->data->categories
            ->take($this->limit('interests'))
            ->values()
            ->map(fn (array $category): array => [
                ...$category,
                'url' => $this->discover['categories'][$category['slug']] ?? $category['url'],
            ]);
    }

    public function featuredLead(): ?ExperienceCardData
    {
        return $this->data->trendingCards->first();
    }

    /** @return Collection<int, ExperienceCardData> */
    public function featuredRest(): Collection
    {
        return $this->data->trendingCards
            ->slice(1, $this->limit('featured') - 1)
            ->values();
    }

    /**
     * Verified hosts from the Host model; the chapter shows nothing until there is one.
     *
     * @return Collection<int, array{name: string, role: string, quote: ?string, image_url: string, href: string, trips: int}>
     */
    public function hosts(): Collection
    {
        return collect($this->hosts)->map(fn (array $host): array => [
            'name' => $host['name'],
            'role' => $host['role'],
            'quote' => $host['quote'],
            'image_url' => $host['image'],
            'href' => $host['href'],
            'trips' => $host['trips'],
        ]);
    }

    /** Titles of the other featured trips, shown as "Pairs well with" chips on the lead card. */
    public function pairings(): Collection
    {
        return $this->featuredRest()->take(2)->map(fn (ExperienceCardData $card): string => $card->title);
    }

    public function stories(?SiteContentPage $content = null): Collection
    {
        $fallbacks = config('landing_v2.images.stories', []);

        return $this->data->testimonials
            ->take($this->limit('stories'))
            ->values()
            ->map(fn (array $story, int $index): array => $story + [
                'cover_url' => ($content ? $content->get('stories.image_'.($index % 3))['url'] : asset($fallbacks[$index % max(count($fallbacks), 1)] ?? '')),
                'cover_alt' => $content ? $content->get('stories.image_'.($index % 3))['alt'] : '',
                'stars' => (int) round((float) $story['rating']),
            ]);
    }

    public function faqs(): Collection
    {
        return collect($this->data->faqs)->take($this->limit('faqs'))->values();
    }

    /** @return list<array{value: string, label_key: string}> */
    public function stats(): array
    {
        $stats = $this->data->stats;

        return [
            ['value' => number_format((int) ($stats['partners_count'] ?? $this->data->topAgencies->count())), 'label_key' => 'partners'],
            ['value' => number_format((int) $stats['happy_travelers']), 'label_key' => 'reviews'],
            ['value' => (string) $stats['avg_rating'], 'label_key' => 'rating'],
            ['value' => number_format((int) $stats['cities_count']), 'label_key' => 'cities'],
        ];
    }

    private function limit(string $key): int
    {
        return (int) config('landing_v2.limits.'.$key);
    }
}
