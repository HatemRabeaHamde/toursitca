<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Experience;
use InvalidArgumentException;

final class PublishExperienceAction
{
    public function execute(Experience $experience): Experience
    {
        if ($experience->images()->count() === 0 && $experience->thumbnail === null) {
            throw new InvalidArgumentException('Experience must have at least one image before publishing.');
        }

        $experience->forceFill(['status' => 'published'])->save();

        return $experience->refresh();
    }
}
