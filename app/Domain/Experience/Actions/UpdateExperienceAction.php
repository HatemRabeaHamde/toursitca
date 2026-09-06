<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\DTOs\ExperienceFormData;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceItineraryService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final class UpdateExperienceAction
{
    public function __construct(
        private readonly ExperienceItineraryService $experienceItineraryService,
    ) {}

    public function execute(Experience $experience, ExperienceFormData $data): Experience
    {
        return DB::transaction(function () use ($experience, $data): Experience {
            $experience->fill(Arr::except($data->toModelAttributes(), ['created_by']));
            $experience->save();

            $this->experienceItineraryService->sync($experience, $data->itinerary);

            return $experience->refresh();
        });
    }
}
