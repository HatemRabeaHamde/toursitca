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
    ) {}

    public function get(): LandingPageData
    {
        return new LandingPageData(
            stats: $this->stats(),
            trendingCards: $this->cards(fn (Builder $query): Builder => $query
                ->orderByDesc('is_top_rated')
                ->orderByDesc('rating_avg')
                ->orderByDesc('reviews_count')),
            newestCards: $this->cards(fn (Builder $query): Builder => $query->latest()),
            topRatedCards: $this->cards(fn (Builder $query): Builder => $query
                ->orderByDesc('rating_avg')
                ->orderByDesc('reviews_count')),
            dealCards: $this->deals(),
            categories: $this->categories(),
            destinations: $this->destinations(),
            topAgencies: $this->topAgencies(),
            faqs: $this->faqs(),
            travelReels: $this->landingContentService->travelReels(),
            testimonials: $this->landingContentService->testimonials(),
        );
    }

    private function stats(): array
    {
        $base = Experience::query()->published();

        return [
            'experiences_count' => (clone $base)->count(),
            'cities_count' => (clone $base)
                ->whereNotNull('location_city')
                ->distinct('location_city')
                ->count('location_city'),
            'avg_rating' => number_format((float) (clone $base)->avg('rating_avg'), 1),
            'happy_travelers' => (int) (clone $base)->sum('reviews_count'),
        ];
    }

    private function cards(callable $sort, int $limit = 6): Collection
    {
        $query = Experience::query()
            ->with(ExperienceCardPresenter::eagerLoads())
            ->published()
            ->limit($limit);

        $sort($query);

        return $query
            ->get()
            ->map(fn (Experience $experience) => $this->experienceCardPresenter->present($experience));
    }

    private function deals(int $limit = 6): Collection
    {
        $now = now();

        return Experience::query()
            ->with(ExperienceCardPresenter::eagerLoads())
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
            ->get()
            ->map(fn (Experience $experience) => $this->experienceCardPresenter->present($experience));
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
        return Agency::query()
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
    }

    private function faqs(): Collection
    {
        $managedFaqs = $this->landingContentService->faqs();

        return $managedFaqs->isNotEmpty()
            ? $managedFaqs
            : collect(__('ui.landing.faq'));
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
        return match ($category) {
            'balloon' => 'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=300&q=80',
            'food' => 'https://images.unsplash.com/photo-1548013146-72479768bada?w=300&q=80',
            'cultural', 'culture' => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=300&q=80',
            'mountain', 'adventure' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?w=300&q=80',
            'wellness' => 'https://images.unsplash.com/photo-1560180474-e8563fd75bab?w=300&q=80',
            'photography' => 'https://images.unsplash.com/photo-1470770903676-69b98201ea1c?w=300&q=80',
            'water' => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=300&q=80',
            default => 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=300&q=80',
        };
    }

    private function destinationImage(?string $city): string
    {
        $normalized = mb_strtolower((string) $city);

        return match (true) {
            str_contains($normalized, 'fes') || str_contains($normalized, 'fez') => 'https://images.unsplash.com/photo-1548013146-72479768bada?w=900&q=80',
            str_contains($normalized, 'sahara') || str_contains($normalized, 'zagora') => 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=900&q=80',
            str_contains($normalized, 'chefchaouen') => 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=900&q=80',
            str_contains($normalized, 'essaouira') => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=900&q=80',
            default => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=900&q=80',
        };
    }
}
