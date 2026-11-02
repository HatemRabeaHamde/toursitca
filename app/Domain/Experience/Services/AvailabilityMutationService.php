<?php

namespace App\Domain\Experience\Services;

use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Experience\Models\Availability;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

final class AvailabilityMutationService
{
    public function __construct(private readonly DatabaseManager $database) {}

    public function update(Availability $availability, array $payload): Availability
    {
        return $this->database->transaction(function () use ($availability, $payload): Availability {
            $locked = Availability::query()->lockForUpdate()->findOrFail($availability->id);
            if ($locked->booked_seats + $locked->held_seats > 0) {
                $moved = (int) $payload['experience_id'] !== $locked->experience_id
                    || ($payload['experience_option_id'] ?? null) !== $locked->experience_option_id
                    || $payload['date'] !== $locked->date->toDateString()
                    || substr($payload['time_slot'], 0, 5) !== substr($locked->time_slot, 0, 5);
                if ($moved || (int) $payload['max_seats'] < $locked->max_seats) {
                    throw ValidationException::withMessages(['availability' => __('booking_flow.validation.slot_locked')]);
                }
            }
            if ($locked->held_seats > 0 && (int) $payload['max_seats'] !== $locked->max_seats
                && CheckoutSession::query()->where('availability_id', $locked->id)->where('status', 'active')->where('booking_type', 'private')->exists()) {
                throw ValidationException::withMessages(['availability' => __('booking_flow.validation.slot_locked')]);
            }
            if ($locked->bookings()->whereIn('status', ['pending', 'confirmed'])->where('booking_type', 'private')->exists()) {
                $payload['booked_seats'] = (int) $payload['max_seats'];
            }
            $locked->fill($payload)->save();

            return $locked->refresh();
        });
    }

    public function delete(Availability $availability): void
    {
        $this->database->transaction(function () use ($availability): void {
            $locked = Availability::query()->lockForUpdate()->findOrFail($availability->id);
            if ($locked->held_seats > 0 || $locked->bookings()->exists()) {
                throw ValidationException::withMessages(['availability' => __('booking_flow.validation.slot_locked')]);
            }
            $locked->delete();
        });
    }
}
