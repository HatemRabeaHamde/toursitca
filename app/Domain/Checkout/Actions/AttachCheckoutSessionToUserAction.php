<?php

namespace App\Domain\Checkout\Actions;

use App\Domain\Checkout\Models\CheckoutSession;
use Illuminate\Validation\ValidationException;

final class AttachCheckoutSessionToUserAction
{
    public function execute(CheckoutSession $checkoutSession, int $userId, string $sessionId): CheckoutSession
    {
        if (! $checkoutSession->isActive()) {
            throw ValidationException::withMessages([
                'checkout_session' => __('ui.messages.checkout_session_expired'),
            ]);
        }

        if ($checkoutSession->user_id !== null && (int) $checkoutSession->user_id !== $userId) {
            throw ValidationException::withMessages([
                'checkout_session' => __('ui.messages.checkout_session_owner_mismatch'),
            ]);
        }

        $checkoutSession->forceFill([
            'user_id' => $userId,
            'session_id' => $sessionId,
        ])->save();

        return $checkoutSession->refresh();
    }
}
