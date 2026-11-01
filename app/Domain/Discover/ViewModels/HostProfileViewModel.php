<?php

namespace App\Domain\Discover\ViewModels;

/** Host profile (Figma 06) ready for Blade. Sections with no data stay empty and are hidden. */
final readonly class HostProfileViewModel
{
    /**
     * @param  list<array{label: string, href: ?string}>  $breadcrumb
     * @param  list<string>  $story
     * @param  list<string>  $skills
     * @param  list<array<string, mixed>>  $experiences
     * @param  array{average: string, count: int, breakdown: list<array{stars: int, percent: int}>, cards: list<array<string, mixed>>, href: string}|null  $reviews
     * @param  list<array{icon: string, title: string, body: ?string}>  $trust
     * @param  list<array<string, mixed>>  $moreHosts
     */
    public function __construct(
        public string $name,
        public string $role,
        public ?string $quote,
        public ?string $photo,
        public string $cover,
        public ?string $place,
        public ?string $languages,
        public array $breadcrumb,
        public array $story,
        public array $skills,
        public array $experiences,
        public ?array $reviews,
        public array $trust,
        public array $moreHosts,
        public string $hostsUrl,
        public string $vettingUrl,
    ) {}

    /** @return array<string, mixed>|null */
    public function featured(): ?array
    {
        return $this->experiences[0] ?? null;
    }
}
