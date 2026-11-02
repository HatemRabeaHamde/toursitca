<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Services\AvailabilityMutationService;

final class UpdateAvailabilityAction
{
    public function __construct(private readonly AvailabilityMutationService $mutations) {}

    public function execute(Availability $availability, array $payload): Availability
    {
        return $this->mutations->update($availability, $payload);
    }
}
