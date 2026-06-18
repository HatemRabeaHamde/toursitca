<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Agency\Actions\ApproveAgencyAction;
use App\Domain\Agency\Models\Agency;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlatformAgencyStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AgencyController extends Controller
{
    public function index(): View
    {
        $agencies = Agency::query()
            ->with('user')
            ->withCount('experiences')
            ->latest()
            ->paginate(15);

        return view('admin.agencies.index', compact('agencies'));
    }

    public function create(): View
    {
        return view('admin.agencies.create');
    }

    public function store(PlatformAgencyStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Agency::query()->create([
            'user_id' => $request->user()->id,
            'name' => $data['agency_name'],
            'slug' => $this->uniqueSlug($data['agency_name']),
            'description' => $this->localizedText($data['description'] ?? ''),
            'commission_rate' => '0.00',
            'status' => 'active',
            'city' => $data['city'],
            'phone' => $data['phone'] ?? null,
            'languages' => array_keys(config('locales.supported')),
            'is_platform' => true,
        ]);

        return redirect()
            ->route('admin.agencies.index')
            ->with('success', __('ui.messages.platform_agency_created'));
    }

    public function approve(Agency $agency, ApproveAgencyAction $approveAgencyAction): RedirectResponse
    {
        $approveAgencyAction->execute($agency);

        return redirect()
            ->route('admin.agencies.index')
            ->with('success', __('ui.messages.agency_approved'));
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base === '' ? Str::uuid()->toString() : $base;
        $candidate = $slug;
        $counter = 2;

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
