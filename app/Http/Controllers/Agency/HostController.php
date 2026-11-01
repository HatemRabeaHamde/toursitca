<?php

namespace App\Http\Controllers\Agency;

use App\Domain\Discover\Actions\DeleteHostAction;
use App\Domain\Discover\Actions\SaveHostProfileAction;
use App\Domain\Discover\Models\Host;
use App\Domain\Discover\Services\HostFormOptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Agency\HostProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class HostController extends Controller
{
    public function index(): View
    {
        $hosts = Host::query()->ofAgency(auth()->user()->agency->id)->withCount('experiences')->orderBy('name')->get();

        return view('agency.hosts.index', compact('hosts'));
    }

    public function create(HostFormOptions $options): View
    {
        return view('agency.hosts.create', $options->profile());
    }

    public function store(HostProfileRequest $request, SaveHostProfileAction $action): RedirectResponse
    {
        $action->execute(null, (int) $request->user()->agency->id, $request->toData());

        return redirect()->route('agency.hosts.index')->with('success', __('hosts_admin.saved_pending'));
    }

    public function edit(Host $host, HostFormOptions $options): View
    {
        $this->authorizeOwnHost($host);

        return view('agency.hosts.edit', ['host' => $host, ...$options->profile()]);
    }

    public function update(HostProfileRequest $request, Host $host, SaveHostProfileAction $action): RedirectResponse
    {
        $this->authorizeOwnHost($host);
        $host = $action->execute($host, (int) $host->agency_id, $request->toData());

        return redirect()->route('agency.hosts.index')->with('success', $host->reviewed_at ? __('hosts_admin.saved') : __('hosts_admin.saved_pending'));
    }

    public function destroy(Host $host, DeleteHostAction $action): RedirectResponse
    {
        $this->authorizeOwnHost($host);

        try {
            $action->execute($host);
        } catch (InvalidArgumentException $exception) {
            return redirect()->route('agency.hosts.index')->with('error', $exception->getMessage());
        }

        return redirect()->route('agency.hosts.index')->with('success', __('hosts_admin.deleted'));
    }

    private function authorizeOwnHost(Host $host): void
    {
        abort_unless($host->agency_id === auth()->user()->agency->id, 403);
    }
}
