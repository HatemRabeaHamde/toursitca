<?php

namespace App\Http\Controllers\Agency;

use App\Domain\Experience\Actions\SubmitExperienceForReviewAction;
use App\Domain\Experience\Actions\WithdrawExperienceReviewAction;
use App\Domain\Experience\Models\Experience;
use App\Http\Controllers\Controller;
use App\Http\Requests\Experience\ManageExperienceReviewRequest;
use Illuminate\Http\RedirectResponse;

class ExperienceReviewController extends Controller
{
    public function store(ManageExperienceReviewRequest $request, Experience $experience, SubmitExperienceForReviewAction $action): RedirectResponse
    {
        $action->execute($experience, $request->user());

        return back()->with('success', __('experience_review.submitted'));
    }

    public function destroy(ManageExperienceReviewRequest $request, Experience $experience, WithdrawExperienceReviewAction $action): RedirectResponse
    {
        $action->execute($experience, $request->user());

        return back()->with('success', __('experience_review.withdrawn'));
    }
}
