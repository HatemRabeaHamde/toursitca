<?php

namespace App\Domain\Review\DTOs;

final readonly class ReviewData
{
    public function __construct(
        public int $bookingId,
        public int $rating,
        public string $body,
    ) {}
}
