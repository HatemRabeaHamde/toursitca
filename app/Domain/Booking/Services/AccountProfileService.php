<?php

namespace App\Domain\Booking\Services;

use App\Domain\Booking\Models\Booking;
use App\Domain\Review\Models\Review;
use App\Domain\Review\Services\ReviewHighlights;
use App\Models\User;
use Illuminate\Support\Str;

/** My profile (Figma 13): account fields, the traveller's reviews and trips still to review. */
final class AccountProfileService
{
    public function __construct(private readonly ReviewHighlights $authors) {}

    public function build(User $user): array
    {
        $locale = app()->getLocale();
        [$first, $last] = array_pad(preg_split('/\s+/', trim($user->name), 2), 2, '');

        return [
            'user' => $user,
            'firstName' => $first,
            'lastName' => $last,
            'avatar' => $this->authors->avatar($user->avatar),
            'initial' => Str::upper(Str::substr($user->name, 0, 1)),
            'memberLine' => collect([
                __('account.profile.member_since', ['date' => $user->created_at->locale($locale)->translatedFormat('F Y')]),
                $user->country,
            ])->filter()->implode(' · '),
            'updatedAt' => $user->updated_at->locale($locale)->translatedFormat('M j, Y'),
            'reviews' => Review::query()
                ->with('experience.media')
                ->where('user_id', $user->id)
                ->latest()
                ->get()
                ->map(fn (Review $review): array => [
                    'title' => (string) $review->experience?->getTranslation('title', $locale, true),
                    'image' => $review->experience?->media->firstWhere('type', 'image')?->publicUrl(),
                    'line' => __('account.profile.review_line', [
                        'date' => $review->created_at->locale($locale)->translatedFormat('F Y'),
                        'rating' => number_format((float) $review->rating, 1),
                    ]),
                    'visible' => (bool) $review->is_visible,
                ])
                ->all(),
            'toReview' => Booking::query()
                ->with('experience')
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereDoesntHave('review')
                ->latest()
                ->get()
                ->map(fn (Booking $booking): array => [
                    'title' => (string) $booking->experience?->getTranslation('title', $locale, true),
                    'href' => route('site.reviews.create', ['locale' => $locale, 'booking' => $booking->id]),
                ])
                ->all(),
        ];
    }
}
