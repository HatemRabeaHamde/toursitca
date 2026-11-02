<?php

namespace App\Http\Controllers\Agency;

use App\Domain\Discover\Services\HostFormOptions;
use App\Domain\Experience\Actions\AttachExperienceMediaAction;
use App\Domain\Experience\Actions\CreateExperienceAction;
use App\Domain\Experience\Actions\DeleteExperienceAction;
use App\Domain\Experience\Actions\UnpublishExperienceAction;
use App\Domain\Experience\Actions\UpdateExperienceAction;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceDraftService;
use App\Domain\Landing\Models\ExperienceCategory;
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
            ->where('agency_id', auth()->user()->agency->id)
            ->with('latestChangeRequest')
            ->latest()
            ->paginate(15);

        return view('agency.experiences.index', compact('experiences'));
    }

    public function create(HostFormOptions $hostOptions): View
    {
        $categories = ExperienceCategory::active()->sorted()->get();
        $hosts = $hostOptions->forAgency(auth()->user()->agency->id);

        return view('agency.experiences.create', compact('categories', 'hosts'));
    }

    public function store(
        ExperienceStoreRequest $request,
        CreateExperienceAction $createExperienceAction,
        AttachExperienceMediaAction $attachExperienceMediaAction,
    ): RedirectResponse {
        $experience = $createExperienceAction->execute(
            $request->toData((int) $request->user()->agency->id, (int) $request->user()->id),
        );

        $attachExperienceMediaAction->execute(
            $experience,
            $request->file('images', []),
            $request->file('video_file'),
            $request->filled('video_url') ? $request->string('video_url')->toString() : null,
        );

        return redirect()
            ->route('agency.experiences.index')
            ->with('success', __('ui.messages.experience_created'));
    }

    public function edit(Experience $experience, HostFormOptions $hostOptions, ExperienceDraftService $drafts): View
    {
        $this->authorizeAgencyExperience($experience);
        $experience->load('latestChangeRequest');
        $experience = $drafts->forEditing($experience);
        $categories = ExperienceCategory::active()->sorted()->get();
        $hosts = $hostOptions->forAgency(auth()->user()->agency->id);

        return view('agency.experiences.edit', compact('experience', 'categories', 'hosts'));
    }

    public function update(
        ExperienceStoreRequest $request,
        Experience $experience,
        UpdateExperienceAction $updateExperienceAction,
        AttachExperienceMediaAction $attachExperienceMediaAction,
    ): RedirectResponse {
        $this->authorizeAgencyExperience($experience);

        $experience = $updateExperienceAction->execute(
            $experience,
            $request->toData((int) $request->user()->agency->id, (int) $request->user()->id),
        );

        $attachExperienceMediaAction->execute(
            $experience,
            $request->file('images', []),
            $request->file('video_file'),
            $request->filled('video_url') ? $request->string('video_url')->toString() : null,
        );

        return redirect()
            ->route('agency.experiences.index')
            ->with('success', __('ui.messages.experience_updated'));
    }

    public function unpublish(Experience $experience, UnpublishExperienceAction $unpublishExperienceAction): RedirectResponse
    {
        $this->authorizeAgencyExperience($experience);
        $unpublishExperienceAction->execute($experience);

        return redirect()
            ->route('agency.experiences.index')
            ->with('success', __('ui.messages.experience_unpublished'));
    }

    public function destroy(Experience $experience, DeleteExperienceAction $deleteExperienceAction): RedirectResponse
    {
        $this->authorizeAgencyExperience($experience);

        try {
            $deleteExperienceAction->execute($experience);
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('agency.experiences.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('agency.experiences.index')
            ->with('success', __('ui.messages.experience_deleted'));
    }

    private function authorizeAgencyExperience(Experience $experience): void
    {
        abort_unless($experience->agency_id === auth()->user()->agency->id, 403);
    }
}
