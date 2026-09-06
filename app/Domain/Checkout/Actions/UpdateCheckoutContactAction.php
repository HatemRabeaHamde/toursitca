<?php

namespace App\Domain\Checkout\Actions;

use App\Domain\Checkout\Models\CheckoutSession;
use Illuminate\Validation\ValidationException;

final class UpdateCheckoutContactAction
{
    public function execute(CheckoutSession $checkoutSession, array $payload): CheckoutSession
    {
        if (! $checkoutSession->isActive()) {
            throw ValidationException::withMessages([
                'checkout_session' => __('ui.messages.checkout_session_expired'),
            ]);
        }

        $checkoutSession->fill([
            'contact_first_name' => $payload['contact_first_name'],
            'contact_last_name' => $payload['contact_last_name'],
            'contact_email' => $payload['contact_email'],
            'contact_phone' => $payload['contact_phone'] ?? null,
            'contact_country' => $payload['contact_country'] ?? null,
            'special_requests' => $payload['special_requests'] ?? null,
        ]);
        $checkoutSession->save();

        return $checkoutSession->refresh();
    }
}
