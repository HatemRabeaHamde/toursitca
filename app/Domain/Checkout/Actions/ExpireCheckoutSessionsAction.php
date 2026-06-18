<?php

namespace App\Domain\Checkout\Actions;

use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Experience\Models\Availability;
use Illuminate\Support\Facades\DB;

final class ExpireCheckoutSessionsAction
{
    public function execute(): int
    {
        $expiredCount = 0;

        CheckoutSession::query()
            ->where('status', 'active')
            ->where('reserved_until', '<=', now())
            ->orderBy('id')
            ->chunkById(100, function ($checkoutSessions) use (&$expiredCount): void {
                foreach ($checkoutSessions as $checkoutSession) {
                    DB::transaction(function () use ($checkoutSession, &$expiredCount): void {
                        $lockedSession = CheckoutSession::query()
                            ->whereKey($checkoutSession->id)
                            ->lockForUpdate()
                            ->first();

                        if (! $lockedSession || $lockedSession->status !== 'active' || $lockedSession->reserved_until->isFuture()) {
                            return;
                        }

                        $availability = Availability::query()
                            ->whereKey($lockedSession->availability_id)
                            ->lockForUpdate()
                            ->first();

                        if ($availability) {
                            $availability->forceFill([
                                'held_seats' => max(0, $availability->held_seats - $lockedSession->charged_seats),
                            ])->save();
                        }

                        $lockedSession->forceFill([
                            'status' => 'expired',
                            'payment_status' => $lockedSession->payment_status === 'pending' ? 'cancelled' : $lockedSession->payment_status,
                        ])->save();

                        $expiredCount++;
                    });
                }
            });

        return $expiredCount;
    }
}
