<?php

namespace App\Domain\Review\DTOs;

/** What the visitor asked the Traveller reviews page for. */
final readonly class ReviewDirectoryFilters
{
    public const SORTS = ['recent', 'highest', 'lowest'];

    public const RATINGS = [5, 4, 3];

    public function __construct(
        public ?string $interest = null,
        public ?string $experience = null,
        public ?string $host = null,
        public ?int $rating = null,
        public bool $withPhotos = false,
        public string $sort = 'recent',
    ) {}

    /** Query string for these filters with overrides (null removes a key). */
    public function query(array $overrides = []): array
    {
        return array_filter([
            'interest' => $this->interest,
            'experience' => $this->experience,
            'host' => $this->host,
            'rating' => $this->rating,
            'photos' => $this->withPhotos ? 1 : null,
            'sort' => $this->sort === 'recent' ? null : $this->sort,
            ...$overrides,
        ], fn ($value): bool => $value !== null && $value !== '');
    }
}
