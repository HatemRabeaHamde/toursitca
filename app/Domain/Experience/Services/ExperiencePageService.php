<?php

namespace App\Domain\Experience\Services;

use App\Domain\Discover\Services\DiscoverUrls;
use App\Domain\Experience\DTOs\ExperienceDetailData;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Support\ExperienceFormatter;
use App\Domain\Experience\ViewModels\ExperiencePageViewModel;
use App\Domain\Review\Models\Review;
use App\Domain\Review\Services\ReviewHighlights;
use App\Domain\Review\Services\ReviewSummary;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Locale;

/**
 * Experience detail: the story of one experience (overview, day-by-day
 * itinerary, host, reviews) around the existing booking logic, which still
 * comes from ExperienceDetailService (calendar, options, quote, checkout).
 */
final class ExperiencePageService
{
    public function __construct(
        private readonly ExperienceDetailService $details,
        private readonly ExperienceFormatter $format,
        private readonly ReviewHighlights $reviewAuthors,
        private readonly ReviewSummary $reviewSummary,
        private readonly DiscoverUrls $urls,
    ) {}

    public function build(Experience $experience): ExperiencePageViewModel
    {
        $experience->loadMissing(['host', 'reviews.booking.availability', 'reviews.user']);
        $detail = $this->details->get($experience);
        $locale = app()->getLocale();
        $reviews = $experience->reviews->where('is_visible', true)->values();
        $languages = $this->languages($detail);

        return new ExperiencePageViewModel(
            title: $detail->title,
            description: (string) $detail->description,
            breadcrumb: $this->urls->breadcrumb([
                ['label' => __('experiences.detail.breadcrumb'), 'href' => $this->urls->experiences()],
                ['label' => $experience->location_city, 'href' => $this->urls->experiences(['city' => $experience->location_city])],
                ['label' => $detail->title, 'href' => null],
            ]),
            hero: $this->hero($experience, $detail, $reviews),
            gallery: array_column(array_filter($detail->gallery, fn (array $media): bool => $media['type'] === 'image'), 'url'),
            highlights: $this->lines($experience, 'highlights', $locale),
            itinerary: $this->itinerary($experience, $detail),
            included: [
                'items' => $this->lines($experience, 'inclusions', $locale),
                'excluded' => ($excluded = $this->lines($experience, 'exclusions', $locale))
                    ? __('experiences.detail.included.not_included', ['items' => implode(', ', $excluded)])
                    : null,
                'excludedItems' => $excluded,
            ],
            host: $this->host($experience, $languages, $locale),
            reviews: $this->reviews($experience, $reviews, $locale),
            booking: $this->booking($experience, $detail, $reviews, $languages, $locale),
            related: $this->related($detail, $locale),
            location: $this->location($experience, $detail),
        );
    }

    /** @param Collection<int, Review> $reviews */
    private function hero(Experience $experience, ExperienceDetailData $detail, Collection $reviews): array
    {
        $stops = count($detail->itinerary['items'] ?? []);
        $group = (int) $experience->max_group_size;

        return [
            'image' => $detail->gallery[0]['url'] ?? (string) config('landing.images.card_fallback'),
            'video' => collect($detail->gallery)->firstWhere('type', 'video')['url'] ?? null,
            'location' => collect([$experience->location_city, __('experiences.detail.country')])->filter()->implode(', '),
            'meta' => array_values(array_filter([
                ['icon' => 'clock-cream', 'label' => $this->format->duration((float) $experience->duration_hours)],
                $stops > 1 && ! $this->isMultiDay($experience) ? ['icon' => 'route-cream', 'label' => trans_choice('experiences.detail.stops', $stops, ['count' => $stops])] : null,
                $group > 0 ? ['icon' => 'users-cream', 'label' => __($group <= 12 ? 'experiences.detail.small_group' : 'experiences.detail.group', ['count' => $group])] : null,
            ])),
            'rating' => $reviews->isNotEmpty() ? number_format((float) $reviews->avg('rating'), 1) : null,
            'reviews' => $reviews->count(),
            'verified' => (bool) ($experience->publicHost()?->is_verified ?? $detail->agencySummary['is_verified']),
        ];
    }

