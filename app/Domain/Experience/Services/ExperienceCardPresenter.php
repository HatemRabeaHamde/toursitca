<?php

namespace App\Domain\Experience\Services;

use App\Domain\Experience\DTOs\ExperienceCardData;
use App\Domain\Experience\Models\Experience;

final class ExperienceCardPresenter
{
    /**
     * Eager-load relations needed to present a card without N+1 queries.
     * Availability is scoped to active, upcoming slots only.
     *
     * @return array<string, mixed>
     */
    public static function eagerLoads(): array
    {
        return [
            'agency',
            'images',
            'availabilities' => fn ($query) => $query
                ->where('is_active', true)
                ->where('date', '>=', now()->toDateString()),
        ];
    }

    public function present(Experience $experience): ExperienceCardData
    {
        $image = $experience->images->first();
        $isPrivateAvailable = $experience->supportsPrivateBooking();
        $features = [];
        $badges = [];
        $tags = [];

        // A deal is a real, currently-active discount (price below original,
        // and either no end date or an end date still in the future).
        $isDeal = $experience->original_price !== null
            && (float) $experience->original_price > (float) $experience->price_per_person
            && ($experience->deal_ends_at === null || $experience->deal_ends_at->isFuture())
            && ($experience->deal_starts_at === null || $experience->deal_starts_at->isPast());

        if ($isDeal) {
            $badges[] = __('ui.badges.deal');
        } elseif ($experience->is_top_rated) {
            $badges[] = __('ui.badges.top_rated');
        }

        if ($isPrivateAvailable) {
            $features[] = __('ui.features.private_option_available');
        }

        if ($experience->pickup_enabled) {
            $features[] = __('ui.features.pickup_available');
        }

        // Card chips, each derived from real experience data (no invented tags).
        if ((int) $experience->max_group_size <= (int) config('booking.small_group_max', 12)) {
            $tags[] = __('ui.tags.small_group');
        }

        if ($isPrivateAvailable) {
            $tags[] = __('ui.tags.private_option');
        }

        if ((float) $experience->duration_hours > (float) config('booking.overnight_hours', 8)) {
            $tags[] = __('ui.tags.overnight');
        }

        [$availabilityStatus, $spotsLeft] = $this->availability($experience);

        return new ExperienceCardData(
            id: $experience->id,
            slug: $experience->slug,
            title: $experience->getTranslation('title', app()->getLocale(), false),
            city: $experience->location_city,
            category: $experience->category,
            // Platform-owned experiences have no public host to credit.
            agencyName: $experience->agency && ! $experience->agency->is_platform
                ? $experience->agency->name
                : null,
            thumbnailUrl: $image?->publicUrl(),
            durationLabel: $this->durationLabel((float) $experience->duration_hours),
            ratingAvg: number_format((float) $experience->rating_avg, 1),
            reviewsCount: (int) $experience->reviews_count,
            badges: $badges,
            features: $features,
            tags: $tags,
            priceFrom: number_format((float) $experience->price_per_person, 2),
            originalPrice: $experience->original_price !== null ? number_format((float) $experience->original_price, 2) : null,
            currency: config('payment.currency', 'MAD'),
            isPrivateAvailable: $isPrivateAvailable,
            isPickupAvailable: (bool) $experience->pickup_enabled,
            isDeal: $isDeal,
            dealEndsAt: $isDeal ? $experience->deal_ends_at : null,
            availabilityStatus: $availabilityStatus,
            spotsLeft: $spotsLeft,
            showUrl: route('site.experiences.show', ['locale' => app()->getLocale(), 'experience' => $experience->slug]),
        );
    }

    private function durationLabel(float $hours): string
    {
        $normalized = rtrim(rtrim(number_format($hours, 1), '0'), '.');

        return trans_choice('ui.units.hours', (int) ceil($hours), ['count' => $normalized]);
    }

    /**
     * @return array{0: string, 1: ?int}
     */
    private function availability(Experience $experience): array
    {
        $totalSeats = $experience->availabilities->sum(fn ($availability) => $availability->availableSeats());

        if ($experience->availabilities->isEmpty() || $totalSeats <= 0) {
            return ['sold_out', null];
        }

        if ($totalSeats <= (int) config('booking.low_availability_threshold', 5)) {
            return ['few_spots', $totalSeats];
        }

        return ['available', null];
    }
}
