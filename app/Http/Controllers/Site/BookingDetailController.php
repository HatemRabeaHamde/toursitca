<?php

namespace App\Http\Controllers\Site;

use App\Domain\Booking\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BookingDetailController extends Controller
{
    public function show(Request $request, string $locale, Booking $booking): View
    {
        abort_unless($booking->user_id === $request->user()->id, 403);

        $booking->load(['experience.agency', 'availability', 'review']);

        return view('site.dashboard.booking-show', compact('booking'));
    }
}
