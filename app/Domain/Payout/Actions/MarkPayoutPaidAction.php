<?php

namespace App\Domain\Payout\Actions;

use App\Domain\Payout\Models\Payout;
use App\Models\User;

final class MarkPayoutPaidAction
{
    public function execute(Payout $payout, User $admin, ?string $notes = null): Payout
    {
        $payout->forceFill([
            'status'         => 'paid',
            'transferred_at' => now(),
            'transferred_by' => $admin->id,
            'notes'          => $notes,
        ])->save();

        return $payout->refresh();
    }
}
