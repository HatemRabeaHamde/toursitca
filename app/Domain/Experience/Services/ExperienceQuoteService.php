<?php

namespace App\Domain\Experience\Services;

use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceOption;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

final class ExperienceQuoteService
{
    /**
     * @param  array<string, int>  $participants
     * @return array<string, mixed>
     */
    public function quote(
        Experience $experience,
        string $bookingType,
        array $participants,
        ?int $optionId = null,
        ?int $availabilityId = null,
        ?string $languageCode = null,
        ?array $childAges = null,
    ): array {
        if (! $experience->isPubliclyBookable()) {
            throw ValidationException::withMessages(['experience_id' => __('ui.messages.booking_slot_unavailable')]);
        }

        $experience->loadMissing(['agency', 'options.prices', 'options.languages']);

        $option = $this->resolveOption($experience, $optionId);
        $availability = $this->resolveAvailability($experience, $option, $availabilityId);
        if (array_diff(array_keys($participants), ['adult', 'child']) !== [] || ($participants['adult'] ?? 0) < 1
            || collect($participants)->contains(fn ($count): bool => ! is_int($count) || $count < 0)) {
            throw ValidationException::withMessages(['participants' => __('ui.messages.participants_required')]);
        }
        if ($bookingType === 'private' && (! ($option?->private_available ?? $experience->supportsPrivateBooking())
            || ($experience->private_price === null && $option?->price_type !== 'per_group'))) {
            throw ValidationException::withMessages(['booking_type' => __('ui.messages.booking_slot_unavailable')]);
        }
        if ($childAges !== null && count($childAges) !== ($participants['child'] ?? 0)) {
            throw ValidationException::withMessages(['child_ages' => __('booking_flow.validation.child_ages')]);
        }
        if ($childAges !== null && $option) {
            $band = $option->prices->firstWhere('participant_type', 'child');
            foreach ($childAges as $age) {
                if ($band && ($age < ($band->min_age ?? 0) || $age > ($band->max_age ?? 17))) {
                    throw ValidationException::withMessages(['child_ages' => __('booking_flow.validation.child_ages')]);
                }
            }
        }
        $totalParticipants = array_sum($participants);

        if ($totalParticipants < 1) {
            throw ValidationException::withMessages([
                'participants' => __('ui.messages.participants_required'),
            ]);
        }

        if ($totalParticipants > $experience->max_group_size) {
            throw ValidationException::withMessages([
                'participants' => __('ui.messages.participants_exceed_experience'),
            ]);
        }

        if ($availability) {
            $this->validateAvailability($availability, $bookingType, $totalParticipants);
        }

        if ($languageCode && $option && $option->languages->isNotEmpty()) {
            $supportsLanguage = $option->languages->contains('language_code', $languageCode);
            if (! $supportsLanguage) {
                throw ValidationException::withMessages([
                    'language' => __('ui.messages.language_unavailable'),
                ]);
            }
        }

        $price = $this->calculatePrice($experience, $option, $bookingType, $participants, $availability);

        return [
            'experience_id' => $experience->id,
            'booking_snapshot' => [
                'title' => $experience->getTranslations('title'),
                'option' => $option?->getTranslations('title'),
                'host_name' => $experience->publicHost()?->name,
                'host_id' => $experience->publicHost()?->id,
                'host_avatar' => $experience->publicHost()?->avatar_url ?: $experience->publicHost()?->photo_url,
                'host_verified' => (bool) $experience->publicHost()?->is_verified,
                'host_licence' => $experience->publicHost()?->verifiedLicence(),
                'location_city' => $experience->location_city,
                'agency_name' => $experience->agency->name,
                'meeting_point' => $experience->meeting_point,
                'duration_hours' => (string) $experience->duration_hours,
                'date' => $availability?->date->toDateString(),
                'time' => $availability ? substr($availability->time_slot, 0, 5) : null,
                'timezone' => config('booking.timezone'),
                'cancellation_hours' => $option?->cancellation_hours,
                'price_type' => $option?->price_type ?? 'per_person',
                'published_version' => $experience->published_version,
            ],
            'option_id' => $option?->id,
            'availability_id' => $availability?->id,
            'booking_type' => $bookingType,
            'participants' => $participants,
            'participants_count' => $totalParticipants,
            'charged_seats' => $bookingType === 'private'
                ? ($availability?->max_seats ?? $experience->max_group_size)
                : $totalParticipants,
            'language' => $languageCode,
            'currency' => $price['currency'],
            'unit_price' => $price['unit_price'],
            'subtotal' => $price['subtotal'],
            'original_subtotal' => $price['original_subtotal'],
            'discount_amount' => $price['discount_amount'],
            'total' => $price['total'],
            'available_seats' => $availability?->availableSeats(),
        ];
    }

    private function resolveOption(Experience $experience, ?int $optionId): ?ExperienceOption
    {
        if ($optionId === null) {
            $active = $experience->options->where('status', 'active');
            if ($active->count() > 1) {
                throw ValidationException::withMessages(['option_id' => __('booking_flow.cta.choose_option')]);
            }

            return $active->first();
        }

        $option = $experience->options->firstWhere('id', $optionId);

        if (! $option || $option->status !== 'active') {
            throw ValidationException::withMessages([
                'option_id' => __('ui.messages.invalid_experience_option'),
            ]);
        }

        return $option;
    }

