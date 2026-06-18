<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Experience\Actions\DeleteExperienceOptionAction;
use App\Domain\Experience\Actions\SaveExperienceOptionAction;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceOption;
use App\Http\Controllers\Controller;
use App\Http\Requests\Experience\ExperienceOptionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class ExperienceOptionController extends Controller
{
    public function index(Experience $experience): View
    {
        $experience->load(['options.prices', 'options.languages']);

        return view('experience-options.index', [
            'experience' => $experience,
            'routePrefix' => 'admin',
        ]);
    }

    public function store(ExperienceOptionRequest $request, Experience $experience, SaveExperienceOptionAction $action): RedirectResponse
    {
        $action->execute($experience, $request->optionAttributes(), $request->priceRows(), $request->languageRows());

        return redirect()
            ->route('admin.experiences.options.index', $experience)
            ->with('success', __('ui.messages.option_saved'));
    }

    public function update(
        ExperienceOptionRequest $request,
        Experience $experience,
        ExperienceOption $option,
        SaveExperienceOptionAction $action,
    ): RedirectResponse {
        abort_unless($option->experience_id === $experience->id, 404);

        $action->execute($experience, $request->optionAttributes(), $request->priceRows(), $request->languageRows(), $option);

        return redirect()
            ->route('admin.experiences.options.index', $experience)
            ->with('success', __('ui.messages.option_saved'));
    }

    public function destroy(Experience $experience, ExperienceOption $option, DeleteExperienceOptionAction $action): RedirectResponse
    {
        abort_unless($option->experience_id === $experience->id, 404);

        try {
            $action->execute($option);
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('admin.experiences.options.index', $experience)
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.experiences.options.index', $experience)
            ->with('success', __('ui.messages.option_deleted'));
    }
}
