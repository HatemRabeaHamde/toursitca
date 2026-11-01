<?php

namespace App\Domain\Discover\ViewModels;

/** An interest guide (Cuisine, Nature…) ready for Blade. No queries happen here. */
final readonly class InterestPageViewModel
{
    /**
     * @param array<string, mixed> $guide
     * @param list<array{label: string, href: ?string}> $breadcrumb
     * @param list<array<string, mixed>> $origins
     * @param list<array<string, mixed>> $dishes
     * @param array<string, mixed>|null $also
     * @param list<array<string, mixed>> $notes
     * @param list<array<string, mixed>> $regions
     * @param list<array<string, mixed>> $experiences
     * @param list<array<string, mixed>> $stories
     * @param list<array<string, mixed>> $hosts
     */
    public function __construct(
        public string $name,
        public string $heroImage,
        public array $guide,
        public array $breadcrumb,
        public array $origins,
        public array $dishes,
        public ?array $also,
        public array $notes,
        public array $regions,
        public array $experiences,
        public array $stories,
        public array $hosts,
        public string $experiencesUrl,
        public string $destinationsUrl,
        public string $storiesUrl,
    ) {}

    public function copy(string $path, mixed $default = null): mixed
    {
        return data_get($this->guide, $path, $default);
    }
}
