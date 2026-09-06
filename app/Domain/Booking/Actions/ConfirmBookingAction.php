<?php

namespace App\Domain\Booking\Actions;

use App\Domain\Booking\Events\BookingConfirmed;
use App\Domain\Booking\Models\Booking;
use App\Domain\Payout\Actions\CreatePayoutFromBookingAction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class ConfirmBookingAction
{
    public function __construct(
        private readonly CreatePayoutFromBookingAction $createPayoutFromBookingAction,
    ) {}

    public function execute(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking): Booking {
            $lockedBooking = Booking::query()
                ->whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedBooking->status !== 'pending') {
                throw new InvalidArgumentException('Only pending bookings can be confirmed.');
            }

            $lockedBooking->forceFill(['status' => 'confirmed'])->save();
            $this->createPayoutFromBookingAction->execute($lockedBooking);
            BookingConfirmed::dispatch($lockedBooking);

            return $lockedBooking->refresh();
        });
    }
}
