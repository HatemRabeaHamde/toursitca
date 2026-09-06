<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Availability;

final class CreateAvailabilityAction
{
    public function execute(array $payload): Availability
    {
        return Availability::query()->create($payload);
    }
}
