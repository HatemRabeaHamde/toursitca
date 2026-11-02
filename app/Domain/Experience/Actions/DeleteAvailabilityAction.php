<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Services\AvailabilityMutationService;
use InvalidArgumentException;

final class DeleteAvailabilityAction
{
    public function __construct(private readonly AvailabilityMutationService $mutations) {}

    public function execute(Availability $availability): void
    {
        if ($availability->bookings()->exists()) {
            throw new InvalidArgumentException('Availability with bookings cannot be deleted.');
        }

        $this->mutations->delete($availability);
    }
}
