<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Agency\Actions\ApproveAgencyAction;
use App\Domain\Agency\Actions\ReactivateAgencyAction;
use App\Domain\Agency\Actions\RejectAgencyAction;
use App\Domain\Agency\Actions\SuspendAgencyAction;
use App\Domain\Agency\Models\Agency;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlatformAgencyStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AgencyController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $agencies = Agency::query()
            ->with('user')
            ->withCount('experiences')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return view('admin.agencies.index', compact('agencies', 'status'));
    }

    public function show(Agency $agency): View
    {
        $agency->load(['user', 'experiences' => fn ($q) => $q->latest()->limit(10)]);

        $stats = [
            'experiences_total'     => $agency->experiences()->count(),
            'experiences_published' => $agency->experiences()->where('status', 'published')->count(),
            'bookings_total'        => $agency->experiences()->withCount('bookings')->get()->sum('bookings_count'),
            'payouts_pending'       => $agency->payouts()->where('status', 'pending')->count(),
        ];

        return view('admin.agencies.show', compact('agency', 'stats'));
    }

    public function edit(Agency $agency): View
    {
        return view('admin.agencies.edit', compact('agency'));
    }

    public function update(Request $request, Agency $agency): RedirectResponse
    {
        $data = $request->validate([
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'phone'           => ['nullable', 'string', 'max:30'],
            'city'            => ['required', 'string', 'max:100'],
        ]);

        $agency->update($data);

        return redirect()
            ->route('admin.agencies.show', $agency)
            ->with('success', __('ui.messages.agency_updated'));
    }

    public function create(): View
    {
        return view('admin.agencies.create');
    }

    public function store(PlatformAgencyStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Agency::query()->create([
            'user_id'         => $request->user()->id,
            'name'            => $data['agency_name'],
            'slug'            => $this->uniqueSlug($data['agency_name']),
            'description'     => $this->localizedText($data['description'] ?? ''),
            'commission_rate' => '0.00',
            'status'          => 'active',
            'city'            => $data['city'],
            'phone'           => $data['phone'] ?? null,
            'languages'       => array_keys(config('locales.supported')),
            'is_platform'     => true,
        ]);

        return redirect()
            ->route('admin.agencies.index')
            ->with('success', __('ui.messages.platform_agency_created'));
    }

    public function approve(Agency $agency, ApproveAgencyAction $action): RedirectResponse
    {
        $action->execute($agency);

        return redirect()
            ->back()
            ->with('success', __('ui.messages.agency_approved'));
    }

    public function suspend(Agency $agency, SuspendAgencyAction $action): RedirectResponse
    {
        $action->execute($agency);

        return redirect()
            ->back()
            ->with('success', __('ui.messages.agency_suspended'));
    }

    public function reactivate(Agency $agency, ReactivateAgencyAction $action): RedirectResponse
    {
        $action->execute($agency);

        return redirect()
            ->back()
            ->with('success', __('ui.messages.agency_reactivated'));
    }

    public function reject(Agency $agency, RejectAgencyAction $action): RedirectResponse
    {
        $action->execute($agency);

        return redirect()
            ->back()
            ->with('success', __('ui.messages.agency_rejected'));
    }

    private function uniqueSlug(string $name): string
    {
        $base      = Str::slug($name);
        $slug      = $base === '' ? Str::uuid()->toString() : $base;
        $candidate = $slug;
        $counter   = 2;

        while (Agency::query()->where('slug', $candidate)->exists()) {
            $candidate = $slug.'-'.$counter;
            $counter++;
        }

        return $candidate;
    }

    private function localizedText(string $value): array
    {
        return collect(config('locales.supported'))
            ->keys()
            ->mapWithKeys(fn (string $locale): array => [$locale => $value])
            ->all();
    }
}
