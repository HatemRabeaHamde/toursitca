<?php

namespace App\Domain\Review\Actions;

use App\Domain\Booking\Models\Booking;
use App\Domain\Review\DTOs\ReviewData;
use App\Domain\Review\Events\ReviewSubmitted;
use App\Domain\Review\Models\Review;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class SubmitReviewAction
{
    public function execute(Booking $booking, ReviewData $data): Review
    {
        return DB::transaction(function () use ($booking, $data): Review {
            $lockedBooking = Booking::query()
                ->whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedBooking->status !== 'completed') {
                throw new InvalidArgumentException('Only completed bookings can be reviewed.');
            }

            if ($lockedBooking->review()->exists()) {
                throw new InvalidArgumentException('This booking already has a review.');
            }

            $review = Review::create([
                'booking_id' => $lockedBooking->id,
                'user_id' => $lockedBooking->user_id,
                'experience_id' => $lockedBooking->experience_id,
                'rating' => $data->rating,
                'body' => $data->body,
            ]);

            ReviewSubmitted::dispatch($review);

            return $review;
        });
    }
}
