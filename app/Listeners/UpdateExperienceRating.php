<?php

namespace App\Listeners;

use App\Domain\Review\Events\ReviewSubmitted;

final class UpdateExperienceRating
{
    public function handle(ReviewSubmitted $event): void
    {
        $experience = $event->review->experience;

        $experience->forceFill([
            'rating_avg' => (float) $experience->reviews()->avg('rating') ?? 0,
            'reviews_count' => $experience->reviews()->count(),
        ])->save();
    }
}
