<?php

namespace App\Domain\Experience\DTOs;

final readonly class ExperienceCardData
{
    public function __construct(
        public int $id,
        public ?string $slug,
        public string $title,
        public string $city,
        public string $category,
        public ?string $agencyName,
        public ?string $thumbnailUrl,
        public string $durationLabel,
        public string $ratingAvg,
        public int $reviewsCount,
        public array $badges,
        public array $features,
        /** @var list<string> Short descriptive chips shown on the card. */
        public array $tags,
        public string $priceFrom,
        public ?string $originalPrice,
        public string $currency,
        public bool $isPrivateAvailable,
        public bool $isPickupAvailable,
        public bool $isDeal,
        public ?\DateTimeInterface $dealEndsAt,
        /** @var 'available'|'few_spots'|'sold_out' */
        public string $availabilityStatus,
        public ?int $spotsLeft,
        public string $showUrl,
    ) {}
}
