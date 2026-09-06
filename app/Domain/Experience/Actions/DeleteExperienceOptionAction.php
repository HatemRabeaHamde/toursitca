<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\ExperienceOption;
use InvalidArgumentException;

final class DeleteExperienceOptionAction
{
    public function execute(ExperienceOption $option): void
    {
        if ($option->availabilities()->exists()) {
            throw new InvalidArgumentException(__('ui.messages.option_has_availability'));
        }

        $option->delete();
    }
}
