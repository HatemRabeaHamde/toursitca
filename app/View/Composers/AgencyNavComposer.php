<?php

namespace App\View\Composers;

use App\Domain\Booking\Models\Booking;
use App\Domain\Experience\Models\Experience;
use Illuminate\View\View;

class AgencyNavComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();

        if (! $user || ! $user->agency) {
            $view->with('agencyNavDraftCount', 0);
            $view->with('agencyNavPendingCount', 0);

            return;
        }

        $agencyId = (int) $user->agency->id;

        $view->with('agencyNavDraftCount', Experience::where('agency_id', $agencyId)->where('status', 'draft')->count());
        $view->with('agencyNavPendingCount', Booking::whereHas('experience', fn ($q) => $q->where('agency_id', $agencyId))->where('status', 'pending')->count());
    }
}
