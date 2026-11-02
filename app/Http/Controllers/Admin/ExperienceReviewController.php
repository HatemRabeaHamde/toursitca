<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Experience\Actions\ReviewExperienceAction;
use App\Domain\Experience\Actions\ShowExperienceReviewsAction;
use App\Domain\Experience\Models\ExperienceChangeRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Experience\ExperienceReviewIndexRequest;
use App\Http\Requests\Experience\ReviewExperienceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExperienceReviewController extends Controller
{
    public function index(ExperienceReviewIndexRequest $request, ShowExperienceReviewsAction $action): View
    {
        return view('admin.experience-reviews.index', ['page' => $action->execute(status: $request->input('status') ?: 'pending')]);
    }

    public function show(ExperienceChangeRequest $review, ShowExperienceReviewsAction $action): View
    {
        return view('admin.experience-reviews.show', ['page' => $action->execute($review)]);
    }

    public function update(ReviewExperienceRequest $request, ExperienceChangeRequest $review, ReviewExperienceAction $action): RedirectResponse
    {
        $action->execute($review, $request->user(), $request->string('decision')->toString(), $request->input('reason'));

        return redirect()->route('admin.experience-reviews.index')->with('success', __('experience_review.decided'));
    }
}
