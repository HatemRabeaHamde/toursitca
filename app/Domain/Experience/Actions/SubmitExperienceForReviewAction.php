<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceChangeRequest;
use App\Domain\Experience\Services\ExperienceReviewService;
use App\Models\User;

final class SubmitExperienceForReviewAction
{
    public function __construct(private readonly ExperienceReviewService $reviews) {}

    public function execute(Experience $experience, User $actor): ExperienceChangeRequest
    {
        return $this->reviews->submit($experience, $actor);
    }
}
