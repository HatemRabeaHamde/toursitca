<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $agency = $request->user()->agency;

        return view('agency.profile.edit', compact('agency'));
    }

    public function update(Request $request): RedirectResponse
    {
        $agency = $request->user()->agency;

        $data = $request->validate([
            'name'              => ['required', 'string', 'max:120'],
            'city'              => ['required', 'string', 'max:80'],
            'phone'             => ['nullable', 'string', 'max:30'],
            'description.en'    => ['nullable', 'string', 'max:2000'],
            'description.fr'    => ['nullable', 'string', 'max:2000'],
            'languages'         => ['nullable', 'array'],
            'languages.*'       => ['string', 'in:en,fr,ar,es,de,it,nl'],
        ]);

        $agency->name     = $data['name'];
        $agency->city     = $data['city'];
        $agency->phone    = $data['phone'] ?? null;
        $agency->languages = $data['languages'] ?? [];

        foreach (['en', 'fr'] as $locale) {
            $agency->setTranslation('description', $locale, $data['description'][$locale] ?? '');
        }

        $agency->save();

        return redirect()->route('agency.profile.edit')->with('success', __('ui.messages.profile_updated'));
    }
}
