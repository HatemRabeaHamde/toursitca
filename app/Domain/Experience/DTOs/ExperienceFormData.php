<?php

namespace App\Domain\Experience\DTOs;

final readonly class ExperienceFormData
{
    public function __construct(
        public int $agencyId,
        public int $createdBy,
        public array $title,
        public array $description,
        public string $category,
        public string $difficulty,
        public string $durationHours,
        public int $maxGroupSize,
        public string $pricePerPerson,
        public ?string $originalPrice,
        public ?string $dealStartsAt,
        public ?string $dealEndsAt,
        public ?string $privatePrice,
        public bool $pickupEnabled,
        public bool $isTopRated,
        public string $locationCity,
        public ?string $locationLat = null,
        public ?string $locationLng = null,
        public ?string $meetingPoint = null,
        public ?array $inclusions = null,
        public ?array $exclusions = null,
        public string $status = 'draft',
    ) {}

    public function toModelAttributes(): array
    {
        return [
            'agency_id' => $this->agencyId,
            'created_by' => $this->createdBy,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'difficulty' => $this->difficulty,
            'duration_hours' => $this->durationHours,
            'max_group_size' => $this->maxGroupSize,
            'price_per_person' => $this->pricePerPerson,
            'original_price' => $this->originalPrice,
            'deal_starts_at' => $this->dealStartsAt,
            'deal_ends_at' => $this->dealEndsAt,
            'private_price' => $this->privatePrice,
            'pickup_enabled' => $this->pickupEnabled,
            'is_top_rated' => $this->isTopRated,
            'location_city' => $this->locationCity,
            'location_lat' => $this->locationLat,
            'location_lng' => $this->locationLng,
            'meeting_point' => $this->meetingPoint,
            'inclusions' => $this->inclusions,
            'exclusions' => $this->exclusions,
            'status' => $this->status,
        ];
    }
}
