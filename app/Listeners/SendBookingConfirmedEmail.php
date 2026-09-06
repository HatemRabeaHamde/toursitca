<?php

namespace App\Listeners;

use App\Domain\Booking\Events\BookingConfirmed;
use App\Mail\BookingConfirmedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

final class SendBookingConfirmedEmail implements ShouldQueue
{
    public function handle(BookingConfirmed $event): void
    {
        $booking = $event->booking->load(['experience.agency', 'availability']);

        if (filled($booking->guest_email)) {
            Mail::to($booking->guest_email, $booking->guest_name)
                ->send(new BookingConfirmedMail($booking));
        }
    }
}
