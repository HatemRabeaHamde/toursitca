<?php

namespace App\Console\Commands;

use App\Domain\Booking\Events\BookingCompleted;
use App\Domain\Booking\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CompleteBookingsCommand extends Command
{
    protected $signature = 'bookings:complete';

    protected $description = 'Mark confirmed bookings as completed when their trip date has passed.';

    public function handle(): int
    {
        $count = 0;

        Booking::query()
            ->where('status', 'confirmed')
            ->whereHas('availability', fn ($q) => $q->whereDate('date', '<', now()->toDateString()))
            ->with('availability')
            ->lazyById()
            ->each(function (Booking $booking) use (&$count): void {
                DB::transaction(function () use ($booking, &$count): void {
                    $booking->forceFill(['status' => 'completed'])->save();
                    BookingCompleted::dispatch($booking);
                    $count++;
                });
            });

        $this->info("Completed {$count} booking(s).");

        return self::SUCCESS;
    }
}
