<?php

namespace App\Domain\Review\ViewModels;

use App\Domain\Review\DTOs\ReviewDirectoryFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/** Traveller reviews page ready for Blade. No queries here. */
final readonly class ReviewsPageViewModel
{
    /**
     * @param  list<array{label: string, href: string, active: bool}>  $chips
     * @param  list<array<string, mixed>>  $reviews
     * @param  array<string, mixed>|null  $summary
     * @param  list<array{value: string, label: string}>  $experiences
     * @param  list<array{value: string, label: string}>  $hosts
     */
    public function __construct(
        public ReviewDirectoryFilters $filters,
        public array $chips,
        public array $reviews,
        public LengthAwarePaginator $paginator,
        public ?array $summary,
        public array $experiences,
        public array $hosts,
        public string $indexUrl,
        public string $experiencesUrl,
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

    /** @return list<array{label: string, href: string}> */
    public function breadcrumb(): array
    {
        return [
            ['label' => __('discover.breadcrumb.home'), 'href' => route('site.home', ['locale' => app()->getLocale()])],
            ['label' => __('reviews.breadcrumb.experiences'), 'href' => $this->experiencesUrl],
            ['label' => __('reviews.breadcrumb.reviews'), 'href' => null],
        ];
    }

    /** @return list<array{value: string, label: string}> */
    public function ratings(): array
    {
        return array_map(
            fn (int $rating): array => ['value' => (string) $rating, 'label' => __('reviews.filters.ratings.'.$rating)],
            ReviewDirectoryFilters::RATINGS,
        );
    }

    /** @return list<array{value: string, label: string}> */
    public function sorts(): array
    {
        return array_map(fn (string $sort): array => ['value' => $sort, 'label' => __('reviews.sort.'.$sort)], ReviewDirectoryFilters::SORTS);
    }
}
