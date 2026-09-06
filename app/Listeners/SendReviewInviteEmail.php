<?php

namespace App\Listeners;

use App\Domain\Booking\Events\BookingCompleted;
use App\Mail\ReviewInviteMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

final class SendReviewInviteEmail implements ShouldQueue
{
    /* delay 1 hour so the email doesn't land while they're still on the trip */
    public int $delay = 3600;

    public function handle(BookingCompleted $event): void
    {
        $booking = $event->booking->load(['experience.agency']);

        if (filled($booking->guest_email)) {
            Mail::to($booking->guest_email, $booking->guest_name)
                ->send(new ReviewInviteMail($booking));
        }
    }
}
