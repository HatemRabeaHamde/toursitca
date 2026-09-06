<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Experience;
use InvalidArgumentException;

final class DeleteExperienceAction
{
    public function execute(Experience $experience): void
    {
        if ($experience->bookings()->exists()) {
            throw new InvalidArgumentException(__('ui.messages.experience_has_bookings'));
        }

        $experience->delete();
    }
}
