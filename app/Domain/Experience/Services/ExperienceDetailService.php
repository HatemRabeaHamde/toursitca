<?php

namespace App\Domain\Experience\Services;

use App\Domain\Experience\DTOs\ExperienceDetailData;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceOption;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

final class ExperienceDetailService
{
    public function __construct(
        private readonly ExperienceCardPresenter $experienceCardPresenter,
        private readonly ExperienceSearchNavService $experienceSearchNavService,
    ) {}

    public function get(Experience $experience): ExperienceDetailData
    {
        $experience->loadMissing([
            'agency',
            'media',
            'availabilities',
            'itineraryItems',
            'options.prices',
            'options.languages',
            'reviews.user',
            'reviews.booking',
        ]);

        return new ExperienceDetailData(
            experience: $experience,
            title: $experience->getTranslation('title', app()->getLocale(), false),
            description: $experience->getTranslation('description', app()->getLocale(), false),
            providerName: $experience->agency->name,
            gallery: $this->gallery($experience),
            badges: $this->badges($experience),
            features: $this->features($experience),
            trustItems: $this->trustItems($experience),
            reviewSummary: $this->reviewSummary($experience),
            agencySummary: $this->agencySummary($experience),
            bookingSummary: $this->bookingSummary($experience),
            experienceOptions: $this->experienceOptions($experience),
            itinerary: $this->itinerary($experience),
            providerExperiences: $this->providerExperiences($experience),
            categoryExperiences: $this->categoryExperiences($experience),
            recommendations: $this->recommendations($experience),
            reviews: $this->reviewCards($experience),
            bookingUrl: route('site.bookings.create', ['locale' => app()->getLocale(), 'experience' => $experience]),
            searchNav: $this->experienceSearchNavService->data($experience->location_city),
        );
    }

    private function gallery(Experience $experience): array
    {
        return $experience->media
            ->map(fn ($media): array => [
                'type' => $media->type,
                'url' => $media->publicUrl(),
            ])
            ->values()
            ->all();
    }

    private function badges(Experience $experience): array
    {
        return collect([
            $experience->is_top_rated ? __('ui.badges.top_rated') : null,
        ])
            ->filter()
            ->values()
            ->all();
    }

    private function features(Experience $experience): array
    {
        return collect([
            __('ui.fields.duration_hours') => trans_choice('ui.units.hours', (int) ceil((float) $experience->duration_hours), [
                'count' => rtrim(rtrim(number_format((float) $experience->duration_hours, 1), '0'), '.'),
            ]),
            __('ui.fields.max_group_size') => (string) $experience->max_group_size,
            __('ui.features.private_option_available') => $experience->supportsPrivateBooking() ? __('ui.labels.available') : null,
            __('ui.features.pickup_available') => $experience->pickup_enabled ? __('ui.labels.available') : null,
        ])
            ->filter()
            ->map(fn (string $value, string $label): array => ['label' => $label, 'value' => $value])
            ->values()
            ->all();
    }

    private function trustItems(Experience $experience): array
    {
        $durationLabel = trans_choice('ui.units.hours', (int) ceil((float) $experience->duration_hours), [
            'count' => rtrim(rtrim(number_format((float) $experience->duration_hours, 1), '0'), '.'),
        ]);

        return collect([
            [
                'icon' => '01',
                'title' => __('ui.experience_detail.key_specs.free_cancellation_short'),
                'body' => __('ui.experience_detail.trust.free_cancellation_body'),
            ],
            [
                'icon' => 'pay_later',
                'title' => __('ui.experience_detail.key_specs.pay_later_title'),
                'body' => __('ui.experience_detail.trust.pay_later_body'),
            ],
            [
                'icon' => '02',
                'title' => __('ui.fields.duration_hours').' '.$durationLabel,
                'body' => __('ui.experience_detail.trust.duration_body'),
            ],
            $experience->pickup_enabled ? [
                'icon' => '03',
                'title' => __('ui.experience_detail.key_specs.pickup_title'),
                'body' => __('ui.experience_detail.trust.pickup_body'),
            ] : null,
            $experience->supportsPrivateBooking() ? [
                'icon' => '04',
                'title' => __('ui.experience_detail.key_specs.private_title'),
                'body' => __('ui.experience_detail.trust.private_body'),
            ] : null,
        ])
            ->filter()
            ->values()
            ->all();
    }

