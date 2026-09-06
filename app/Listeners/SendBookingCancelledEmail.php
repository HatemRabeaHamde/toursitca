<?php

namespace App\Listeners;

use App\Domain\Booking\Events\BookingCancelled;
use App\Mail\BookingCancelledMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

final class SendBookingCancelledEmail implements ShouldQueue
{
    public function handle(BookingCancelled $event): void
    {
        $booking = $event->booking->load(['experience', 'availability']);

        if (filled($booking->guest_email)) {
            Mail::to($booking->guest_email, $booking->guest_name)
                ->send(new BookingCancelledMail($booking));
        }
    }
}
