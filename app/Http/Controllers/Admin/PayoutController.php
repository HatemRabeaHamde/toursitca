<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payout\Actions\MarkPayoutPaidAction;
use App\Domain\Payout\Models\Payout;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $payouts = Payout::query()
            ->with(['agency', 'booking.experience'])
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $totals = [
            'pending' => Payout::where('status', 'pending')->sum('amount'),
            'paid'    => Payout::where('status', 'paid')->sum('amount'),
        ];

        return view('admin.payouts.index', compact('payouts', 'status', 'totals'));
    }

    public function markPaid(Payout $payout, Request $request, MarkPayoutPaidAction $action): RedirectResponse
    {
        $notes = $request->string('notes')->toString() ?: null;
        $action->execute($payout, $request->user(), $notes);

        return redirect()->back()->with('success', __('ui.messages.payout_marked_paid'));
    }
}
