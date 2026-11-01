<?php

namespace App\Http\Controllers\Site;

use App\Domain\Booking\Services\BookingTripPresenter;
use App\Domain\Checkout\Actions\AttachCheckoutSessionToUserAction;
use App\Domain\Checkout\Actions\ConfirmCheckoutSessionAction;
use App\Domain\Checkout\Actions\CreateCheckoutSessionAction;
use App\Domain\Checkout\Actions\RegisterCheckoutUserAction;
use App\Domain\Checkout\Actions\UpdateCheckoutContactAction;
use App\Domain\Checkout\Actions\UpdateCheckoutPickupAction;
use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Experience\Models\Experience;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\CheckoutContactRequest;
use App\Http\Requests\Site\CheckoutLoginRequest;
use App\Http\Requests\Site\CheckoutPickupRequest;
use App\Http\Requests\Site\CheckoutRegisterRequest;
use App\Http\Requests\Site\CheckoutStartRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    private const TRIP_RELATIONS = ['experience.media', 'experience.host', 'experience.agency', 'availability', 'option'];

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
            childAges: $request->filled('child_ages') ? array_map('intval', (array) $request->input('child_ages')) : null,
        );

        return response()->json([
            'data' => [
                'uuid' => $checkoutSession->uuid,
                'status' => $checkoutSession->status,
                'reserved_until' => $checkoutSession->reserved_until->toISOString(),
                'request_url' => route($checkoutSession->needsPickupStep() ? 'site.checkout.activity' : 'site.checkout.contact', [
                    'locale' => $locale,
                    'checkoutSession' => $checkoutSession->uuid,
                ]),
            ],
        ], 201);
    }

    public function activity(Request $request, string $locale, CheckoutSession $checkoutSession, BookingTripPresenter $trips): View
    {
        $this->authorizeCheckoutSession($request, $checkoutSession);
        $checkoutSession->load(self::TRIP_RELATIONS);

        return view('site.checkout.activity', ['checkoutSession' => $checkoutSession, 'trip' => $trips->fromCheckout($checkoutSession)]);
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

    public function contact(Request $request, string $locale, CheckoutSession $checkoutSession, BookingTripPresenter $trips): View
    {
        $this->authorizeCheckoutSession($request, $checkoutSession);
        $checkoutSession->load(self::TRIP_RELATIONS);

        return view('site.checkout.request', ['checkoutSession' => $checkoutSession, 'trip' => $trips->fromCheckout($checkoutSession)]);
    }

    public function login(
        CheckoutLoginRequest $request,
        string $locale,
        CheckoutSession $checkoutSession,
        AttachCheckoutSessionToUserAction $action,
    ): RedirectResponse {
        $this->authorizeCheckoutSession($request, $checkoutSession);
        $request->authenticate();
        $request->session()->regenerate();
        $action->execute($checkoutSession, (int) Auth::id(), $request->session()->getId());

        return redirect()->route('site.checkout.contact', ['locale' => $locale, 'checkoutSession' => $checkoutSession]);
    }

    public function register(
        CheckoutRegisterRequest $request,
        string $locale,
        CheckoutSession $checkoutSession,
        RegisterCheckoutUserAction $registerAction,
        AttachCheckoutSessionToUserAction $attachAction,
    ): RedirectResponse {
        $this->authorizeCheckoutSession($request, $checkoutSession);
        Auth::login($registerAction->execute($request->validated()));
        $request->session()->regenerate();
        $attachAction->execute($checkoutSession, (int) Auth::id(), $request->session()->getId());

        return redirect()->route('site.checkout.contact', ['locale' => $locale, 'checkoutSession' => $checkoutSession]);
    }

    public function storeContact(
        CheckoutContactRequest $request,
        string $locale,
        CheckoutSession $checkoutSession,
        UpdateCheckoutContactAction $contactAction,
        ConfirmCheckoutSessionAction $confirmAction,
    ): RedirectResponse {
        $this->authorizeCheckoutSession($request, $checkoutSession);
        $this->requireAuthenticatedCheckout($request, $locale, $checkoutSession);

        $contactAction->execute($checkoutSession, $request->validated());
        $booking = $confirmAction->execute($checkoutSession);
        $request->session()->put('last_booking_id', $booking->id);

        return redirect()->route('site.bookings.confirmation', ['locale' => $locale, 'booking' => $booking]);
    }

    private function authorizeCheckoutSession(Request $request, CheckoutSession $checkoutSession): void
    {
        $ownsSession = $checkoutSession->user_id !== null
            ? $request->user()?->id === $checkoutSession->user_id
            : $request->session()->getId() === $checkoutSession->session_id;

        abort_unless($ownsSession, 403);
    }

    private function requireAuthenticatedCheckout(Request $request, string $locale, CheckoutSession $checkoutSession): void
    {
        if ($request->user() !== null) {
            return;
        }

        throw new HttpResponseException(
            redirect()
                ->route('site.checkout.contact', ['locale' => $locale, 'checkoutSession' => $checkoutSession])
                ->withErrors(['auth' => __('ui.messages.checkout_auth_required')])
        );
    }
}
