<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceChangeRequest;
use App\Domain\Experience\Services\ExperienceReviewService;
use App\Models\User;

final class ReviewExperienceAction
{
    public function __construct(private readonly ExperienceReviewService $reviews) {}

    public function execute(ExperienceChangeRequest $review, User $actor, string $decision, ?string $reason): Experience
    {
        return $this->reviews->decide($review, $actor, $decision, $reason);
    }
}
