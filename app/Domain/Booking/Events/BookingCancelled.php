<?php

namespace App\Domain\Booking\Events;

use App\Domain\Booking\Models\Booking;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class BookingCancelled
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Booking $booking) {}
}
