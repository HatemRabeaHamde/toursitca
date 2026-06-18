<?php

namespace App\Http\Controllers\Agency;

use App\Domain\Experience\Actions\CreateAvailabilityAction;
use App\Domain\Experience\Actions\DeleteAvailabilityAction;
use App\Domain\Experience\Actions\UpdateAvailabilityAction;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Http\Controllers\Controller;
use App\Http\Requests\Experience\AvailabilityStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class AvailabilityController extends Controller
{
    public function index(): View
    {
        $agencyId = (int) auth()->user()->agency->id;

        $availabilities = Availability::query()
            ->whereHas('experience', fn ($query) => $query->where('agency_id', $agencyId))
            ->with(['experience', 'option'])
            ->orderBy('date')
            ->orderBy('time_slot')
            ->paginate(20);

        $experiences = Experience::query()
            ->where('agency_id', $agencyId)
            ->with(['options' => fn ($query) => $query->orderBy('id')])
            ->orderBy('location_city')
            ->orderBy('category')
            ->get();

        return view('agency.availability.index', compact('availabilities', 'experiences'));
    }

    public function store(AvailabilityStoreRequest $request, CreateAvailabilityAction $action): RedirectResponse
    {
        $this->authorizeExperience((int) $request->input('experience_id'));
        $action->execute($request->payload());

        return redirect()
            ->route('agency.availability.index')
            ->with('success', __('ui.messages.availability_created'));
    }

    public function update(
        AvailabilityStoreRequest $request,
        Availability $availability,
        UpdateAvailabilityAction $action,
    ): RedirectResponse {
        $this->authorizeAvailability($availability);
        $this->authorizeExperience((int) $request->input('experience_id'));
        $action->execute($availability, $request->payload());

        return redirect()
            ->route('agency.availability.index')
            ->with('success', __('ui.messages.availability_updated'));
    }

    public function destroy(Availability $availability, DeleteAvailabilityAction $action): RedirectResponse
    {
        $this->authorizeAvailability($availability);

        try {
            $action->execute($availability);
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('agency.availability.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('agency.availability.index')
            ->with('success', __('ui.messages.availability_deleted'));
    }

    private function authorizeExperience(int $experienceId): void
    {
        abort_unless(
            Experience::query()
                ->whereKey($experienceId)
                ->where('agency_id', auth()->user()->agency->id)
                ->exists(),
            403,
        );
    }

    private function authorizeAvailability(Availability $availability): void
    {
        $availability->loadMissing('experience');
        abort_unless($availability->experience->agency_id === auth()->user()->agency->id, 403);
    }
}
