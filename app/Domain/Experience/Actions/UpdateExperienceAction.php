<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\DTOs\ExperienceFormData;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceDraftService;
use App\Domain\Experience\Services\ExperienceItineraryService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final class UpdateExperienceAction
{
    public function __construct(
        private readonly ExperienceItineraryService $experienceItineraryService,
        private readonly ExperienceDraftService $drafts,
    ) {}

    public function execute(Experience $experience, ExperienceFormData $data): Experience
    {
        if ($experience->status === 'published' || $experience->changeRequests()->exists()) {
            $this->drafts->mutate($experience, function (array $payload) use ($data): array {
                $payload['attributes'] = array_replace($payload['attributes'], Arr::except($data->toModelAttributes(), ['created_by', 'status']));
                $payload['itinerary'] = $data->itinerary;

                return $payload;
            });

            return $experience->refresh();
        }

        return DB::transaction(function () use ($experience, $data): Experience {
            $experience->fill(Arr::except($data->toModelAttributes(), ['created_by']));
            $experience->save();

            $this->experienceItineraryService->sync($experience, $data->itinerary);

            return $experience->refresh();
        });
    }
}
