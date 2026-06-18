<?php

namespace App\Domain\Booking\DTOs;

final readonly class BookingData
{
    public function __construct(
        public int $availabilityId,
        public string $bookingType,
        public int $participantsCount,
        public string $guestName,
        public string $guestEmail,
        public ?int $userId = null,
        public ?string $guestPhone = null,
        public ?string $specialNotes = null,
    ) {}
}
