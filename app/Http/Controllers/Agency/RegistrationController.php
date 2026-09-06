<?php

namespace App\Http\Controllers\Agency;

use App\Domain\Agency\Models\Agency;
use App\Http\Controllers\Controller;
use App\Http\Requests\Agency\AgencyRegistrationRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('agency.auth.register');
    }

    public function store(AgencyRegistrationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data): User {
            $user = User::query()->create([
                'name' => $data['owner_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'preferred_lang' => app()->getLocale(),
                'status' => 'active',
            ]);

            $user->assignRole(Role::findOrCreate('user', 'web'));

            Agency::query()->create([
                'user_id' => $user->id,
                'name' => $data['agency_name'],
                'slug' => $this->uniqueSlug($data['agency_name']),
                'description' => $this->localizedText($data['description'] ?? ''),
                'commission_rate' => config('commission.default_rate'),
                'status' => 'pending',
                'city' => $data['city'],
                'phone' => $data['phone'] ?? null,
                'languages' => [app()->getLocale()],
                'is_platform' => false,
            ]);

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('agency.pending');
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
