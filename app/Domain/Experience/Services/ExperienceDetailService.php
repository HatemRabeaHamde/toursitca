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
    ) {}

    public function get(Experience $experience): ExperienceDetailData
    {
        $experience->loadMissing(['agency', 'media', 'availabilities', 'options.prices', 'options.languages']);

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
            recommendations: $this->recommendations($experience),
            bookingUrl: route('site.bookings.create', ['locale' => app()->getLocale(), 'experience' => $experience]),
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
        return collect([
            [
                'icon' => '01',
                'title' => __('ui.experience_detail.trust.manual_confirmation_title'),
                'body' => __('ui.experience_detail.trust.manual_confirmation_body'),
            ],
            [
                'icon' => '02',
                'title' => __('ui.fields.duration_hours'),
                'body' => trans_choice('ui.units.hours', (int) ceil((float) $experience->duration_hours), [
                    'count' => rtrim(rtrim(number_format((float) $experience->duration_hours, 1), '0'), '.'),
                ]),
            ],
            $experience->pickup_enabled ? [
                'icon' => '03',
                'title' => __('ui.features.pickup_available'),
                'body' => __('ui.experience_detail.trust.pickup_body'),
            ] : null,
            $experience->supportsPrivateBooking() ? [
                'icon' => '04',
                'title' => __('ui.features.private_option_available'),
                'body' => __('ui.experience_detail.trust.private_body'),
            ] : null,
        ])
            ->filter()
            ->values()
            ->all();
    }

    private function reviewSummary(Experience $experience): array
    {
        return [
            'rating' => number_format((float) $experience->rating_avg, 1),
            'count' => (int) $experience->reviews_count,
            'has_reviews' => (int) $experience->reviews_count > 0,
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
            'price_from' => number_format((float) $experience->price_per_person, 2),
            'original_price' => $experience->original_price !== null ? number_format((float) $experience->original_price, 2) : null,
            'currency' => config('payment.currency', 'MAD'),
            'supports_private' => $experience->supportsPrivateBooking(),
            'pickup_enabled' => (bool) $experience->pickup_enabled,
            'next_availabilities' => $nextAvailabilities,
            'calendar' => $this->calendar($futureAvailabilities),
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

                $week[] = [
                    'date' => $dateKey,
                    'number' => $cursor->day,
                    'in_month' => $cursor->month === $month->month,
                    'selected' => $selectedDate === $dateKey,
                    'status' => $this->calendarStatus($maxSeats, $availableSeats),
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
                        'price' => number_format((float) $price->price, 2),
                        'original_price' => $price->original_price !== null ? number_format((float) $price->original_price, 2) : null,
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
}
