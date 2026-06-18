<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Experience;

final class UnpublishExperienceAction
{
    public function execute(Experience $experience): Experience
    {
        $experience->forceFill(['status' => 'unpublished'])->save();

        return $experience->refresh();
    }
}
