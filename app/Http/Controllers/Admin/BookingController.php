<?php

namespace App\Http\Controllers\Admin;

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
        $status = $request->string('status')->toString();
        $paymentStatus = $request->string('payment_status')->toString();

        $bookings = Booking::query()
            ->with(['experience.agency', 'availability'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($paymentStatus !== '', fn ($query) => $query->where('payment_status', $paymentStatus))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings', 'status', 'paymentStatus'));
    }

    public function confirm(Booking $booking, ConfirmBookingAction $confirmBookingAction): RedirectResponse
    {
        try {
            $confirmBookingAction->execute($booking);
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('admin.bookings.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', __('ui.messages.booking_confirmed'));
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