    private function reviewSummary(Experience $experience): array
    {
        $count = $experience->reviews->count();
        $avg = $count > 0 ? $experience->reviews->avg('rating') : 0;

        return [
            'rating' => number_format((float) $avg, 1),
            'count' => $count,
            'has_reviews' => $count > 0,
        ];
    }

    private function agencySummary(Experience $experience): array
    {
        return [
            'name' => $experience->agency->name,
            'city' => $experience->agency->city,
            'is_verified' => $experience->agency->status === 'active',
        ];
    }

    private function bookingSummary(Experience $experience): array
    {
        $futureAvailabilities = $experience->availabilities
            ->filter(fn ($availability): bool => $availability->is_active && $availability->date->toDateString() >= now()->toDateString())
            ->sortBy(fn ($availability): string => $availability->date->toDateString().' '.$availability->time_slot)
            ->values();

        $nextAvailabilities = $futureAvailabilities
            ->filter(fn ($availability): bool => $availability->availableSeats() > 0)
            ->sortBy(fn ($availability): string => $availability->date->toDateString().' '.$availability->time_slot)
            ->take(3)
            ->map(fn ($availability): array => [
                'id' => $availability->id,
                'date' => $availability->date->toDateString(),
                'time' => substr($availability->time_slot, 0, 5),
                'available_seats' => $availability->availableSeats(),
            ])
            ->values()
            ->all();

        return [
            'price_from' => number_format((float) $experience->price_per_person, 2, '.', ''),
            'original_price' => $experience->original_price !== null ? number_format((float) $experience->original_price, 2, '.', '') : null,
            'private_price' => $experience->private_price !== null ? number_format((float) $experience->private_price, 2, '.', '') : null,
            'currency' => config('payment.currency', 'MAD'),
            'supports_private' => $experience->supportsPrivateBooking(),
            'pickup_enabled' => (bool) $experience->pickup_enabled,
            'next_availabilities' => $nextAvailabilities,
            'calendar' => $this->calendar($futureAvailabilities),
            'has_future_availability' => $futureAvailabilities->contains(fn ($availability): bool => $availability->availableSeats() > 0),
            'quote_url' => route('site.experiences.quote', ['locale' => app()->getLocale(), 'experience' => $experience->slug]),
            'checkout_url' => route('site.checkout.start', ['locale' => app()->getLocale()]),
        ];
    }

    private function calendar(Collection $availabilities): array
    {
        $selectedDate = $availabilities
            ->first(fn ($availability): bool => $availability->availableSeats() > 0)
            ?->date
            ?->toDateString();

        $month = CarbonImmutable::parse($selectedDate ?: now()->toDateString())->startOfMonth();
        $cursor = $month->startOfWeek(CarbonInterface::MONDAY);
        $end = $month->endOfMonth()->endOfWeek(CarbonInterface::SUNDAY);
        $availabilityByDate = $availabilities->groupBy(fn ($availability): string => $availability->date->toDateString());
        $weeks = [];

        while ($cursor->lte($end)) {
            $week = [];

            for ($day = 0; $day < 7; $day++) {
                $dateKey = $cursor->toDateString();
                $dayAvailabilities = $availabilityByDate->get($dateKey, collect());
                $availableSeats = (int) $dayAvailabilities->sum(fn ($availability): int => $availability->availableSeats());
                $maxSeats = (int) $dayAvailabilities->sum('max_seats');
                $availableDaySlots = $dayAvailabilities
                    ->filter(fn ($availability): bool => $availability->availableSeats() > 0)
                    ->sortBy('time_slot')
                    ->values();
                $availabilityIds = $availableDaySlots
                    ->mapWithKeys(fn ($availability): array => [
                        $availability->experience_option_id ? (string) $availability->experience_option_id : 'default' => $availability->id,
                    ])
                    ->all();

                if ($availableDaySlots->isNotEmpty()) {
                    $availabilityIds['any'] = $availableDaySlots->first()->id;
                }

                $week[] = [
                    'date' => $dateKey,
                    'number' => $cursor->day,
                    'in_month' => $cursor->month === $month->month,
                    'selected' => $selectedDate === $dateKey,
                    'status' => $this->calendarStatus($maxSeats, $availableSeats),
                    'availability_ids' => $availabilityIds,
                ];

                $cursor = $cursor->addDay();
            }

            $weeks[] = $week;
        }

        return [
            'month_label' => $month->translatedFormat('j F Y'),
            'weekdays' => __('ui.experience_detail.weekdays_short'),
            'weeks' => $weeks,
        ];
    }

