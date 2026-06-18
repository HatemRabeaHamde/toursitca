<?php

namespace App\Domain\Experience\DTOs;

final readonly class ExperienceSearchData
{
    /**
     * @param  list<string>  $categories  Selected category slugs (multi-select).
     */
    public function __construct(
        public ?string $search = null,
        public ?string $city = null,
        public array $categories = [],
        public ?string $date = null,
        public ?int $participants = null,
        public ?string $minPrice = null,
        public ?string $maxPrice = null,
        public bool $privateAvailable = false,
        public bool $pickupAvailable = false,
        public ?string $duration = null,
        public string $sort = 'recommended',
    ) {}
}
