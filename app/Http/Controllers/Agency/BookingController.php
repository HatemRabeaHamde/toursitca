<?php

namespace App\Http\Controllers\Agency;

use App\Domain\Booking\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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

    public function show(Request $request, Booking $booking): View
    {
        $agencyId = (int) $request->user()->agency->id;

        abort_unless(
            $booking->experience?->agency_id === $agencyId,
            403
        );

        $booking->load(['experience', 'availability', 'payout']);

        return view('agency.bookings.show', compact('booking'));
    }
}
