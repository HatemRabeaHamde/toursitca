<?php

namespace App\Domain\Experience\Services;

use App\Domain\Discover\Models\Interest;
use App\Domain\Discover\Services\HostDirectoryService;
use App\Domain\Experience\DTOs\ExperienceSearchData;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Queries\ExperienceSearchQuery;
use App\Domain\Experience\Support\ExperienceFormatter;
use App\Domain\Experience\ViewModels\ExperienceMarketplaceViewModel;
use App\Domain\Review\Services\ReviewHighlights;

/**
 * Experiences marketplace: the existing search / filter / sort logic
 * (ExperienceSearchQuery) presented as compact cards, interest chips,
 * local partners and booking-linked reviews.
 */
final class ExperienceMarketplaceService
{
    public function __construct(
        private readonly ExperienceSearchQuery $search,
        private readonly ExperienceCardPresenter $presenter,
        private readonly HostDirectoryService $hosts,
        private readonly ReviewHighlights $reviews,
        private readonly ExperienceFormatter $format,
    ) {}

    /** Everything the full page needs. */
    public function build(ExperienceSearchData $filters): ExperienceMarketplaceViewModel
    {
        return $this->make($filters, withExtras: true);
    }

    /** Only what changes when the visitor filters in place (no partners / reviews / cities queries). */
    public function results(ExperienceSearchData $filters): ExperienceMarketplaceViewModel
    {
        return $this->make($filters, withExtras: false);
    }

    private function make(ExperienceSearchData $filters, bool $withExtras): ExperienceMarketplaceViewModel
    {
        $locale = app()->getLocale();
        $page = $this->search->paginate($filters, (int) config('experiences.marketplace.per_page', 9), ['host']);
        $saved = auth()->user()?->wishlistedExperienceIds() ?? [];

        return new ExperienceMarketplaceViewModel(
            filters: $filters,
            chips: $this->chips($filters, $locale),
            cards: $page->getCollection()->map(fn (Experience $experience): array => $this->card($experience, $saved, $locale))->all(),
            paginator: $page,
            cities: $withExtras ? $this->cities() : [],
            partners: $withExtras ? $this->hosts->top((int) config('experiences.marketplace.partners', 6)) : [],
            reviews: $withExtras ? $this->reviews->build() : null,
            indexUrl: route('site.experiences.index', ['locale' => $locale]),
            hostsUrl: route('site.hosts.index', ['locale' => $locale]),
        );
    }

    /** "All" + one chip per interest; a chip selects that interest's experience categories. */
    private function chips(ExperienceSearchData $filters, string $locale): array
    {
        $current = $this->sorted($filters->categories);
        $url = fn (array $categories): string => $this->url($filters, ['category' => $categories ?: null]);

        return [
            ['label' => __('experiences.filters.all'), 'href' => $url([]), 'active' => $current === []],
            ...Interest::query()->active()->sorted()->get(['id', 'slug', 'name', 'category_slugs'])
                ->map(fn (Interest $interest): array => [
                    'label' => $interest->getTranslation('name', $locale, true),
                    'href' => $url($this->sorted((array) $interest->category_slugs)),
                    'active' => $current !== [] && $current === $this->sorted((array) $interest->category_slugs),
                ])
                ->all(),
        ];
    }

    /** @return list<array{value: string, label: string}> cities that have published experiences */
    private function cities(): array
    {
        return Experience::query()->published()
            ->whereNotNull('location_city')
            ->distinct()
            ->orderBy('location_city')
            ->pluck('location_city')
            ->map(fn (string $city): array => ['value' => $city, 'label' => $city])
            ->all();
    }

    /** @param list<int> $saved */
    private function card(Experience $experience, array $saved, string $locale): array
    {
        $card = $this->presenter->present($experience);

        return [
            'id' => $card->id,
            'href' => $card->showUrl,
            'title' => $card->title,
            'image' => $card->thumbnailUrl ?: (string) config('landing.images.card_fallback'),
            'meta' => $card->city.' · '.$this->format->duration((float) $experience->duration_hours),
            'host' => $experience->publicHost()?->name ?? $card->agencyName,
            'rating' => $card->reviewsCount > 0 ? $card->ratingAvg : null,
            'price' => $this->format->money((float) $experience->price_per_person, $card->currency, $locale),
            'wishlistUrl' => route('site.wishlist.toggle', ['locale' => $locale, 'experience' => $card->id]),
            'saved' => in_array($card->id, $saved, true),
        ];
    }

    /** Index URL for the current filters with some keys replaced (null removes a key). */
    private function url(ExperienceSearchData $filters, array $overrides): string
    {
        $query = array_filter([
            'search' => $filters->search,
            'city' => $filters->city,
            'category' => $filters->categories ?: null,
            'date' => $filters->date,
            'participants' => $filters->participants,
            'min_price' => $filters->minPrice,
            'max_price' => $filters->maxPrice,
            'private_available' => $filters->privateAvailable ? 1 : null,
            'pickup_available' => $filters->pickupAvailable ? 1 : null,
            'duration' => $filters->duration,
            'sort' => $filters->sort === 'recommended' ? null : $filters->sort,
            'host' => $filters->host,
            ...$overrides,
        ], fn ($value): bool => $value !== null && $value !== '' && $value !== []);

        return route('site.experiences.index', ['locale' => app()->getLocale()]).($query ? '?'.http_build_query($query) : '').'#results';
    }

    /** @param list<string> $values */
    private function sorted(array $values): array
    {
        $values = array_values(array_unique($values));
        sort($values);

        return $values;
    }
}