    private function resolveAvailability(Experience $experience, ?ExperienceOption $option, ?int $availabilityId): ?Availability
    {
        if ($availabilityId === null) {
            return null;
        }

        $availability = Availability::query()->find($availabilityId);

        if (! $availability || $availability->experience_id !== $experience->id) {
            throw ValidationException::withMessages([
                'availability_id' => __('ui.messages.invalid_booking_slot'),
            ]);
        }

        if ($option && $availability->experience_option_id !== null && $availability->experience_option_id !== $option->id) {
            throw ValidationException::withMessages([
                'availability_id' => __('ui.messages.invalid_booking_slot'),
            ]);
        }

        return $availability;
    }

    private function validateAvailability(Availability $availability, string $bookingType, int $participantsCount): void
    {
        if (! $availability->is_active || CarbonImmutable::parse($availability->date->toDateString().' '.$availability->time_slot, config('app.timezone'))->isPast()) {
            throw ValidationException::withMessages([
                'availability_id' => __('ui.messages.booking_slot_unavailable'),
            ]);
        }

        if ($bookingType === 'private') {
            if ($availability->booked_seats > 0 || $availability->held_seats > 0) {
                throw ValidationException::withMessages([
                    'availability_id' => __('ui.messages.booking_slot_unavailable'),
                ]);
            }

            return;
        }

        if ($participantsCount > $availability->availableSeats()) {
            throw ValidationException::withMessages([
                'participants' => __('ui.messages.not_enough_seats'),
            ]);
        }
    }

    /**
     * @param  array<string, int>  $participants
     * @return array{currency: string, unit_price: string, subtotal: string, original_subtotal: ?string, discount_amount: string, total: string}
     */
    private function calculatePrice(Experience $experience, ?ExperienceOption $option, string $bookingType, array $participants, ?Availability $availability): array
    {
        $currency = config('payment.currency', 'MAD');
        $baseUnit = BigDecimal::of((string) $experience->price_per_person);
        $originalUnit = $experience->original_price !== null ? BigDecimal::of((string) $experience->original_price) : null;

        if ($option && $option->prices->isNotEmpty()) {
            $currency = $option->prices->first()->currency;
            $adultPrice = $option->prices->firstWhere('participant_type', 'adult') ?? $option->prices->first();
            $baseUnit = BigDecimal::of((string) $adultPrice->price);
        }

        if ($option?->price_type === 'per_group') {
            $groupPrice = $option->prices->firstWhere('participant_type', 'group') ?? $option->prices->firstWhere('participant_type', 'adult');
            if (! $groupPrice) {
                throw ValidationException::withMessages(['option_id' => __('ui.messages.invalid_experience_option')]);
            }
            $subtotal = BigDecimal::of((string) $groupPrice->price);
            $originalSubtotal = $groupPrice->original_price !== null ? BigDecimal::of((string) $groupPrice->original_price) : null;
            $unitPrice = $subtotal;
        } elseif ($bookingType === 'private') {
            $chargedSeats = $availability?->max_seats ?? $experience->max_group_size;
            $subtotal = $experience->private_price !== null
                ? BigDecimal::of((string) $experience->private_price)
                : $baseUnit->multipliedBy($chargedSeats);
            $unitPrice = $subtotal;
            $originalSubtotal = $originalUnit?->multipliedBy($chargedSeats);
        } else {
            $subtotal = BigDecimal::zero();
            $originalSubtotal = BigDecimal::zero();

            foreach ($participants as $type => $count) {
                $price = $this->participantPrice($experience, $option, $type);
                $subtotal = $subtotal->plus($price['price']->multipliedBy($count));

                if ($price['original_price']) {
                    $originalSubtotal = $originalSubtotal->plus($price['original_price']->multipliedBy($count));
                }
            }

            $unitPrice = $baseUnit;
            $originalSubtotal = $originalSubtotal->isEqualTo(BigDecimal::zero()) ? null : $originalSubtotal;
        }

        $discountAmount = $originalSubtotal
            ? $originalSubtotal->minus($subtotal)
            : BigDecimal::zero();

        return [
            'currency' => $currency,
            'unit_price' => (string) $unitPrice->toScale(2, RoundingMode::HALF_UP),
            'subtotal' => (string) $subtotal->toScale(2, RoundingMode::HALF_UP),
            'original_subtotal' => $originalSubtotal ? (string) $originalSubtotal->toScale(2, RoundingMode::HALF_UP) : null,
            'discount_amount' => (string) $discountAmount->toScale(2, RoundingMode::HALF_UP),
            'total' => (string) $subtotal->toScale(2, RoundingMode::HALF_UP),
        ];
    }

    /**
     * @return array{price: BigDecimal, original_price: ?BigDecimal}
     */
    private function participantPrice(Experience $experience, ?ExperienceOption $option, string $type): array
    {
        $optionPrice = $option?->prices->firstWhere('participant_type', $type)
            ?? $option?->prices->firstWhere('participant_type', 'adult');

        if ($optionPrice) {
            return [
                'price' => BigDecimal::of((string) $optionPrice->price),
                'original_price' => $optionPrice->original_price !== null ? BigDecimal::of((string) $optionPrice->original_price) : null,
            ];
        }

        return [
            'price' => BigDecimal::of((string) $experience->price_per_person),
            'original_price' => $experience->original_price !== null ? BigDecimal::of((string) $experience->original_price) : null,
        ];
    }
}