    /** Day-by-day for multi-day trips, step-by-step otherwise. */
    private function itinerary(Experience $experience, ExperienceDetailData $detail): array
    {
        $images = $experience->itineraryItems->pluck('image', 'id');
        $key = $this->isMultiDay($experience) ? 'experiences.detail.itinerary.day' : 'experiences.detail.itinerary.step';

        return collect($detail->itinerary['items'] ?? [])
            ->values()
            ->map(fn (array $item, int $index): array => [
                'label' => __($key, ['number' => $index + 1]),
                'title' => (string) ($item['title'] ?: $item['location_name']),
                'description' => $item['description'] ?: null,
                'image' => ($path = $images[$item['id']] ?? null) ? $this->url($path) : null,
                'duration' => $item['duration'] ?? null,
            ])
            ->all();
    }

    /** @param list<array{code: string, label: string}> $languages */
    private function host(Experience $experience, array $languages, string $locale): ?array
    {
        $host = $experience->publicHost();

        if (! $host) {
            return null;
        }

        return [
            'name' => $host->name,
            'firstName' => Str::before(trim($host->name), ' '),
            'avatar' => ($avatar = $host->avatar_url ?: $host->photo_url) ? $this->url($avatar) : null,
            'line' => collect([
                $host->getTranslation('role', $locale, true),
                $host->city,
                implode(', ', array_column($languages, 'label')),
            ])->filter()->implode(' · '),
            'verified' => (bool) $host->is_verified,
            'licence' => $host->verifiedLicence(),
            'role' => collect([$host->getTranslation('role', $locale, true), $host->city])->filter()->implode(' · '),
            'bio' => $host->getTranslation('bio', $locale, false) ?: null,
            'photo' => ($photo = $host->photo_url ?: $host->avatar_url) ? $this->url($photo) : null,
            'languages' => implode(', ', array_column($languages, 'label')),
            'href' => $this->urls->host($host->slug),
        ];
    }

    /** @param Collection<int, Review> $reviews */
    private function reviews(Experience $experience, Collection $reviews, string $locale): ?array
    {
        if ($reviews->isEmpty()) {
            return null;
        }

        $featured = $reviews
            ->filter(fn (Review $review): bool => filled($review->body) && $review->rating >= 4)
            ->sortByDesc(fn (Review $review): string => $review->rating.'-'.$review->created_at?->timestamp)
            ->first();

        return [
            'average' => number_format((float) $reviews->avg('rating'), 1),
            'count' => $reviews->count(),
            'featured' => $featured ? [
                'body' => Str::limit(trim((string) $featured->body), 260),
                'author' => collect([
                    $this->reviewAuthors->author($featured),
                    ($travelled = $featured->booking?->availability?->date ?? $featured->created_at)
                        ? $travelled->locale($locale)->translatedFormat('F Y')
                        : null,
                ])->filter()->implode(' · '),
                'avatar' => $this->reviewAuthors->avatar($featured->user?->avatar),
                'initials' => Str::upper(Str::substr((string) ($featured->user?->name ?? '?'), 0, 1)),
            ] : null,
            'href' => $this->urls->reviews(['experience' => $experience->slug]).'#results',
            'breakdown' => $this->reviewSummary->breakdown($reviews),
            'cards' => $this->reviewSummary->cards($reviews, $locale),
        ];
    }

    /**
     * Location card + schematic route map: only stops with real coordinates are
     * drawn (viewBox 470 × 270); null when there is nothing true to show.
     */
    private function location(Experience $experience, ExperienceDetailData $detail): ?array
    {
        $stops = collect($detail->itinerary['items'] ?? [])
            ->filter(fn (array $item): bool => $item['lat'] !== null && $item['lng'] !== null && filled($item['location_name']))
            ->values();
        $note = $experience->meeting_point
            ?: ($experience->pickup_enabled ? __('experiences.detail.booking.pickup_note_generic') : null);

        if ($stops->count() < 2 && ! $note) {
            return null;
        }

        $first = $stops->first();
        $lat = $first['lat'] ?? ($experience->location_lat !== null ? (float) $experience->location_lat : null);
        $lng = $first['lng'] ?? ($experience->location_lng !== null ? (float) $experience->location_lng : null);

        return [
            'place' => $first['location_name'] ?? $experience->location_city,
            'note' => $note,
            'mapUrl' => $lat !== null && $lng !== null ? 'https://www.google.com/maps/search/?api=1&query='.$lat.','.$lng : null,
            'stops' => $stops->count() >= 2 ? $this->projectStops($stops) : [],
        ];
    }

