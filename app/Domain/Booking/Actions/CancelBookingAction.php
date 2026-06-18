<?php

namespace App\Domain\Booking\Actions;

use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Services\SeatReservationService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class CancelBookingAction
{
    public function __construct(
        private readonly SeatReservationService $seatReservationService,
    ) {}

    public function execute(Booking $booking, ?string $reason = null): Booking
    {
        return DB::transaction(function () use ($booking, $reason): Booking {
            $lockedBooking = Booking::query()
                ->with('availability')
                ->whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedBooking->status === 'cancelled') {
                throw new InvalidArgumentException('Booking is already cancelled.');
            }

            if ($lockedBooking->status === 'completed') {
                throw new InvalidArgumentException('Completed bookings cannot be cancelled.');
            }

            $this->seatReservationService->release(
                $lockedBooking->availability,
                $lockedBooking->booking_type,
                $lockedBooking->participants_count,
            );

            $lockedBooking->forceFill([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ])->save();

            return $lockedBooking->refresh();
        });
    }
}
