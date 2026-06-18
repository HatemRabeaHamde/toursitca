<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\DTOs\ExperienceFormData;
use App\Domain\Experience\Models\Experience;

final class CreateExperienceAction
{
    public function execute(ExperienceFormData $data): Experience
    {
        return Experience::query()->create($data->toModelAttributes());
    }
}
