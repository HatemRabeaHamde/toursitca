<?php

namespace App\Domain\Experience\Services;

use App\Domain\Experience\Models\Experience;
use Illuminate\Support\Arr;

final class ExperienceItineraryService
{
    public function sync(Experience $experience, array $items): void
    {
        $experience->itineraryItems()->delete();

        collect($items)
            ->filter(fn (array $item): bool => $this->hasContent($item))
            ->values()
            ->each(function (array $item, int $index) use ($experience): void {
                $experience->itineraryItems()->create([
                    'sort_order' => (int) ($item['sort_order'] ?? ($index + 1)),
                    'type' => $item['type'] ?? 'stop',
                    'title' => $item['title'],
                    'description' => $this->emptyLocalizedToNull($item['description'] ?? []),
                    'duration_minutes' => filled($item['duration_minutes'] ?? null) ? (int) $item['duration_minutes'] : null,
                    'location_name' => $this->emptyLocalizedToNull($item['location_name'] ?? []),
                    'location_lat' => filled($item['location_lat'] ?? null) ? $item['location_lat'] : null,
                    'location_lng' => filled($item['location_lng'] ?? null) ? $item['location_lng'] : null,
                    'is_main_stop' => (bool) ($item['is_main_stop'] ?? false),
                    'icon' => filled($item['icon'] ?? null) ? $item['icon'] : null,
                ]);
            });
    }

    private function hasContent(array $item): bool
    {
        return filled(Arr::get($item, 'title.en'))
            || filled(Arr::get($item, 'location_name.en'))
            || filled(Arr::get($item, 'description.en'));
    }

    private function emptyLocalizedToNull(array $value): ?array
    {
        $filtered = collect($value)
            ->filter(fn ($text): bool => filled($text))
            ->all();

        return $filtered === [] ? null : $value;
    }
}