    /** @param Collection<int, array<string, mixed>> $stops */
    private function projectStops(Collection $stops): array
    {
        [$minLat, $maxLat] = [$stops->min('lat'), $stops->max('lat')];
        [$minLng, $maxLng] = [$stops->min('lng'), $stops->max('lng')];

        return $stops->map(fn (array $stop): array => [
            'name' => $stop['location_name'],
            'x' => round(70 + (($stop['lng'] - $minLng) / max($maxLng - $minLng, 0.0001)) * 300),
            'y' => round(60 + (($maxLat - $stop['lat']) / max($maxLat - $minLat, 0.0001)) * 150),
        ])->all();
    }

    /**
     * Booking card: display values + the config the existing experienceBooking
     * store already understands (same quote / checkout endpoints).
     *
     * @param  Collection<int, Review>  $reviews
     * @param  list<array{code: string, label: string}>  $languages
     */
    private function booking(Experience $experience, ExperienceDetailData $detail, Collection $reviews, array $languages, string $locale): array
    {
        $summary = $detail->bookingSummary;
        $currency = $summary['currency'];

        $options = array_map(function (array $option): array {
            $price = fn (string $type, string $key = 'price'): float => (float) (collect($option['prices'])->firstWhere('type', $type)[$key] ?? 0);

            return [
                'id' => $option['id'],
                'title' => (string) $option['title'],
                'priceType' => $option['price_type'],
                'adultPrice' => $price('adult'),
                'childPrice' => $price('child'),
                'groupPrice' => $price('group'),
                'adultOriginal' => $price('adult', 'original_price'),
                'childOriginal' => $price('child', 'original_price'),
                'groupOriginal' => $price('group', 'original_price'),
                'currency' => $option['currency'],
                'duration' => $option['duration_minutes'] ? $this->format->hours($option['duration_minutes'] / 60) : null,
                'languages' => array_map(fn (string $code): array => ['code' => $code, 'label' => Str::ucfirst(Locale::getDisplayLanguage($code, app()->getLocale()))], $option['languages']),
                'pickup' => (bool) $option['pickup_enabled'],
                'private' => (bool) $option['private_available'],
                'payLater' => (bool) $option['pay_later_enabled'],
                'cancellationHours' => $option['cancellation_hours'],
            ];
        }, $detail->experienceOptions);

        // Trips without separate packages still expose their actual base booking choice.
        if ($options === []) {
            $options = [[
                'id' => null,
                'title' => $detail->title,
                'priceType' => 'per_person',
                'adultPrice' => (string) $experience->price_per_person,
                'childPrice' => (string) $experience->price_per_person,
                'groupPrice' => null,
                'adultOriginal' => $experience->original_price,
                'childOriginal' => $experience->original_price,
                'groupOriginal' => null,
                'currency' => $currency,
                'duration' => $this->format->hours($experience->duration_hours),
                'languages' => $languages,
                'pickup' => (bool) $summary['pickup_enabled'],
                'private' => (bool) $summary['supports_private'],
                'payLater' => false,
                'cancellationHours' => null,
            ]];
        }

        return [
            'priceFrom' => $this->format->money((float) $experience->price_per_person, $currency, $locale),
            'rating' => $reviews->isNotEmpty() ? number_format((float) $reviews->avg('rating'), 1) : null,
            'calendar' => $this->calendar($summary['calendar'], $locale),
            'hasDates' => $summary['has_future_availability'],
            'supportsPrivate' => $summary['supports_private'],
            'pickup' => $summary['pickup_enabled'] ? __('experiences.detail.booking.pickup_place', ['city' => $experience->location_city]) : null,
            'hasChildPricing' => collect($detail->experienceOptions)
                ->contains(fn (array $option): bool => collect($option['prices'])->contains('type', 'child')),
            'cancellationHours' => collect($detail->experienceOptions)->pluck('cancellation_hours')->filter()->first(),
            'hostName' => $experience->publicHost() ? Str::before(trim($experience->publicHost()->name), ' ') : null,
            'maxGroup' => (int) $experience->max_group_size,
            'languages' => $languages,
            'options' => $options,
            'store' => [
                'checkoutUrl' => $summary['checkout_url'],
                'quoteUrl' => $summary['quote_url'],
                'csrfToken' => csrf_token(),
                'defaultLanguage' => $languages[0]['code'] ?? $locale,
                'experienceId' => $experience->id,
                'maxParticipants' => (int) $experience->max_group_size,
                'privateAvailable' => $summary['supports_private'],
                'pricePerPerson' => (float) $experience->price_per_person,
                'privatePrice' => $summary['private_price'] !== null ? (float) $summary['private_price'] : null,
                'currency' => $currency,
                'strings' => [
                    'checkoutFailed' => __('ui.experience_detail.booking_panel.checkout_failed'),
                    'quoteFailed' => __('ui.experience_detail.booking_panel.quote_failed'),
                    'selectDateAndParticipants' => __('ui.experience_detail.booking_panel.select_date_and_participants'),
                ],
            ],
            'card' => [
                'locale' => str_replace('_', '-', $locale),
                'currency' => $currency,
                'tripDays' => $this->isMultiDay($experience) ? (int) ceil((float) $experience->duration_hours / 24) : 1,
                'strings' => [
                    'adults' => $this->plural('experiences.detail.booking.adults_value'),
                    'children' => $this->plural('experiences.detail.booking.children_value'),
                    'travellers' => $this->plural('experiences.detail.booking.travellers_value'),
                    'chooseOption' => __('booking_flow.cta.choose_option'),
                    'chooseDate' => __('booking_flow.cta.choose_date'),
                    'hintDate' => __('booking_flow.hint.date'),
                    'hintOption' => __('booking_flow.hint.option'),
                    'breakdown' => __('experiences.detail.booking.breakdown'),
                    'flat' => __('experiences.detail.booking.breakdown_flat'),
                ],
            ],
        ];
    }

