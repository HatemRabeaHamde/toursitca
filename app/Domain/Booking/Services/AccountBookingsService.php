<?php

namespace App\Domain\Booking\Services;

use App\Domain\Booking\Models\Booking;
use App\Models\User;
use Illuminate\Support\Collection;

/** My bookings (Figma 14): tabs with counts, cards grouped by what happens next. */
final class AccountBookingsService
{
    public const TABS = ['all', 'requests', 'upcoming', 'past', 'cancelled'];

    public function __construct(private readonly BookingTripPresenter $trips) {}

    public function build(User $user, string $tab): array
    {
        $bookings = Booking::query()
            ->with(['experience.media', 'experience.host', 'experience.agency', 'availability', 'review'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $groups = [
            'requests' => $bookings->where('status', 'pending'),
            'upcoming' => $bookings->where('status', 'confirmed'),
            'past' => $bookings->where('status', 'completed'),
            'cancelled' => $bookings->where('status', 'cancelled'),
        ];

        return [
            'tab' => $tab,
            'tabs' => collect(self::TABS)->map(fn (string $key): array => [
                'key' => $key,
                'label' => __('account.bookings.tabs.'.$key),
                'count' => $key === 'all' ? $bookings->count() : $groups[$key]->count(),
                'href' => route('site.dashboard.bookings', ['locale' => app()->getLocale(), 'tab' => $key === 'all' ? null : $key]),
            ])->all(),
            'groups' => collect($groups)
                ->filter(fn (Collection $group, string $key): bool => $group->isNotEmpty() && in_array($tab, ['all', $key], true))
                ->map(fn (Collection $group): array => $group->map(fn (Booking $booking): array => $this->card($booking))->values()->all())
                ->all(),
        ];
    }

    private function card(Booking $booking): array
    {
        $locale = app()->getLocale();

        return [
            ...$this->trips->fromBooking($booking),
            'status' => $booking->status,
            'stamp' => $booking->status === 'pending'
                ? __('account.bookings.requested', ['date' => $booking->created_at->locale($locale)->translatedFormat('M j, Y')])
                : __('experiences.detail.v1.reviews.travelled', ['date' => $booking->availability->date->locale($locale)->translatedFormat('F Y')]),
            'showUrl' => route('site.dashboard.bookings.show', ['locale' => $locale, 'booking' => $booking->id]),
            'cancelUrl' => $booking->status === 'pending' ? route('site.dashboard.bookings.cancel', ['locale' => $locale, 'booking' => $booking->id]) : null,
            'reviewUrl' => $booking->status === 'completed' && ! $booking->review ? route('site.reviews.create', ['locale' => $locale, 'booking' => $booking->id]) : null,
            'reviewRating' => $booking->review?->rating,
        ];
    }
}
