<?php

namespace App\Domain\Experience\Services;

use App\Domain\Agency\Models\Agency;
use App\Domain\Experience\DTOs\LandingPageData;
use App\Domain\Experience\Models\Experience;
use App\Domain\Landing\Services\LandingContentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class ExperienceLandingPageService
{
    public function __construct(
        private ExperienceCardPresenter $experienceCardPresenter,
        private LandingContentService $landingContentService,
        private LandingDemoContentFactory $landingDemoContentFactory,
    ) {}

    public function get(): LandingPageData
    {
        $cardSections = $this->cardSections();

        return new LandingPageData(
            stats: $this->stats(),
            trendingCards: $cardSections['trending'],
            newestCards: $cardSections['newest'],
            topRatedCards: $cardSections['top_rated'],
            dealCards: $cardSections['deals'],
            categories: $this->categories(),
            destinations: $this->destinations(),
            topAgencies: $this->topAgencies(),
            faqs: $this->faqs(),
            travelReels: $this->travelReels(),
            testimonials: $this->testimonials(),
        );
    }

    private function stats(): array
    {
        $stats = Experience::query()
            ->published()
            ->selectRaw('COUNT(*) as experiences_count')
            ->selectRaw('COUNT(DISTINCT location_city) as cities_count')
            ->selectRaw('AVG(rating_avg) as avg_rating')
            ->selectRaw('COALESCE(SUM(reviews_count), 0) as happy_travelers')
            ->first();

        if ((int) $stats->experiences_count === 0 && $this->showsDemoContent()) {
            return $this->landingDemoContentFactory->stats();
        }

        return [
            'experiences_count' => (int) $stats->experiences_count,
            'cities_count' => (int) $stats->cities_count,
            'avg_rating' => number_format((float) $stats->avg_rating, 1),
            'happy_travelers' => (int) $stats->happy_travelers,
        ];
    }

    private function cardSections(): array
    {
        $cardLimit = (int) config('landing.section_limits.cards', 6);
        $dealLimit = (int) config('landing.section_limits.deals', 6);
        $now = now();

        $idsBySection = [
            'trending' => $this->cardIds(fn (Builder $query): Builder => $query
                ->orderByDesc('is_top_rated')
                ->orderByDesc('rating_avg')
                ->orderByDesc('reviews_count'), $cardLimit),
            'newest' => $this->cardIds(fn (Builder $query): Builder => $query->latest(), $cardLimit),
            'top_rated' => $this->cardIds(fn (Builder $query): Builder => $query
                ->orderByDesc('rating_avg')
                ->orderByDesc('reviews_count'), $cardLimit),
            'deals' => $this->dealIds($now, $dealLimit),
        ];

        $cardMap = $this->cardMap(collect($idsBySection)->flatten()->unique()->values());

        return [
            'trending' => $this->withDemoCards($this->cardsForIds($idsBySection['trending'], $cardMap), $cardLimit),
            'newest' => $this->withDemoCards($this->cardsForIds($idsBySection['newest'], $cardMap), $cardLimit),
            'top_rated' => $this->withDemoCards($this->cardsForIds($idsBySection['top_rated'], $cardMap), $cardLimit),
            'deals' => $this->withDemoDeals($this->cardsForIds($idsBySection['deals'], $cardMap), $dealLimit),
        ];
    }

    private function cardIds(callable $sort, int $limit): Collection
    {
        $query = Experience::query()
            ->published()
            ->limit($limit);

        $sort($query);

        return $query->pluck('id');
    }

    private function dealIds(\DateTimeInterface $now, int $limit): Collection
    {
        return Experience::query()
            ->published()
            ->whereNotNull('original_price')
            ->whereNotNull('deal_ends_at')
            ->whereColumn('original_price', '>', 'price_per_person')
            ->where(function (Builder $query) use ($now): void {
                $query
                    ->whereNull('deal_starts_at')
                    ->orWhere('deal_starts_at', '<=', $now);
            })
            ->where('deal_ends_at', '>=', $now)
            ->orderByRaw('(original_price - price_per_person) / original_price DESC')
            ->orderBy('deal_ends_at')
            ->limit($limit)
            ->pluck('id');
    }

    private function cardMap(Collection $experienceIds): Collection
    {
        if ($experienceIds->isEmpty()) {
            return collect();
        }

        return Experience::query()
            ->with(ExperienceCardPresenter::eagerLoads())
            ->published()
            ->whereIn('id', $experienceIds)
            ->get()
            ->mapWithKeys(fn (Experience $experience): array => [
                $experience->id => $this->experienceCardPresenter->present($experience),
            ]);
    }

    private function cardsForIds(Collection $experienceIds, Collection $cardMap): Collection
    {
        return $experienceIds
            ->map(fn (int $id) => $cardMap->get($id))
            ->filter()
            ->values();
    }

    private function withDemoCards(Collection $cards, int $limit): Collection
    {
        return $cards->isEmpty() && $this->showsDemoContent()
            ? $this->landingDemoContentFactory->cards()->take($limit)
            : $cards;
    }

    private function withDemoDeals(Collection $cards, int $limit): Collection
    {
        return $cards->isEmpty() && $this->showsDemoContent()
            ? $this->landingDemoContentFactory->cards()->filter(fn ($card): bool => $card->isDeal)->take($limit)->values()
            : $cards;
    }

    private function categories(): Collection
    {
        $counts = Experience::query()
            ->published()
            ->selectRaw('category, COUNT(*) as experiences_count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->get()
            ->pluck('experiences_count', 'category');

        $managedCategories = $this->landingContentService->categories();

        if ($managedCategories->isNotEmpty()) {
            return $managedCategories->map(fn (array $category): array => $this->categoryCard($category, $counts));
        }

        return collect($this->landingCategorySlugs())
            ->map(fn (string $slug): array => $this->categoryCard([
                'slug' => $slug,
                'name' => __('ui.landing.category_names.'.$slug),
                'image_url' => null,
            ], $counts));
    }

    private function landingCategorySlugs(): array
    {
        return [
            'desert',
            'balloon',
            'food',
            'culture',
            'adventure',
            'wellness',
            'photography',
            'water',
        ];
    }

    private function destinations(): Collection
    {
        $counts = Experience::query()
            ->published()
            ->selectRaw('LOWER(location_city) as city_key, COUNT(*) as experiences_count')
            ->whereNotNull('location_city')
            ->groupBy('city_key')
            ->get()
            ->pluck('experiences_count', 'city_key');

        $managedDestinations = $this->landingContentService->destinations();

        if ($managedDestinations->isNotEmpty()) {
            return $managedDestinations->map(fn (array $destination): array => $this->destinationCard($destination, $counts));
        }

        return collect($this->landingDestinations())
            ->map(fn (array $destination): array => $this->destinationCard($destination, $counts));
    }

    private function landingDestinations(): array
    {
        return [
            ['name' => 'Marrakesh', 'city' => 'Marrakesh', 'is_popular' => true],
            ['name' => 'Fes', 'city' => 'Fes', 'is_popular' => false],
            ['name' => 'Sahara', 'city' => 'Sahara', 'is_popular' => false],
            ['name' => 'Chefchaouen', 'city' => 'Chefchaouen', 'is_popular' => false],
            ['name' => 'Essaouira', 'city' => 'Essaouira', 'is_popular' => false],
        ];
    }

    private function topAgencies(): Collection
    {
        $agencies = Agency::query()
            ->active()
            ->withCount(['experiences' => fn (Builder $query): Builder => $query->published()])
            ->withAvg(['experiences as rating_avg' => fn (Builder $query): Builder => $query->published()], 'rating_avg')
            ->withSum(['experiences as reviews_count' => fn (Builder $query): Builder => $query->published()], 'reviews_count')
            ->orderByDesc('experiences_count')
            ->limit(4)
            ->get()
            ->map(fn (Agency $agency): array => [
                'name' => $agency->name,
                'city' => $agency->city,
                'experiences_count' => (int) $agency->experiences_count,
                'rating_avg' => number_format((float) ($agency->rating_avg ?? 0), 1),
                'reviews_count' => (int) ($agency->reviews_count ?? 0),
                'is_verified' => $agency->status === 'active',
                'image_url' => $agency->logo ?: $this->destinationImage($agency->city),
            ]);

        return $agencies->isEmpty() && $this->showsDemoContent()
            ? $this->landingDemoContentFactory->topAgencies()
            : $agencies;
    }

    private function faqs(): Collection
    {
        $managedFaqs = $this->landingContentService->faqs();

        return $managedFaqs->isNotEmpty()
            ? $managedFaqs
            : collect(__('ui.landing.faq'));
    }

    private function travelReels(): Collection
    {
        $reels = $this->landingContentService->travelReels();

        return $reels->isEmpty() && $this->showsDemoContent()
            ? $this->landingDemoContentFactory->travelReels()
            : $reels;
    }

    private function testimonials(): Collection
    {
        $testimonials = $this->landingContentService->testimonials();

        return $testimonials->isEmpty() && $this->showsDemoContent()
            ? $this->landingDemoContentFactory->testimonials()
            : $testimonials;
    }

    private function categoryCard(array $category, Collection $counts): array
    {
        $slug = (string) $category['slug'];

        return [
            'slug' => $slug,
            'name' => $category['name'],
            'experiences_count' => (int) ($counts[$slug] ?? 0),
            'url' => route('site.experiences.index', [
                'locale' => app()->getLocale(),
                'category' => $slug,
            ]),
            'image_url' => ($category['image_url'] ?? null) ?: $this->categoryImage($slug),
        ];
    }

    private function destinationCard(array $destination, Collection $counts): array
    {
        $city = (string) $destination['city'];

        return [
            'name' => $destination['name'],
            'experiences_count' => (int) ($counts[mb_strtolower($city)] ?? 0),
            'url' => route('site.experiences.index', [
                'locale' => app()->getLocale(),
                'city' => $city,
            ]),
            'image_url' => ($destination['image_url'] ?? null) ?: $this->destinationImage($city),
            'is_popular' => (bool) ($destination['is_popular'] ?? $destination['is_featured'] ?? false),
        ];
    }

    private function categoryImage(?string $category): string
    {
        $images = config('landing.images.categories', []);

        return $images[$category] ?? $images['default'];
    }

    private function destinationImage(?string $city): string
    {
        $normalized = mb_strtolower((string) $city);

        $images = config('landing.images.destinations', []);

        return match (true) {
            str_contains($normalized, 'fes') || str_contains($normalized, 'fez') => $images['fes'],
            str_contains($normalized, 'sahara') => $images['sahara'],
            str_contains($normalized, 'zagora') => $images['zagora'],
            str_contains($normalized, 'chefchaouen') => $images['chefchaouen'],
            str_contains($normalized, 'essaouira') => $images['essaouira'],
            default => $images['default'],
        };
    }

    private function showsDemoContent(): bool
    {
        return app()->environment(config('landing.demo_environments', ['local', 'testing']));
    }
}
