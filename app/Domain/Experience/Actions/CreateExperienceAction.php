<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\DTOs\ExperienceFormData;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceItineraryService;
use Illuminate\Support\Facades\DB;

final class CreateExperienceAction
{
    public function __construct(
        private readonly ExperienceItineraryService $experienceItineraryService,
    ) {}

    public function execute(ExperienceFormData $data): Experience
    {
        return DB::transaction(function () use ($data): Experience {
            $experience = Experience::query()->create($data->toModelAttributes());

            $this->experienceItineraryService->sync($experience, $data->itinerary);

            return $experience;
        });
    }
}
