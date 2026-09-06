<?php

namespace App\Domain\Checkout\Actions;

use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceQuoteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CreateCheckoutSessionAction
{
    public function __construct(
        private readonly ExperienceQuoteService $quoteService,
    ) {}

    /**
     * @param  array<string, int>  $participants
     * @param  list<int>|null  $childAges
     */
    public function execute(
        Experience $experience,
        string $bookingType,
        array $participants,
        int $availabilityId,
        ?int $optionId,
        ?string $languageCode,
        ?int $userId,
        ?string $sessionId,
        string $locale,
        string $paymentMethod = 'manual',
        ?array $childAges = null,
    ): CheckoutSession {
        return DB::transaction(function () use (
            $experience,
            $bookingType,
            $participants,
            $availabilityId,
            $optionId,
            $languageCode,
            $userId,
            $sessionId,
            $locale,
            $paymentMethod,
            $childAges,
        ): CheckoutSession {
            $availability = Availability::query()
                ->whereKey($availabilityId)
                ->lockForUpdate()
                ->firstOrFail();

            $quote = $this->quoteService->quote(
                experience: $experience,
                bookingType: $bookingType,
                participants: $participants,
                optionId: $optionId,
                availabilityId: $availability->id,
                languageCode: $languageCode,
            );

            $this->holdSeats($availability, $quote['charged_seats'], $bookingType);

            return CheckoutSession::query()->create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $userId,
                'session_id' => $sessionId,
                'experience_id' => $experience->id,
                'experience_option_id' => $quote['option_id'],
                'availability_id' => $availability->id,
                'locale' => $locale,
                'currency' => $quote['currency'],
                'booking_type' => $bookingType,
                'participants' => $participants,
                'child_ages' => $childAges,
                'participants_count' => $quote['participants_count'],
                'charged_seats' => $quote['charged_seats'],
                'tour_language' => $languageCode,
                'price_snapshot' => $quote,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'pay_later' ? 'not_required' : 'pending',
                'status' => 'active',
                'reserved_until' => now()->addMinutes((int) config('booking.checkout_hold_minutes', 30)),
            ]);
        });
    }

    private function holdSeats(Availability $availability, int $chargedSeats, string $bookingType): void
    {
        if (! $availability->is_active || $availability->date->lt(today())) {
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

            $availability->forceFill(['held_seats' => $availability->max_seats])->save();

            return;
        }

        if ($chargedSeats > $availability->availableSeats()) {
            throw ValidationException::withMessages([
                'participants' => __('ui.messages.not_enough_seats'),
            ]);
        }

        $availability->increment('held_seats', $chargedSeats);
    }
}
