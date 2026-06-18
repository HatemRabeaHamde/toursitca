<?php

namespace App\Domain\Booking\Services;

use App\Domain\Experience\Models\Availability;
use InvalidArgumentException;

final class SeatReservationService
{
    public function reserve(Availability $availability, string $bookingType, int $participantsCount): Availability
    {
        $lockedAvailability = Availability::query()
            ->whereKey($availability->id)
            ->lockForUpdate()
            ->firstOrFail();

        if (! $lockedAvailability->is_active) {
            throw new InvalidArgumentException('Selected slot is not active.');
        }

        if ($bookingType === 'private') {
            if ($lockedAvailability->booked_seats > 0) {
                throw new InvalidArgumentException('Selected slot is not available for private booking.');
            }

            $lockedAvailability->forceFill([
                'booked_seats' => $lockedAvailability->max_seats,
            ])->save();

            return $lockedAvailability;
        }

        if ($bookingType !== 'group') {
            throw new InvalidArgumentException('Unsupported booking type.');
        }

        if ($participantsCount > $lockedAvailability->availableSeats()) {
            throw new InvalidArgumentException('Not enough available seats for this slot.');
        }

        $lockedAvailability->increment('booked_seats', $participantsCount);

        return $lockedAvailability->refresh();
    }

    public function release(Availability $availability, string $bookingType, int $participantsCount): void
    {
        $lockedAvailability = Availability::query()
            ->whereKey($availability->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($bookingType === 'private') {
            $lockedAvailability->forceFill(['booked_seats' => 0])->save();

            return;
        }

        $lockedAvailability->forceFill([
            'booked_seats' => max(0, $lockedAvailability->booked_seats - $participantsCount),
        ])->save();
    }
}
