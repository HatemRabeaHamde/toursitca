<?php

namespace App\Domain\Payout\Actions;

use App\Domain\Booking\Models\Booking;
use App\Domain\Payout\Models\Payout;

final class CreatePayoutFromBookingAction
{
    public function execute(Booking $booking): ?Payout
    {
        $booking->loadMissing('experience.agency');

        if ($booking->experience->agency->is_platform) {
            return null;
        }

        return Payout::query()->firstOrCreate(
            ['booking_id' => $booking->id],
            [
                'agency_id' => $booking->experience->agency_id,
                'amount' => $booking->agency_amount,
                'status' => 'pending',
            ],
        );
    }
}
