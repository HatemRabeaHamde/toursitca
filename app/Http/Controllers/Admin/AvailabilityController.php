<?php

namespace App\Http\Controllers\Admin;

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
        $availabilities = Availability::query()
            ->with(['experience.agency', 'option'])
            ->orderBy('date')
            ->orderBy('time_slot')
            ->paginate(20);

        $experiences = Experience::query()
            ->with(['agency', 'options' => fn ($query) => $query->orderBy('id')])
            ->orderBy('location_city')
            ->orderBy('category')
            ->get();

        return view('admin.availability.index', compact('availabilities', 'experiences'));
    }

    public function store(AvailabilityStoreRequest $request, CreateAvailabilityAction $action): RedirectResponse
    {
        $action->execute($request->payload());

        return redirect()
            ->route('admin.availability.index')
            ->with('success', __('ui.messages.availability_created'));
    }

    public function update(
        AvailabilityStoreRequest $request,
        Availability $availability,
        UpdateAvailabilityAction $action,
    ): RedirectResponse {
        $action->execute($availability, $request->payload());

        return redirect()
            ->route('admin.availability.index')
            ->with('success', __('ui.messages.availability_updated'));
    }

    public function destroy(Availability $availability, DeleteAvailabilityAction $action): RedirectResponse
    {
        try {
            $action->execute($availability);
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('admin.availability.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.availability.index')
            ->with('success', __('ui.messages.availability_deleted'));
    }
}
