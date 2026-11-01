<?php

namespace App\Http\Controllers\Site;

use App\Domain\Booking\Actions\CancelBookingAction;
use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Services\AccountBookingsService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TouristBookingController extends Controller
{
    public function index(Request $request, AccountBookingsService $service): View
    {
        $tab = in_array($request->query('tab'), AccountBookingsService::TABS, true) ? $request->query('tab') : 'all';

        return view('site.account.bookings', ['page' => $service->build($request->user(), $tab)]);
    }

    public function cancel(Request $request, string $locale, Booking $booking, CancelBookingAction $action): RedirectResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        abort_unless($booking->status === 'pending', 404);

        $action->execute($booking, __('account.bookings.cancelled_by_traveller'));

        return redirect()->route('site.dashboard.bookings', ['locale' => $locale])->with('status', __('account.bookings.cancelled_notice'));
    }
}
