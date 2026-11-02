<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceReviewService;
use App\Models\User;

final class WithdrawExperienceReviewAction
{
    public function __construct(private readonly ExperienceReviewService $reviews) {}

    public function execute(Experience $experience, User $actor): void
    {
        $this->reviews->withdraw($experience, $actor);
    }
}
