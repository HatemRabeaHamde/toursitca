<?php

namespace App\Domain\Discover\Services;

use App\Domain\Landing\Models\Testimonial;
use Illuminate\Database\Eloquent\Builder;

/**
 * Traveller story cards (Figma "Card / Story") from post-trip testimonials.
 *
 * @phpstan-type StoryCard array{href: string, title: string, image: string, location: ?string, days: ?int, rating: string}
 */
final class StoryCards
{
    /** @return list<StoryCard> */
    public function featured(int $limit = 3): array
    {
        return $this->cards(Testimonial::query(), $limit);
    }

    /** @return list<StoryCard> */
    public function forDestination(int $destinationId, int $limit = 2): array
    {
        return $this->cards(Testimonial::query()->where('destination_id', $destinationId), $limit);
    }

    /** @return list<StoryCard> */
    public function forInterest(int $interestId, int $limit = 1): array
    {
        return $this->cards(Testimonial::query()->where('interest_id', $interestId), $limit);
    }

    /** @return list<StoryCard> */
    private function cards(Builder $query, int $limit): array
    {
        $href = route('site.home', ['locale' => app()->getLocale()]).'#stories';

        return $query->active()->whereNotNull('image_url')->sorted()->limit($limit)->get()
            ->map(fn (Testimonial $story): array => [
                'href' => $href,
                'title' => (string) $story->experience_title,
                'image' => (string) $story->image_url,
                'location' => $story->city,
                'days' => $story->days,
                'rating' => number_format((float) $story->rating, 1),
            ])
            ->all();
    }
}
