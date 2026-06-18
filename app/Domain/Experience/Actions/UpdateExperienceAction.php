<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\DTOs\ExperienceFormData;
use App\Domain\Experience\Models\Experience;
use Illuminate\Support\Arr;

final class UpdateExperienceAction
{
    public function execute(Experience $experience, ExperienceFormData $data): Experience
    {
        $experience->fill(Arr::except($data->toModelAttributes(), ['created_by']));
        $experience->save();

        return $experience->refresh();
    }
}