    private function calendarStatus(int $maxSeats, int $availableSeats): ?string
    {
        if ($maxSeats === 0) {
            return null;
        }

        if ($availableSeats === 0) {
            return 'full';
        }

        if ($availableSeats <= 2) {
            return 'few';
        }

        return 'available';
    }

    private function experienceOptions(Experience $experience): array
    {
        $currency = config('payment.currency', 'MAD');

        return $experience->options
            ->filter(fn (ExperienceOption $option): bool => $option->status === 'active')
            ->map(function (ExperienceOption $option) use ($currency): array {
                $prices = $option->prices
                    ->map(fn ($price): array => [
                        'type' => $price->participant_type,
                        'price' => number_format((float) $price->price, 2, '.', ''),
                        'original_price' => $price->original_price !== null ? number_format((float) $price->original_price, 2, '.', '') : null,
                    ])
                    ->values()
                    ->all();

                $total = $option->prices->reduce(
                    fn (Money $carry, $price): Money => $carry->plus($price->price),
                    Money::zero($currency)
                );

                $originalTotal = $option->prices->every(fn ($price) => $price->original_price !== null)
                    ? $option->prices->reduce(
                        fn (Money $carry, $price): Money => $carry->plus($price->original_price),
                        Money::zero($currency)
                    )
                    : null;

                $discountPercent = null;

                if ($originalTotal !== null && $originalTotal->isPositive()) {
                    $discountPercent = (int) round((1 - ($total->getAmount()->toFloat() / $originalTotal->getAmount()->toFloat())) * 100);
                }

                return [
                    'id' => $option->id,
                    'title' => $option->getTranslation('title', app()->getLocale(), false),
                    'duration_minutes' => $option->duration_minutes,
                    'pickup_enabled' => $option->pickup_enabled,
                    'private_available' => $option->private_available,
                    'pay_later_enabled' => $option->pay_later_enabled,
                    'cancellation_hours' => $option->cancellation_hours,
                    'price_type' => $option->price_type,
                    'languages' => $option->languages
                        ->pluck('language_code')
                        ->unique()
                        ->values()
                        ->all(),
                    'prices' => $prices,
                    'total_price' => number_format($total->getAmount()->toFloat(), 2),
                    'original_total_price' => $originalTotal?->getAmount()->toFloat() !== null
                        ? number_format($originalTotal->getAmount()->toFloat(), 2)
                        : null,
                    'discount_percent' => $discountPercent,
                    'currency' => $currency,
                ];
            })
            ->values()
            ->all();
    }

