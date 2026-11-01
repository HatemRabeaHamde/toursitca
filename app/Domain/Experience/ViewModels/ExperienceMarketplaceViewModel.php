<?php

namespace App\Domain\Experience\ViewModels;

use App\Domain\Experience\DTOs\ExperienceSearchData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/** Experiences marketplace (Figma "07 · Experiences — Marketplace v2") ready for Blade. No queries here. */
final readonly class ExperienceMarketplaceViewModel
{
    public const SORTS = ['recommended', 'rating', 'price_low', 'price_high', 'newest'];

    /**
     * @param  list<array{label: string, href: string, active: bool}>  $chips
     * @param  list<array<string, mixed>>  $cards
     * @param  list<array{value: string, label: string}>  $cities
     * @param  list<array<string, mixed>>  $partners
     * @param  array{average: string, count: int, reviews: list<array<string, mixed>>}|null  $reviews
     */
    public function __construct(
        public ExperienceSearchData $filters,
        public array $chips,
        public array $cards,
        public LengthAwarePaginator $paginator,
        public array $cities,
        public array $partners,
        public ?array $reviews,
        public string $indexUrl,
        public string $hostsUrl,
    ) {}

    public function total(): int
    {
        return $this->paginator->total();
    }

    public function shown(): int
    {
        return min($this->paginator->currentPage() * $this->paginator->perPage(), $this->total());
    }

    public function nextPageUrl(): ?string
    {
        return $this->paginator->nextPageUrl();
    }

    /** @return list<array{value: string, label: string}> */
    public function sorts(): array
    {
        return array_map(fn (string $sort): array => ['value' => $sort, 'label' => __('experiences.sort.'.$sort)], self::SORTS);
    }

    /** @return list<array{value: string, label: string}> */
    public function travellers(): array
    {
        return array_map(
            fn (int $count): array => ['value' => (string) $count, 'label' => trans_choice('experiences.search.travellers', $count, ['count' => $count])],
            range(1, (int) config('booking.max_participants', 12)),
        );
    }

    /** @return list<array{value: string, label: string}> */
    public function durations(): array
    {
        return array_map(fn (string $duration): array => ['value' => $duration, 'label' => __('experiences.filters.durations.'.$duration)], ['half', 'full', 'multi']);
    }

    /** Number of drawer filters in use (badge on the "Filters" button). */
    public function activeFilterCount(): int
    {
        $f = $this->filters;

        return count(array_filter([$f->search, $f->duration, $f->minPrice, $f->maxPrice, $f->privateAvailable ?: null, $f->pickupAvailable ?: null, $f->host]));
    }
}
