<?php

namespace App\Http\Controllers\Site;

use App\Domain\Checkout\Actions\ConfirmCheckoutSessionAction;
use App\Domain\Checkout\Actions\CreateCheckoutSessionAction;
use App\Domain\Checkout\Actions\UpdateCheckoutContactAction;
use App\Domain\Checkout\Actions\UpdateCheckoutPickupAction;
use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Experience\Models\Experience;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\CheckoutContactRequest;
use App\Http\Requests\Site\CheckoutPickupRequest;
use App\Http\Requests\Site\CheckoutStartRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function start(CheckoutStartRequest $request, string $locale, CreateCheckoutSessionAction $action): JsonResponse
    {
        $experience = Experience::query()->findOrFail($request->integer('experience_id'));
        abort_unless($experience->status === 'published', 404);

        $checkoutSession = $action->execute(
            experience: $experience,
            bookingType: $request->string('booking_type')->toString(),
            participants: $request->participants(),
            availabilityId: $request->integer('availability_id'),
            optionId: $request->filled('option_id') ? $request->integer('option_id') : null,
            languageCode: $request->filled('language') ? $request->string('language')->toString() : null,
            userId: $request->user()?->id,
            sessionId: $request->session()->getId(),
            locale: $locale,
            paymentMethod: $request->string('payment_method', 'manual')->toString(),
        );

        return response()->json([
            'data' => [
                'uuid' => $checkoutSession->uuid,
                'status' => $checkoutSession->status,
                'reserved_until' => $checkoutSession->reserved_until->toISOString(),
                'activity_url' => route('site.checkout.activity', [
                    'locale' => $locale,
                    'checkoutSession' => $checkoutSession->uuid,
                ]),
            ],
        ], 201);
    }

    public function activity(Request $request, string $locale, CheckoutSession $checkoutSession): View
    {
        $this->authorizeCheckoutSession($request, $checkoutSession);
        $checkoutSession->load(['experience', 'availability']);

        return view('site.checkout.activity', compact('checkoutSession'));
    }

    public function storeActivity(
        CheckoutPickupRequest $request,
        string $locale,
        CheckoutSession $checkoutSession,
        UpdateCheckoutPickupAction $action,
    ): RedirectResponse {
        $this->authorizeCheckoutSession($request, $checkoutSession);

        $action->execute(
            checkoutSession: $checkoutSession,
            pickupStatus: $request->string('pickup_status')->toString(),
            pickupAddress: $request->filled('pickup_address') ? $request->string('pickup_address')->toString() : null,
            pickupLat: $request->filled('pickup_lat') ? $request->string('pickup_lat')->toString() : null,
            pickupLng: $request->filled('pickup_lng') ? $request->string('pickup_lng')->toString() : null,
        );

        return redirect()
            ->route('site.checkout.contact', ['locale' => $locale, 'checkoutSession' => $checkoutSession])
            ->with('status', __('ui.messages.checkout_activity_saved'));
    }

    public function contact(Request $request, string $locale, CheckoutSession $checkoutSession): View
    {
        $this->authorizeCheckoutSession($request, $checkoutSession);

        return view('site.checkout.contact', compact('checkoutSession'));
    }

    public function storeContact(
        CheckoutContactRequest $request,
        string $locale,
        CheckoutSession $checkoutSession,
        UpdateCheckoutContactAction $action,
    ): RedirectResponse {
        $this->authorizeCheckoutSession($request, $checkoutSession);

        $action->execute($checkoutSession, $request->validated());

        return redirect()
            ->route('site.checkout.payment', ['locale' => $locale, 'checkoutSession' => $checkoutSession])
            ->with('status', __('ui.messages.checkout_contact_saved'));
    }

    public function payment(Request $request, string $locale, CheckoutSession $checkoutSession): View
    {
        $this->authorizeCheckoutSession($request, $checkoutSession);

        return view('site.checkout.payment', compact('checkoutSession'));
    }

    public function confirmPayment(
        Request $request,
        string $locale,
        CheckoutSession $checkoutSession,
        ConfirmCheckoutSessionAction $action,
    ): RedirectResponse {
        $this->authorizeCheckoutSession($request, $checkoutSession);

        $booking = $action->execute($checkoutSession);
        $request->session()->put('last_booking_id', $booking->id);

        return redirect()
            ->route('site.bookings.confirmation', ['locale' => $locale, 'booking' => $booking])
            ->with('status', __('ui.messages.booking_created'));
    }

    private function authorizeCheckoutSession(Request $request, CheckoutSession $checkoutSession): void
    {
        $ownsSession = $checkoutSession->user_id !== null
            ? $request->user()?->id === $checkoutSession->user_id
            : $request->session()->getId() === $checkoutSession->session_id;

        abort_unless($ownsSession, 403);
    }
}
