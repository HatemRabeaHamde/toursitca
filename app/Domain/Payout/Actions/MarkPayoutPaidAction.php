<?php

namespace App\Domain\Payout\Actions;

use App\Domain\Payout\Models\Payout;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class MarkPayoutPaidAction
{
    public function execute(Payout $payout, User $admin, ?string $notes = null): Payout
    {
        abort_unless($admin->hasRole('admin'), 403);

        return DB::transaction(function () use ($payout, $admin, $notes): Payout {
            $locked = Payout::query()->lockForUpdate()->findOrFail($payout->id);
            if ($locked->status === 'paid') {
                return $locked;
            }
            if ($locked->status !== 'pending' || $locked->booking->status === 'cancelled') {
                throw ValidationException::withMessages(['payout' => __('booking_flow.validation.payout_cancelled')]);
            }
            $locked->forceFill(['status' => 'paid', 'transferred_at' => now(), 'transferred_by' => $admin->id, 'notes' => $notes])->save();

            return $locked->refresh();
        });
    }
}
