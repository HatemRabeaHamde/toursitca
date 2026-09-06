<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Booking\Actions\CancelBookingAction;
use App\Domain\Booking\Actions\ConfirmBookingAction;
use App\Domain\Booking\Actions\UpdateBookingPaymentStatusAction;
use App\Domain\Booking\Models\Booking;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookingPaymentStatusRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $status        = $request->string('status')->toString();
        $paymentStatus = $request->string('payment_status')->toString();

        $bookings = Booking::query()
            ->with(['experience.agency', 'availability'])
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($paymentStatus !== '', fn ($q) => $q->where('payment_status', $paymentStatus))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'pending'   => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'status', 'paymentStatus', 'counts'));
    }

    public function show(Booking $booking): View
    {
        $booking->load(['experience.agency', 'availability', 'user', 'payout']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking, ConfirmBookingAction $action): RedirectResponse
    {
        try {
            $action->execute($booking);
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('admin.bookings.index')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', __('ui.messages.booking_confirmed'));
    }

    public function cancel(Booking $booking, CancelBookingAction $action): RedirectResponse
    {
        try {
            $action->execute($booking, 'Cancelled by admin');
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('admin.bookings.index')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', __('ui.messages.booking_cancelled'));
    }

    public function updatePaymentStatus(
        BookingPaymentStatusRequest $request,
        Booking $booking,
        UpdateBookingPaymentStatusAction $action,
    ): RedirectResponse {
        $action->execute($booking, $request->string('payment_status')->toString());

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', __('ui.messages.payment_status_updated'));
    }
}
