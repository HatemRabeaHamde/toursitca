<?php

namespace App\Http\Controllers\Agency;

use App\Domain\Booking\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $agencyId = (int) $request->user()->agency->id;

        $bookings = Booking::query()
            ->with(['experience', 'availability'])
            ->whereHas('experience', fn ($query) => $query->where('agency_id', $agencyId))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('agency.bookings.index', compact('bookings', 'status'));
    }
}
