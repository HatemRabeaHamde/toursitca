<?php

namespace App\Domain\Booking\Services;

use App\Domain\Booking\Models\Booking;
use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Support\ExperienceFormatter;
use Illuminate\Support\Str;

/** One trip's display values, shared by the request pages and My bookings. */
final class BookingTripPresenter
{
    public function __construct(private readonly ExperienceFormatter $format) {}

    public function fromCheckout(CheckoutSession $session): array
    {
        return $this->present(
            $session->experience,
            $session->availability,
            (array) $session->participants,
            (int) $session->participants_count,
            (string) $session->booking_type,
            (float) ($session->price_snapshot['unit_price'] ?? 0),
            (float) ($session->price_snapshot['total'] ?? 0),
            (string) $session->currency,
            $session->option?->getTranslation('title', app()->getLocale(), true),
        );
    }

    public function fromBooking(Booking $booking): array
    {
        return $this->present(
            $booking->experience,
            $booking->availability,
            (array) $booking->participants,
            (int) $booking->participants_count,
            (string) $booking->booking_type,
            (float) $booking->unit_price,
            (float) $booking->total_price,
            (string) ($booking->price_snapshot['currency'] ?? config('payment.currency', 'MAD')),
            $booking->option?->getTranslation('title', app()->getLocale(), true),
        );
    }

    private function present(Experience $experience, Availability $availability, array $participants, int $count, string $type, float $unit, float $total, string $currency, ?string $option = null): array
    {
        $locale = app()->getLocale();
        $host = $experience->host;
        $hours = (float) $experience->duration_hours;

        return [
            'title' => (string) $experience->getTranslation('title', $locale, true),
            'href' => route('site.experiences.show', ['locale' => $locale, 'experience' => $experience->slug]),
            'image' => $this->image($experience),
            'place' => collect([$experience->location_city, $this->format->duration($hours)])->filter()->implode(' · '),
            'hostName' => $host?->name,
            'hostLine' => $host ? collect([$host->name, $host->getTranslation('role', $locale, true)])->filter()->implode(' · ') : $experience->agency?->name,
            'hostAvatar' => ($avatar = $host?->avatar_url ?: $host?->photo_url) ? $this->url($avatar) : null,
            'hostVerified' => (bool) $host?->is_verified,
            'hostLicence' => $host?->verifiedLicence(),
            'option' => $option ?: null,
            'dates' => $this->dates($availability, $hours),
            'date' => $availability->date->locale($locale),
            'travellers' => $this->travellers($participants, $count),
            'travellersShort' => trans_choice('experiences.detail.booking.travellers_value', $count, ['count' => $count]),
            'group' => $type === 'private' ? __('experiences.detail.v1.booking.private') : __('experiences.detail.v1.booking.small_group'),
            'groupLine' => $type === 'private' ? __('experiences.detail.v1.booking.private') : __('experiences.detail.booking.group_shared', ['count' => (int) $experience->max_group_size]),
            'priceLine' => $type === 'private'
                ? __('experiences.detail.booking.breakdown_flat')
                : $this->format->money($unit, $currency).' × '.trans_choice('experiences.detail.booking.travellers_value', $count, ['count' => $count]),
            'total' => $this->format->money($total, $currency),
        ];
    }

    private function dates(Availability $availability, float $hours): string
    {
        $start = $availability->date->copy()->locale(app()->getLocale());
        $days = $hours >= 24 ? (int) ceil($hours / 24) : 1;

        if ($days <= 1) {
            return $start->translatedFormat('M j, Y');
        }

        $end = $start->copy()->addDays($days - 1);

        return $start->translatedFormat('M j').' – '.$end->translatedFormat($start->isSameMonth($end) ? 'j, Y' : 'M j, Y');
    }

    private function travellers(array $participants, int $count): string
    {
        $adults = (int) ($participants['adult'] ?? $count);
        $children = (int) ($participants['child'] ?? 0);

        return collect([
            trans_choice('experiences.detail.booking.adults_value', $adults, ['count' => $adults]),
            $children > 0 ? trans_choice('experiences.detail.booking.children_value', $children, ['count' => $children]) : null,
        ])->filter()->implode(', ');
    }

    private function image(Experience $experience): string
    {
        $path = $experience->media->firstWhere('type', 'image')?->publicUrl() ?? $experience->thumbnail;

        return $path ? $this->url($path) : (string) config('landing.images.card_fallback');
    }

    private function url(string $path): string
    {
        return Str::startsWith($path, ['http://', 'https://', '/']) ? $path : asset($path);
    }
}
