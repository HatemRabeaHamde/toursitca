<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Routing\UrlGenerator;

final class DashboardDestination
{
    public function __construct(private readonly UrlGenerator $urls) {}

    public function forUser(User $user): string
    {
        if ($user->hasRole('admin')) {
            return $this->urls->route('admin.dashboard', [], false);
        }

        $agency = $user->agency;
        if ($agency !== null || $user->hasRole('travel_agency')) {
            return $this->urls->route($agency?->status === 'active' ? 'agency.dashboard' : 'agency.pending', [], false);
        }

        $locale = in_array($user->preferred_lang, ['en', 'fr', 'nl'], true) ? $user->preferred_lang : 'en';

        return $this->urls->route('site.dashboard.bookings', ['locale' => $locale], false);
    }
}
