<?php

namespace App\Domain\Review\Services;

use App\Domain\Review\Models\Review;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/** Star breakdown and top review cards, shared by the experience and host pages. */
final class ReviewSummary
{
    public function __construct(private readonly ReviewHighlights $authors) {}

    /**
     * @param  Collection<int, Review>  $reviews
     * @return list<array{stars: int, percent: int}>
     */
    public function breakdown(Collection $reviews): array
    {
        return collect([5, 4, 3, 2, 1])->map(fn (int $stars): array => [
            'stars' => $stars,
            'percent' => (int) round($reviews->where('rating', $stars)->count() / $reviews->count() * 100),
        ])->all();
    }

    /**
     * @param  Collection<int, Review>  $reviews
     * @return list<array<string, mixed>>
     */
    public function cards(Collection $reviews, string $locale, int $limit = 2): array
    {
        return $reviews
            ->filter(fn (Review $review): bool => filled($review->body))
            ->sortByDesc(fn (Review $review): string => $review->rating.'-'.$review->created_at?->timestamp)
            ->take($limit)
            ->map(fn (Review $review): array => [
                'rating' => (int) $review->rating,
                'body' => Str::limit(trim((string) $review->body), 160),
                'author' => $this->authors->author($review),
                'avatar' => $this->authors->avatar($review->user?->avatar),
                'initials' => Str::upper(Str::substr((string) ($review->user?->name ?? '?'), 0, 1)),
                'context' => ($travelled = $review->booking?->availability?->date ?? $review->created_at)
                    ? __('experiences.detail.v1.reviews.travelled', ['date' => $travelled->locale($locale)->translatedFormat('F Y')])
                    : null,
            ])
            ->values()
            ->all();
    }
}
