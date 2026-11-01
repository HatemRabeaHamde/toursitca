<?php

namespace App\Domain\Booking\Services;

use App\Domain\Booking\Models\Booking;
use App\Domain\Discover\Services\DiscoverUrls;
use App\Domain\Discover\Services\GuideExperienceCards;
use App\Domain\Discover\Services\StoryCards;

/** "Your request is with…" (Figma 12) with real suggestions for "While you wait". */
final class BookingRequestPageService
{
    public function __construct(
        private readonly BookingTripPresenter $trips,
        private readonly GuideExperienceCards $experiences,
        private readonly StoryCards $stories,
        private readonly DiscoverUrls $urls,
    ) {}

    public function build(Booking $booking): array
    {
        $booking->load(['experience.media', 'experience.host', 'experience.agency', 'availability']);
        $trip = $this->trips->fromBooking($booking);

        return [
            'trip' => $trip,
            'email' => $booking->guest_email,
            'hostName' => $trip['hostName'] ?? __('account.sent.your_host'),
            'status' => $booking->status,
            'bookingsUrl' => route('site.dashboard.bookings', ['locale' => app()->getLocale()]),
            'suggestions' => $this->suggestions($trip['title']),
        ];
    }

    private function suggestions(string $currentTitle): array
    {
        $story = $this->stories->featured(1)[0] ?? null;
        $pair = collect($this->experiences->featured(4))->firstWhere(fn (array $card): bool => $card['title'] !== $currentTitle);

        return array_values(array_filter([
            $story ? ['eyebrow' => __('account.sent.story'), 'title' => $story['title'], 'body' => null, 'image' => $story['image'], 'href' => $story['href'], 'link' => __('discover.story.read')] : null,
            ['eyebrow' => __('account.sent.practical'), 'title' => __('account.sent.practical_title'), 'body' => __('account.sent.practical_body'), 'image' => asset(config('discover.images.hosts_hero')), 'href' => $this->urls->hub('practical'), 'link' => __('account.sent.practical_link')],
            $pair ? ['eyebrow' => __('account.sent.pairs'), 'title' => $pair['title'], 'body' => $pair['summary'], 'image' => $pair['image'], 'href' => $pair['href'], 'link' => __('discover.experience.view_details')] : null,
        ]));
    }
}
