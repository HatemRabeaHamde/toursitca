<?php

namespace App\Domain\Discover\ViewModels;

/** A destination guide (Marrakech, Fès…) ready for Blade. No queries happen here. */
final readonly class DestinationPageViewModel
{
    /**
     * @param array<string, mixed> $guide  translated page copy (hero, facts, overview…)
     * @param list<array{label: string, href: ?string}> $breadcrumb
     * @param list<array<string, mixed>> $landmarks
     * @param list<array<string, mixed>> $crafts
     * @param list<array<string, mixed>> $dishes
     * @param list<array<string, mixed>> $tips
     * @param list<array<string, mixed>> $experiences
     * @param list<array<string, mixed>> $stories
     * @param list<array<string, mixed>> $hosts
     */
    public function __construct(
        public string $name,
        public string $region,
        public string $heroImage,
        public array $guide,
        public array $breadcrumb,
        public array $landmarks,
        public array $crafts,
        public array $dishes,
        public array $tips,
        public array $experiences,
        public array $stories,
        public array $hosts,
        public string $experiencesUrl,
        public string $guideUrl,
    ) {}

    public function copy(string $path, mixed $default = null): mixed
    {
        return data_get($this->guide, $path, $default);
    }

    /**
     * Chapters of the guide that actually have content, in page order —
     * drives both the main column and the sticky "In this guide" nav.
     *
     * @return list<string>
     */
    public function chapters(): array
    {
        return array_values(array_filter([
            $this->copy('overview') ? 'overview' : null,
            $this->landmarks ? 'landmarks' : null,
            $this->copy('culture') ? 'culture' : null,
            $this->dishes ? 'food' : null,
            $this->experiences ? 'experiences' : null,
            $this->stories ? 'stories' : null,
            $this->hosts ? 'hosts' : null,
            $this->tips ? 'tips' : null,
        ]));
    }

    /** @return list<array{id: string, label: string}> links of the sticky "In this guide" nav */
    public function tocLinks(): array
    {
        return array_map(
            fn (string $id): array => ['id' => $id, 'label' => __('discover.destination.chapters.'.$id)],
            $this->chapters(),
        );
    }

    /** @return list<array{label: string, value: string}> */
    public function facts(): array
    {
        return array_values(array_filter([
            $this->region ? ['label' => __('discover.destination.facts.region'), 'value' => $this->region] : null,
            ...collect(['known_for', 'best_for', 'base_for'])
                ->map(fn (string $key): ?array => $this->copy('facts.'.$key)
                    ? ['label' => __('discover.destination.facts.'.$key), 'value' => (string) $this->copy('facts.'.$key)]
                    : null)
                ->all(),
        ]));
    }
}
