<?php

namespace App\Http\Controllers;

use App\Domain\Experience\Actions\PreviewExperienceDraftAction;
use App\Domain\Experience\Models\Experience;
use App\Http\Requests\Experience\ManageExperienceReviewRequest;
use Illuminate\View\View;

class ExperienceDraftPreviewController extends Controller
{
    public function __invoke(ManageExperienceReviewRequest $request, Experience $experience, PreviewExperienceDraftAction $action): View
    {
        return view('site.experiences.detail-v1', ['page' => $action->execute($experience), 'isExperiencePreview' => true, 'seo' => ['robots' => 'noindex, nofollow']]);
    }
}
