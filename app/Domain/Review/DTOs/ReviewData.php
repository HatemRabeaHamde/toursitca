<?php

namespace App\Domain\Review\DTOs;

use Illuminate\Http\UploadedFile;

final readonly class ReviewData
{
    public function __construct(
        public int $bookingId,
        public int $rating,
        public string $body,
        /** @var array<string, int> detail rating column => stars */
        public array $details = [],
        /** @var list<UploadedFile> */
        public array $photos = [],
    ) {}
}
