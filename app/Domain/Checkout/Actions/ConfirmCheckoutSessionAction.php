<?php

namespace App\Domain\Checkout\Actions;

use App\Domain\Booking\Models\Booking;
use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Experience\Models\Availability;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ConfirmCheckoutSessionAction
{
    public function execute(CheckoutSession $checkoutSession): Booking
    {
        if (! $checkoutSession->isActive()) {
            throw ValidationException::withMessages([
                'checkout_session' => __('ui.messages.checkout_session_expired'),
            ]);
        }

        if (blank($checkoutSession->contact_first_name) || blank($checkoutSession->contact_last_name) || blank($checkoutSession->contact_email)) {
            throw ValidationException::withMessages([
                'contact' => __('ui.messages.checkout_contact_required'),
            ]);
        }

        if (blank($checkoutSession->user_id)) {
            throw ValidationException::withMessages([
                'auth' => __('ui.messages.checkout_auth_required'),
            ]);
        }

        return DB::transaction(function () use ($checkoutSession): Booking {
            $lockedSession = CheckoutSession::query()
                ->whereKey($checkoutSession->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedSession->isActive()) {
                throw ValidationException::withMessages([
                    'checkout_session' => __('ui.messages.checkout_session_expired'),
                ]);
            }

            $availability = Availability::query()
                ->whereKey($lockedSession->availability_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($availability->held_seats < $lockedSession->charged_seats) {
                throw ValidationException::withMessages([
                    'availability_id' => __('ui.messages.booking_slot_unavailable'),
                ]);
            }

            $availability->forceFill([
                'held_seats' => max(0, $availability->held_seats - $lockedSession->charged_seats),
                'booked_seats' => min($availability->max_seats, $availability->booked_seats + $lockedSession->charged_seats),
            ])->save();

            $lockedSession->loadMissing('experience.agency');
            $price = $this->priceSnapshotWithCommission($lockedSession);

            $booking = Booking::query()->create([
                'user_id' => $lockedSession->user_id,
                'checkout_session_id' => $lockedSession->id,
                'experience_id' => $lockedSession->experience_id,
                'experience_option_id' => $lockedSession->experience_option_id,
                'availability_id' => $lockedSession->availability_id,
                'booking_type' => $lockedSession->booking_type,
                'participants_count' => $lockedSession->participants_count,
                'participants' => $lockedSession->participants,
                'child_ages' => $lockedSession->child_ages,
                'charged_seats' => $lockedSession->charged_seats,
                'tour_language' => $lockedSession->tour_language,
                'pickup_status' => $lockedSession->pickup_status,
                'pickup_address' => $lockedSession->pickup_address,
                'pickup_lat' => $lockedSession->pickup_lat,
                'pickup_lng' => $lockedSession->pickup_lng,
                'unit_price' => $price['unit_price'],
                'total_price' => $price['total_price'],
                'commission_rate' => $price['commission_rate'],
                'commission_amount' => $price['commission_amount'],
                'agency_amount' => $price['agency_amount'],
                'price_snapshot' => $lockedSession->price_snapshot,
                'payment_method' => $lockedSession->payment_method,
                'payment_status' => $lockedSession->payment_status,
                'status' => 'pending',
                'guest_name' => trim($lockedSession->contact_first_name.' '.$lockedSession->contact_last_name),
                'guest_email' => $lockedSession->contact_email,
                'guest_phone' => $lockedSession->contact_phone,
                'contact_country' => $lockedSession->contact_country,
                'special_notes' => $lockedSession->special_requests,
                'reserved_until' => $lockedSession->reserved_until,
            ]);

            $lockedSession->forceFill(['status' => 'completed'])->save();

            return $booking;
        });
    }

    /**
     * @return array{unit_price: string, total_price: string, commission_rate: string, commission_amount: string, agency_amount: string}
     */
    private function priceSnapshotWithCommission(CheckoutSession $checkoutSession): array
    {
        $total = BigDecimal::of((string) $checkoutSession->price_snapshot['total']);
        $unit = BigDecimal::of((string) $checkoutSession->price_snapshot['unit_price']);
        $commissionRate = BigDecimal::of((string) $checkoutSession->experience->agency->commission_rate);
        $commissionAmount = $total
            ->multipliedBy($commissionRate)
            ->dividedBy(100, 2, RoundingMode::HALF_UP);
        $agencyAmount = $total->minus($commissionAmount);

        return [
            'unit_price' => (string) $unit->toScale(2, RoundingMode::HALF_UP),
            'total_price' => (string) $total->toScale(2, RoundingMode::HALF_UP),
            'commission_rate' => (string) $commissionRate->toScale(2, RoundingMode::HALF_UP),
            'commission_amount' => (string) $commissionAmount->toScale(2, RoundingMode::HALF_UP),
            'agency_amount' => (string) $agencyAmount->toScale(2, RoundingMode::HALF_UP),
        ];
    }
}
