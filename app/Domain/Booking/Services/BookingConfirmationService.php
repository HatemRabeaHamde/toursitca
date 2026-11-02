<?php

namespace App\Domain\Booking\Services;

use App\Domain\Booking\Events\BookingConfirmed;
use App\Domain\Booking\Models\Booking;
use App\Domain\Payout\Models\Payout;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;

final class BookingConfirmationService
{
    public function __construct(
        private readonly DatabaseManager $database,
        private readonly Dispatcher $events,
    ) {}

    /** Called within the booking transaction, after seats have been reserved. */
    public function confirm(Booking $booking): Booking
    {
        $booking->forceFill(['status' => 'confirmed', 'reserved_until' => null])->save();
        $booking->loadMissing('experience.agency');

        if (! $booking->experience->agency->is_platform) {
            Payout::query()->firstOrCreate(['booking_id' => $booking->id], [
                'agency_id' => $booking->experience->agency_id,
                'amount' => $booking->agency_amount,
                'status' => 'pending',
            ]);
        }

        $this->database->connection()->afterCommit(fn () => $this->events->dispatch(new BookingConfirmed($booking)));

        return $booking;
    }
}
