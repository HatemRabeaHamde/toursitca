<?php

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\ExperienceOption;
use App\Domain\Experience\Services\ExperienceDraftService;
use InvalidArgumentException;

final class DeleteExperienceOptionAction
{
    public function __construct(private readonly ExperienceDraftService $drafts) {}

    public function execute(ExperienceOption $option): void
    {
        if ($option->availabilities()->exists()) {
            throw new InvalidArgumentException(__('ui.messages.option_has_availability'));
        }

        $experience = $option->experience;
        if ($experience->status === 'published' || $experience->changeRequests()->exists()) {
            $this->drafts->mutate($experience, function (array $payload) use ($option): array {
                $payload['options'] = array_values(array_filter($payload['options'], fn (array $row): bool => $row['id'] !== $option->id));

                return $payload;
            });

            return;
        }

        $option->delete();
    }
}
