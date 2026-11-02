<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\ExperienceChangeRequest;
use App\Domain\Experience\Services\ExperienceReviewPageService;
use App\Domain\Experience\ViewModels\ExperienceReviewViewModel;

final class ShowExperienceReviewsAction
{
    public function __construct(private readonly ExperienceReviewPageService $pages) {}

    public function execute(?ExperienceChangeRequest $review = null, string $status = 'pending'): ExperienceReviewViewModel
    {
        return $this->pages->build($review, $status);
    }
}
