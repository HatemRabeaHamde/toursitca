<?php

namespace App\Domain\Experience\Services;

use App\Domain\Experience\Models\Experience;

final class ExperienceSearchNavService
{
    public function data(?string $destination = null): array
    {
        $locale = app()->getLocale();
        $cities = Experience::query()
            ->published()
            ->select('location_city')
            ->distinct()
            ->orderBy('location_city')
            ->pluck('location_city')
            ->filter()
            ->values()
            ->all();

        $suggestions = Experience::query()
            ->published()
            ->selectRaw('category, COUNT(*) as total')
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(7)
            ->get()
            ->map(fn ($row): array => [
                'label' => __("ui.landing.category_names.{$row->category}"),
                'category' => $row->category,
                'count' => (int) $row->total,
                'city' => $destination,
                'url' => route('site.experiences.index', array_filter([
                    'locale' => $locale,
                    'city' => $destination,
                    'category' => [$row->category],
                ], fn ($value): bool => $value !== null && $value !== '')),
            ])
            ->values()
            ->all();

        return [
            'cities' => $cities,
            'destination' => $destination,
            'max_participants' => (int) config('booking.max_participants'),
            'suggestions' => $suggestions,
        ];
    }
}
