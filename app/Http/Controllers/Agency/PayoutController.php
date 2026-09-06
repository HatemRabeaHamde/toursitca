<?php

namespace App\Http\Controllers\Agency;

use App\Domain\Payout\Models\Payout;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function index(Request $request): View
    {
        $agencyId = (int) $request->user()->agency->id;
        $status   = $request->string('status')->toString();

        $payouts = Payout::query()
            ->with('booking.experience')
            ->where('agency_id', $agencyId)
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totals = [
            'pending' => Payout::where('agency_id', $agencyId)->where('status', 'pending')->sum('amount'),
            'paid'    => Payout::where('agency_id', $agencyId)->where('status', 'paid')->sum('amount'),
        ];

        return view('agency.payouts.index', compact('payouts', 'status', 'totals'));
    }
}
