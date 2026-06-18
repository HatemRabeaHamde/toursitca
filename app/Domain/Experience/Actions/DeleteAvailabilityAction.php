<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Availability;
use InvalidArgumentException;

final class DeleteAvailabilityAction
{
    public function execute(Availability $availability): void
    {
        if ($availability->bookings()->exists()) {
            throw new InvalidArgumentException('Availability with bookings cannot be deleted.');
        }

        $availability->delete();
    }
}
