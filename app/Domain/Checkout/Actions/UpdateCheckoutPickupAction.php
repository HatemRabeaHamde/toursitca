<?php

namespace App\Domain\Checkout\Actions;

use App\Domain\Checkout\Models\CheckoutSession;
use Illuminate\Validation\ValidationException;

final class UpdateCheckoutPickupAction
{
    public function execute(
        CheckoutSession $checkoutSession,
        string $pickupStatus,
        ?string $pickupAddress = null,
        ?string $pickupLat = null,
        ?string $pickupLng = null,
    ): CheckoutSession {
        if (! $checkoutSession->isActive()) {
            throw ValidationException::withMessages([
                'checkout_session' => __('ui.messages.checkout_session_expired'),
            ]);
        }

        if ($pickupStatus === 'add_now' && (blank($pickupLat) || blank($pickupLng))) {
            throw ValidationException::withMessages([
                'pickup_lat' => __('validation.required', ['attribute' => __('ui.fields.selected_coordinates')]),
            ]);
        }

        $checkoutSession->fill([
            'pickup_status' => $pickupStatus,
            'pickup_address' => $pickupStatus === 'add_now' ? $pickupAddress : null,
            'pickup_lat' => $pickupStatus === 'add_now' ? $pickupLat : null,
            'pickup_lng' => $pickupStatus === 'add_now' ? $pickupLng : null,
        ]);
        $checkoutSession->save();

        return $checkoutSession->refresh();
    }
}
