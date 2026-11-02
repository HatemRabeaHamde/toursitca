<?php

namespace App\Http\Controllers\Agency;

use App\Domain\Experience\Actions\DeleteExperienceOptionAction;
use App\Domain\Experience\Actions\SaveExperienceOptionAction;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceOption;
use App\Domain\Experience\Services\ExperienceDraftService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Experience\ExperienceOptionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class ExperienceOptionController extends Controller
{
    public function index(Experience $experience, ExperienceDraftService $drafts): View
    {
        $this->authorizeExperience($experience);
        $experience->load('latestChangeRequest');
        $experience = $drafts->forEditing($experience);

        return view('experience-options.index', [
            'experience' => $experience,
            'routePrefix' => 'agency',
        ]);
    }

    public function store(ExperienceOptionRequest $request, Experience $experience, SaveExperienceOptionAction $action): RedirectResponse
    {
        $this->authorizeExperience($experience);
        $action->execute($experience, $request->optionAttributes(), $request->priceRows(), $request->languageRows());

        return redirect()
            ->route('agency.experiences.options.index', $experience)
            ->with('success', __('ui.messages.option_saved'));
    }

    public function update(
        ExperienceOptionRequest $request,
        Experience $experience,
        ExperienceOption $option,
        SaveExperienceOptionAction $action,
    ): RedirectResponse {
        $this->authorizeExperience($experience);
        abort_unless($option->experience_id === $experience->id, 404);

        $action->execute($experience, $request->optionAttributes(), $request->priceRows(), $request->languageRows(), $option);

        return redirect()
            ->route('agency.experiences.options.index', $experience)
            ->with('success', __('ui.messages.option_saved'));
    }

    public function destroy(Experience $experience, ExperienceOption $option, DeleteExperienceOptionAction $action): RedirectResponse
    {
        $this->authorizeExperience($experience);
        abort_unless($option->experience_id === $experience->id, 404);

        try {
            $action->execute($option);
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('agency.experiences.options.index', $experience)
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('agency.experiences.options.index', $experience)
            ->with('success', __('ui.messages.option_deleted'));
    }

    private function authorizeExperience(Experience $experience): void
    {
        abort_unless($experience->agency_id === auth()->user()->agency->id, 403);
    }
}
