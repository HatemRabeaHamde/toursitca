<?php

namespace App\Http\ViewComposers;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Models\Booking;
use App\Domain\Discover\Models\Host;
use App\Domain\Landing\Models\NewsletterSubscriber;
use App\Domain\Payout\Models\Payout;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class NavbarComposer
{
    public function compose(View $view): void
    {
        $isAdmin = $view->name() === 'layouts.admin';
        $view->with([
            'adminAgenciesPending' => $isAdmin ? Agency::where('status', 'pending')->count() : 0,
            'adminHostsPending' => $isAdmin ? Host::awaitingReview()->count() : 0,
            'adminBookingsPending' => $isAdmin ? Booking::where('status', 'pending')->count() : 0,
            'adminPayoutsPending' => $isAdmin ? Payout::where('status', 'pending')->count() : 0,
            'locales' => config('locales.supported', []),
            'currentLocale' => App::getLocale(),
            'authUser' => auth()->user(),
            'categories' => trans('ui.categories'),
            'adminUnreadInquiries' => $view->name() === 'layouts.admin'
                ? NewsletterSubscriber::whereNotNull('message')->whereNull('read_at')->count()
                : null,
        ]);
    }
}
