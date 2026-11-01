<?php

namespace App\Domain\Discover\ViewModels;

use App\Domain\Discover\Services\DiscoverUrls;

/** Everything the Discover hub Blade sections render. No queries happen here. */
final readonly class DiscoverPageViewModel
{
    /**
     * @param list<array<string, mixed>> $cities
     * @param list<array{0: string, 1: string}> $mapRoutes
     * @param list<array<string, mixed>> $interests
     * @param list<array<string, mixed>> $stories
     * @param list<array<string, mixed>> $experiences
     * @param list<array<string, mixed>> $hosts
     * @param list<array{question: string, answer: string}> $faqs
     */
    public function __construct(
        public DiscoverUrls $urls,
        public array $cities,
        public array $mapRoutes,
        public array $interests,
        public array $stories,
        public array $experiences,
        public array $hosts,
        public array $faqs,
    ) {}

    /** The city shown as selected on first paint (the first one). */
    public function initialCity(): ?string
    {
        return $this->cities[0]['slug'] ?? null;
    }

    /**
     * Cities that have a map pin, plus where their preview card opens so it
     * never leaves the map (above the pin near the bottom, leftwards near the right edge).
     *
     * @return list<array<string, mixed>>
     */
    public function mapPins(): array
    {
        return collect($this->cities)
            ->filter(fn (array $city): bool => $city['x'] !== null && $city['y'] !== null)
            ->map(fn (array $city): array => $city + [
                'opens_above' => $city['y'] > 62,
                'opens_start' => $city['x'] > 64,
            ])
            ->values()
            ->all();
    }

    /**
     * Dashed map lines between pins that both exist.
     *
     * @return list<array{x1: float, y1: float, x2: float, y2: float}>
     */
    public function mapLines(): array
    {
        $pins = collect($this->cities)->filter(fn (array $city): bool => $city['x'] !== null)->keyBy('slug');

        return collect($this->mapRoutes)
            ->filter(fn (array $route): bool => $pins->has($route[0]) && $pins->has($route[1]))
            ->map(fn (array $route): array => [
                'x1' => $pins[$route[0]]['x'], 'y1' => $pins[$route[0]]['y'],
                'x2' => $pins[$route[1]]['x'], 'y2' => $pins[$route[1]]['y'],
            ])
            ->values()
            ->all();
    }
}