    /**
     * "You may also like": the existing recommendations (same city or category)
     * as marketplace compact cards.
     */
    private function related(ExperienceDetailData $detail, string $locale): array
    {
        $cards = $detail->recommendations->take(3);

        if ($cards->isEmpty()) {
            return [];
        }

        $extra = Experience::query()
            ->whereKey($cards->pluck('id'))
            ->with('host:id,name,is_active')
            ->get(['id', 'duration_hours', 'price_per_person', 'host_id'])
            ->keyBy('id');

        return $cards->map(function ($card) use ($extra, $locale): array {
            $experience = $extra->get($card->id);

            return [
                'href' => $card->showUrl,
                'title' => $card->title,
                'image' => $card->thumbnailUrl ?: (string) config('landing.images.card_fallback'),
                'meta' => $card->city.' · '.$this->format->duration((float) $experience?->duration_hours),
                'host' => $experience?->publicHost()?->name ?? $card->agencyName,
                'rating' => $card->reviewsCount > 0 ? $card->ratingAvg : null,
                'price' => $this->format->money((float) $experience?->price_per_person, $card->currency, $locale),
            ];
        })->values()->all();
    }

    /** Same availability calendar; the heading reads "October 2026". */
    private function calendar(array $calendar, string $locale): array
    {
        $firstDay = collect($calendar['weeks'])->flatten(1)->firstWhere('in_month', true);

        if ($firstDay) {
            $calendar['month_label'] = Str::ucfirst(CarbonImmutable::parse($firstDay['date'])->locale($locale)->translatedFormat('F Y'));
        }

        return $calendar;
    }

    /** @return list<array{code: string, label: string}> */
    private function languages(ExperienceDetailData $detail): array
    {
        return collect($detail->experienceOptions)
            ->pluck('languages')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->map(fn (string $code): array => [
                'code' => $code,
                'label' => Str::ucfirst(Locale::getDisplayLanguage($code, app()->getLocale())),
            ])
            ->all();
    }

    /** @return list<string> */
    private function lines(Experience $experience, string $field, string $locale): array
    {
        return array_values(array_filter(array_map(
            fn ($line): string => trim((string) $line),
            (array) $experience->getTranslation($field, $locale, true),
        )));
    }

    /** Singular / plural forms for the client (":count" stays a placeholder). */
    private function plural(string $key): array
    {
        return [
            'one' => trans_choice($key, 1, ['count' => ':count']),
            'other' => trans_choice($key, 2, ['count' => ':count']),
        ];
    }

    private function isMultiDay(Experience $experience): bool
    {
        return (float) $experience->duration_hours >= 24;
    }

    private function url(string $path): string
    {
        return Str::startsWith($path, ['http://', 'https://', '/']) ? $path : asset($path);
    }
}
