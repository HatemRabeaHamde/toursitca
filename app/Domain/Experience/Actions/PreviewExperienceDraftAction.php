<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceDraftService;
use App\Domain\Experience\Services\ExperiencePageService;
use App\Domain\Experience\ViewModels\ExperiencePageViewModel;

final class PreviewExperienceDraftAction
{
    public function __construct(private readonly ExperienceDraftService $drafts, private readonly ExperiencePageService $pages) {}

    public function execute(Experience $experience): ExperiencePageViewModel
    {
        return $this->pages->build($this->drafts->forEditing($experience), true);
    }
}