    private function itinerary(Experience $experience): array
    {
        $items = $experience->itineraryItems
            ->map(function ($item): array {
                $duration = $item->duration_minutes
                    ? trans_choice('ui.units.minutes', $item->duration_minutes, ['count' => $item->duration_minutes])
                    : null;

                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'title' => $item->getTranslation('title', app()->getLocale(), false),
                    'description' => $item->getTranslation('description', app()->getLocale(), false),
                    'duration' => $duration,
                    'location_name' => $item->getTranslation('location_name', app()->getLocale(), false),
                    'lat' => $item->location_lat !== null ? (float) $item->location_lat : null,
                    'lng' => $item->location_lng !== null ? (float) $item->location_lng : null,
                    'is_main_stop' => (bool) $item->is_main_stop,
                    'icon' => $item->icon ?: $this->itineraryIcon($item->type),
                ];
            })
            ->filter(fn (array $item): bool => filled($item['title']) || filled($item['location_name']) || filled($item['description']))
            ->values();

        if ($items->isEmpty() && app()->environment('local')) {
            $items = collect($this->demoItinerary($experience));
        }

        $coordinates = $items
            ->filter(fn (array $item): bool => $item['lat'] !== null && $item['lng'] !== null)
            ->map(fn (array $item): array => ['lat' => $item['lat'], 'lng' => $item['lng']])
            ->values();

        if ($coordinates->isEmpty() && $experience->location_lat !== null && $experience->location_lng !== null) {
            $coordinates = collect([[
                'lat' => (float) $experience->location_lat,
                'lng' => (float) $experience->location_lng,
            ]]);
        }

