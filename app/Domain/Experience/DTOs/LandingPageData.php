<?php

namespace App\Domain\Experience\DTOs;

use Illuminate\Support\Collection;

final readonly class LandingPageData
{
    public function __construct(
        public array $stats,
        public Collection $trendingCards,
        public Collection $newestCards,
        public Collection $topRatedCards,
        public Collection $dealCards,
        public Collection $categories,
        public Collection $destinations,
        public Collection $topAgencies,
        public Collection $faqs,
        public Collection $travelReels,
        public Collection $testimonials,
    ) {}
}
