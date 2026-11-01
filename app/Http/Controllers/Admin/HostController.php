<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Agency\Models\Agency;
use App\Domain\Discover\Actions\DeleteHostAction;
use App\Domain\Discover\Actions\ReviewHostAction;
use App\Domain\Discover\Actions\SaveHostProfileAction;
use App\Domain\Discover\Models\Host;
use App\Domain\Discover\Services\HostFormOptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HostStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class HostController extends Controller
{
    public function index(Request $request): View
    {
        $status = in_array($request->query('status'), ['review', 'live', 'hidden'], true) ? $request->query('status') : null;

        $hosts = Host::query()
            ->with('agency:id,name,is_platform')
            ->withCount('experiences')
            ->when($status === 'review', fn ($query) => $query->awaitingReview())
            ->when($status === 'live', fn ($query) => $query->where('is_active', true))
            ->when($status === 'hidden', fn ($query) => $query->where('is_active', false)->whereNotNull('reviewed_at'))
            ->orderByRaw('reviewed_at IS NULL DESC')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.hosts.index', compact('hosts', 'status'));
    }

    public function create(HostFormOptions $options): View
    {
        return view('admin.hosts.create', ['agencies' => $this->agencies(), ...$options->profile()]);
    }

    public function store(HostStoreRequest $request, SaveHostProfileAction $save, ReviewHostAction $review): RedirectResponse
    {
        $host = $save->execute(null, $request->integer('agency_id'), $request->toData());

        return $this->review($host, $request, $review, __('hosts_admin.saved'));
    }

    public function edit(Host $host, HostFormOptions $options): View
    {
        return view('admin.hosts.edit', ['host' => $host, 'agencies' => $this->agencies(), ...$options->profile()]);
    }

    public function update(HostStoreRequest $request, Host $host, SaveHostProfileAction $save, ReviewHostAction $review): RedirectResponse
    {
        $host->agency_id = $request->integer('agency_id');
        $host = $save->execute($host, $request->integer('agency_id'), $request->toData());

        return $this->review($host, $request, $review, __('hosts_admin.saved'));
    }

    public function approve(Host $host, ReviewHostAction $review): RedirectResponse
    {
        try {
            $review->execute($host, active: true, verified: true);
        } catch (InvalidArgumentException $exception) {
            return redirect()->route('admin.hosts.index')->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.hosts.index')->with('success', __('hosts_admin.approved', ['name' => $host->name]));
    }

    public function destroy(Host $host, DeleteHostAction $action): RedirectResponse
    {
        try {
            $action->execute($host);
        } catch (InvalidArgumentException $exception) {
            return redirect()->route('admin.hosts.index')->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.hosts.index')->with('success', __('hosts_admin.deleted'));
    }

    private function review(Host $host, HostStoreRequest $request, ReviewHostAction $review, string $message): RedirectResponse
    {
        try {
            $review->execute($host, $request->boolean('is_active'), $request->boolean('is_verified'), $request->filled('sort_order') ? $request->integer('sort_order') : null);
        } catch (InvalidArgumentException $exception) {
            return redirect()->route('admin.hosts.edit', $host)->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.hosts.index')->with('success', $message);
    }

    private function agencies()
    {
        return Agency::query()->active()->orderByDesc('is_platform')->orderBy('name')->get();
    }
}
