<?php

namespace App\Domain\Booking\Actions;

use App\Domain\Booking\Models\Booking;
use Illuminate\Validation\ValidationException;

final class UpdateBookingPaymentStatusAction
{
    public const STATUSES = ['pending', 'not_required', 'paid', 'failed', 'cancelled'];

    public function execute(Booking $booking, string $paymentStatus): Booking
    {
        if (! in_array($paymentStatus, self::STATUSES, true)) {
            throw ValidationException::withMessages([
                'payment_status' => __('validation.in', ['attribute' => __('ui.fields.payment_status')]),
            ]);
        }

        $booking->forceFill(['payment_status' => $paymentStatus])->save();

        return $booking->refresh();
    }
}
