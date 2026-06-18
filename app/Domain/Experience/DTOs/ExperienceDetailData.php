<?php

namespace App\Domain\Experience\DTOs;

use App\Domain\Experience\Models\Experience;
use Illuminate\Support\Collection;

final readonly class ExperienceDetailData
{
    public function __construct(
        public Experience $experience,
        public string $title,
        public string $description,
        public string $providerName,
        public array $gallery,
        public array $badges,
        public array $features,
        public array $trustItems,
        public array $reviewSummary,
        public array $agencySummary,
        public array $bookingSummary,
        public array $experienceOptions,
        public Collection $recommendations,
        public string $bookingUrl,
    ) {}
}
