<?php

namespace App\Http\Controllers\Site;

use App\Domain\Booking\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TouristBookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->with(['experience.agency', 'availability', 'review'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('site.dashboard.bookings', compact('bookings'));
    }
}
