<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Availability;

final class UpdateAvailabilityAction
{
    public function execute(Availability $availability, array $payload): Availability
    {
        $availability->fill($payload);
        $availability->save();

        return $availability->refresh();
    }
}
