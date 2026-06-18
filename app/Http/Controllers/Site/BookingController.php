<?php

namespace App\Http\Controllers\Site;

use App\Domain\Booking\Models\Booking;
use App\Domain\Checkout\Actions\CreateCheckoutSessionAction;
use App\Domain\Checkout\Actions\UpdateCheckoutContactAction;
use App\Domain\Experience\Models\Experience;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\BookingStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(string $locale, Experience $experience): View
    {
        abort_unless($experience->status === 'published', 404);

        $experience->load('agency');

        $availabilities = $experience->availabilities()
            ->where('is_active', true)
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('time_slot')
            ->get();

        return view('site.bookings.create', compact('experience', 'availabilities'));
    }

    public function store(
        BookingStoreRequest $request,
        string $locale,
        Experience $experience,
        CreateCheckoutSessionAction $createCheckoutSessionAction,
        UpdateCheckoutContactAction $updateCheckoutContactAction,
    ): RedirectResponse {
        $checkoutSession = $createCheckoutSessionAction->execute(
            experience: $experience,
            bookingType: $request->string('booking_type')->toString(),
            participants: ['adult' => $request->integer('participants_count')],
            availabilityId: $request->integer('availability_id'),
            optionId: null,
            languageCode: null,
            userId: $request->user()?->id,
            sessionId: $request->session()->getId(),
            locale: $locale,
            paymentMethod: 'manual',
        );

        $nameParts = preg_split('/\s+/', trim($request->string('guest_name')->toString()), 2);

        $updateCheckoutContactAction->execute($checkoutSession, [
            'contact_first_name' => $nameParts[0] ?? $request->string('guest_name')->toString(),
            'contact_last_name' => $nameParts[1] ?? '-',
            'contact_email' => $request->string('guest_email')->toString(),
            'contact_phone' => $request->string('guest_phone')->toString() ?: null,
            'contact_country' => null,
            'special_requests' => $request->string('special_notes')->toString() ?: null,
        ]);

        return redirect()
            ->route('site.checkout.activity', [
                'locale' => app()->getLocale(),
                'checkoutSession' => $checkoutSession,
            ])
            ->with('status', __('ui.messages.checkout_started'));
    }

    public function confirmation(Request $request, string $locale, Booking $booking): View
    {
        $canView = (int) $request->session()->get('last_booking_id') === $booking->id
            || ($request->user() && $booking->user_id === $request->user()->id);

        abort_unless($canView, 403);

        $booking->load(['experience.agency', 'availability']);

        return view('site.bookings.confirmation', compact('booking'));
    }
}
