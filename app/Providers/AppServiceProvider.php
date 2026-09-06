<?php

namespace App\Providers;

use App\Domain\Agency\Events\AgencyApproved;
use App\Domain\Agency\Events\AgencyRejected;
use App\Domain\Booking\Events\BookingCancelled;
use App\Domain\Booking\Events\BookingCompleted;
use App\Domain\Booking\Events\BookingConfirmed;
use App\Domain\Review\Events\ReviewSubmitted;
use App\Listeners\SendAgencyApprovedEmail;
use App\Listeners\SendAgencyRejectedEmail;
use App\Listeners\SendBookingCancelledEmail;
use App\Listeners\SendBookingConfirmedEmail;
use App\Listeners\SendReviewInviteEmail;
use App\Listeners\UpdateExperienceRating;
use App\View\Composers\AgencyNavComposer;
use App\View\Composers\WishlistComposer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('layouts.agency', AgencyNavComposer::class);
        View::composer(
            [
                'site.experiences.partials.card-grid',
                'site.experiences.partials.detail-hero',
                'site.home.partials.deals',
            ],
            WishlistComposer::class
        );

        Event::listen(BookingConfirmed::class, SendBookingConfirmedEmail::class);
        Event::listen(BookingCancelled::class, SendBookingCancelledEmail::class);
        Event::listen(BookingCompleted::class, SendReviewInviteEmail::class);
        Event::listen(AgencyApproved::class, SendAgencyApprovedEmail::class);
        Event::listen(AgencyRejected::class, SendAgencyRejectedEmail::class);
        Event::listen(ReviewSubmitted::class, UpdateExperienceRating::class);
    }
}
