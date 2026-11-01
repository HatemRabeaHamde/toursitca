<?php

namespace App\Domain\Review\Services;

use App\Domain\Review\Models\Review;
use Illuminate\Support\Str;

/**
 * Real, booking-linked traveller reviews for marketing sections
 * ("Written after the trip, never before."). Returns null when there is
 * nothing honest to show yet, so the section simply stays hidden.
 */
final class ReviewHighlights
{
    /** @return array{average: string, count: int, reviews: list<array<string, mixed>>}|null */
    public function build(int $limit = 3): ?array
    {
        $visible = Review::query()->where('is_visible', true)->whereNotNull('booking_id');
        $count = (clone $visible)->count();

        if ($count === 0) {
            return null;
        }

        $locale = app()->getLocale();

        return [
            'average' => number_format((float) (clone $visible)->avg('rating'), 1),
            'count' => $count,
            'reviews' => (clone $visible)
                ->where('rating', '>=', 4)
                ->whereNotNull('body')
                ->with(['user:id,name,avatar', 'booking:id,contact_country', 'experience:id,title,host_id', 'experience.host:id,name'])
                ->latest()
                ->limit($limit)
                ->get()
                ->map(fn (Review $review): array => [
                    'rating' => (int) $review->rating,
                    'body' => Str::limit(trim((string) $review->body), 220),
                    'author' => $this->author($review),
                    'initials' => Str::upper(Str::substr((string) ($review->user?->name ?? '?'), 0, 1)),
                    'avatar' => $this->avatar($review->user?->avatar),
                    'context' => collect([
                        $review->experience?->getTranslation('title', $locale, true),
                        $review->experience?->host ? __('experiences.card.with', ['name' => $review->experience->host->name]) : null,
                    ])->filter()->implode(' · '),
                ])
                ->all(),
        ];
    }

    /** "Sophie, Canada": first name + the country given at checkout. */
    public function author(Review $review): string
    {
        $name = $review->user?->name ? Str::before(trim($review->user->name), ' ') : __('experiences.reviews.anonymous');

        return collect([$name, $review->booking?->contact_country])->filter()->implode(', ');
    }

    public function avatar(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://', '/']) ? $path : asset($path);
    }
}