        return [
            'items' => $items->all(),
            'has_items' => $items->isNotEmpty(),
            'map' => $this->itineraryMap($coordinates),
            'start_count' => $items->where('type', 'start')->count(),
            'dropoff_count' => $items->where('type', 'dropoff')->count(),
        ];
    }

    private function demoItinerary(Experience $experience): array
    {
        $baseLat = $experience->location_lat !== null ? (float) $experience->location_lat : 31.6295;
        $baseLng = $experience->location_lng !== null ? (float) $experience->location_lng : -7.9811;
        $city = $experience->location_city ?: 'Marrakesh';

        return [
            [
                'id' => 'demo-start',
                'type' => 'start',
                'title' => __('ui.experience_detail.itinerary.demo.start_title'),
                'description' => __('ui.experience_detail.itinerary.demo.start_description'),
                'duration' => trans_choice('ui.units.minutes', 15, ['count' => 15]),
                'location_name' => $city,
                'lat' => $baseLat,
                'lng' => $baseLng,
                'is_main_stop' => false,
                'icon' => 'pin',
            ],
            [
                'id' => 'demo-route',
                'type' => 'transport',
                'title' => __('ui.experience_detail.itinerary.demo.transfer_title'),
                'description' => __('ui.experience_detail.itinerary.demo.transfer_description'),
                'duration' => trans_choice('ui.units.minutes', 35, ['count' => 35]),
                'location_name' => __('ui.experience_detail.itinerary.demo.scenic_route'),
                'lat' => $baseLat + 0.035,
                'lng' => $baseLng + 0.035,
                'is_main_stop' => false,
                'icon' => 'car',
            ],
            [
                'id' => 'demo-main',
                'type' => 'stop',
                'title' => __('ui.experience_detail.itinerary.demo.main_stop_title'),
                'description' => __('ui.experience_detail.itinerary.demo.main_stop_description'),
                'duration' => trans_choice('ui.units.minutes', 90, ['count' => 90]),
                'location_name' => __('ui.experience_detail.itinerary.demo.main_stop_place'),
                'lat' => $baseLat + 0.07,
                'lng' => $baseLng + 0.015,
                'is_main_stop' => true,
                'icon' => 'photo',
            ],
            [
                'id' => 'demo-finish',
                'type' => 'dropoff',
                'title' => __('ui.experience_detail.itinerary.demo.dropoff_title'),
                'description' => __('ui.experience_detail.itinerary.demo.dropoff_description'),
                'duration' => null,
                'location_name' => $city,
                'lat' => $baseLat + 0.01,
                'lng' => $baseLng - 0.025,
                'is_main_stop' => false,
                'icon' => 'finish',
            ],
        ];
    }

    private function itineraryIcon(string $type): string
    {
        return match ($type) {
            'start' => 'pin',
            'transport' => 'car',
            'activity' => 'walk',
            'dropoff' => 'finish',
            default => 'pin',
        };
    }

    private function itineraryMap(Collection $coordinates): array
    {
        if ($coordinates->isEmpty()) {
            return ['has_map' => false];
        }

        $minLat = (float) $coordinates->min('lat');
        $maxLat = (float) $coordinates->max('lat');
        $minLng = (float) $coordinates->min('lng');
        $maxLng = (float) $coordinates->max('lng');
        $padding = 0.05;

        if ($minLat === $maxLat) {
            $minLat -= $padding;
            $maxLat += $padding;
        }

        if ($minLng === $maxLng) {
            $minLng -= $padding;
            $maxLng += $padding;
        }

        return [
            'has_map' => true,
            'center_lat' => round(($minLat + $maxLat) / 2, 7),
            'center_lng' => round(($minLng + $maxLng) / 2, 7),
            'bbox' => implode(',', [
                round($minLng - $padding, 7),
                round($minLat - $padding, 7),
                round($maxLng + $padding, 7),
                round($maxLat + $padding, 7),
            ]),
        ];
    }

    private function reviewCards(Experience $experience): Collection
    {
        return $experience->reviews
            ->sortByDesc('created_at')
            ->take(6)
            ->map(function ($review): array {
                $name = $review->user?->name ?? __('ui.labels.anonymous');
                $initials = mb_strtoupper(mb_substr($name, 0, 1));
                $colors = ['#c0392b', '#2980b9', '#27ae60', '#8e44ad', '#e67e22', '#16a085'];
                $color = $colors[crc32($name) % count($colors)];

                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'body' => $review->body,
                    'name' => $name,
                    'initials' => $initials,
                    'color' => $color,
                    'date' => $review->created_at->translatedFormat('j F Y'),
                    'is_verified' => $review->booking !== null,
                    'agency_reply' => $review->agency_reply,
                    'replied_at' => $review->replied_at?->translatedFormat('j F Y'),
                ];
            })
            ->values();
    }

    private function recommendations(Experience $experience): Collection
    {
        return Experience::query()
            ->with(ExperienceCardPresenter::eagerLoads())
            ->published()
            ->whereKeyNot($experience->id)
            ->where(function ($query) use ($experience): void {
                $query
                    ->where('location_city', $experience->location_city)
                    ->orWhere('category', $experience->category);
            })
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->limit(4)
            ->get()
            ->map(fn (Experience $experience) => $this->experienceCardPresenter->present($experience));
    }

    private function providerExperiences(Experience $experience): Collection
    {
        return Experience::query()
            ->with(ExperienceCardPresenter::eagerLoads())
            ->published()
            ->whereKeyNot($experience->id)
            ->where('agency_id', $experience->agency_id)
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->latest()
            ->limit(4)
            ->get()
            ->map(fn (Experience $experience) => $this->experienceCardPresenter->present($experience));
    }

    private function categoryExperiences(Experience $experience): Collection
    {
        $matches = Experience::query()
            ->with(ExperienceCardPresenter::eagerLoads())
            ->published()
            ->whereKeyNot($experience->id)
            ->where('category', $experience->category)
            ->where('agency_id', '!=', $experience->agency_id)
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->latest()
            ->limit(4)
            ->get()
            ->map(fn (Experience $experience) => $this->experienceCardPresenter->present($experience));

        if ($matches->isNotEmpty() || ! app()->environment('local')) {
            return $matches;
        }

        return Experience::query()
            ->with(ExperienceCardPresenter::eagerLoads())
            ->published()
            ->whereKeyNot($experience->id)
            ->where('agency_id', '!=', $experience->agency_id)
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->latest()
            ->limit(4)
            ->get()
            ->map(fn (Experience $experience) => $this->experienceCardPresenter->present($experience));
    }
}
