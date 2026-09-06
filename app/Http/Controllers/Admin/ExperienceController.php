<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Agency\Models\Agency;
use App\Domain\Landing\Models\ExperienceCategory;
use App\Domain\Experience\Actions\AttachExperienceMediaAction;
use App\Domain\Experience\Actions\CreateExperienceAction;
use App\Domain\Experience\Actions\DeleteExperienceAction;
use App\Domain\Experience\Actions\PublishExperienceAction;
use App\Domain\Experience\Actions\UnpublishExperienceAction;
use App\Domain\Experience\Actions\UpdateExperienceAction;
use App\Domain\Experience\Models\Experience;
use App\Http\Controllers\Controller;
use App\Http\Requests\Experience\ExperienceStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class ExperienceController extends Controller
{
    public function index(): View
    {
        $experiences = Experience::query()
            ->with('agency')
            ->latest()
            ->paginate(15);

        return view('admin.experiences.index', compact('experiences'));
    }

    public function create(): View
    {
        $agencies = Agency::query()
            ->active()
            ->orderByDesc('is_platform')
            ->orderBy('name')
            ->get();

        $categories = ExperienceCategory::active()->sorted()->get();

        return view('admin.experiences.create', compact('agencies', 'categories'));
    }

    public function store(
        ExperienceStoreRequest $request,
        CreateExperienceAction $createExperienceAction,
        AttachExperienceMediaAction $attachExperienceMediaAction,
    ): RedirectResponse {
        $agencyId = $this->resolveAgencyId($request);

        if ($agencyId === null) {
            return redirect()
                ->route('admin.agencies.create')
                ->with('error', __('ui.messages.platform_agency_required'));
        }

        $experience = $createExperienceAction->execute(
            $request->toData($agencyId, (int) $request->user()->id),
        );

        $attachExperienceMediaAction->execute(
            $experience,
            $request->file('images', []),
            $request->file('video_file'),
            $request->filled('video_url') ? $request->string('video_url')->toString() : null,
        );

        return redirect()
            ->route('admin.experiences.index')
            ->with('success', __('ui.messages.experience_created'));
    }

    public function edit(Experience $experience): View
    {
        $experience->load(['media', 'itineraryItems']);
        $agencies = Agency::query()
            ->active()
            ->orderByDesc('is_platform')
            ->orderBy('name')
            ->get();

        $categories = ExperienceCategory::active()->sorted()->get();

        return view('admin.experiences.edit', compact('agencies', 'experience', 'categories'));
    }

    public function update(
        ExperienceStoreRequest $request,
        Experience $experience,
        UpdateExperienceAction $updateExperienceAction,
        AttachExperienceMediaAction $attachExperienceMediaAction,
    ): RedirectResponse {
        $agencyId = $this->resolveAgencyId($request) ?? $experience->agency_id;

        $experience = $updateExperienceAction->execute(
            $experience,
            $request->toData($agencyId, (int) $request->user()->id),
        );

        $attachExperienceMediaAction->execute(
            $experience,
            $request->file('images', []),
            $request->file('video_file'),
            $request->filled('video_url') ? $request->string('video_url')->toString() : null,
        );

        return redirect()
            ->route('admin.experiences.index')
            ->with('success', __('ui.messages.experience_updated'));
    }

    public function publish(Experience $experience, PublishExperienceAction $publishExperienceAction): RedirectResponse
    {
        try {
            $publishExperienceAction->execute($experience);
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('admin.experiences.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.experiences.index')
            ->with('success', __('ui.messages.experience_published'));
    }

    public function unpublish(Experience $experience, UnpublishExperienceAction $unpublishExperienceAction): RedirectResponse
    {
        $unpublishExperienceAction->execute($experience);

        return redirect()
            ->route('admin.experiences.index')
            ->with('success', __('ui.messages.experience_unpublished'));
    }

    public function destroy(Experience $experience, DeleteExperienceAction $deleteExperienceAction): RedirectResponse
    {
        try {
            $deleteExperienceAction->execute($experience);
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('admin.experiences.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.experiences.index')
            ->with('success', __('ui.messages.experience_deleted'));
    }

    private function resolveAgencyId(ExperienceStoreRequest $request): ?int
    {
        if ($request->filled('agency_id')) {
            return $request->integer('agency_id');
        }

        return Agency::query()
            ->where('user_id', $request->user()->id)
            ->where('is_platform', true)
            ->where('status', 'active')
            ->oldest()
            ->value('id');
    }
}
