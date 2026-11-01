<?php

namespace App\Domain\Discover\DTOs;

use Illuminate\Http\UploadedFile;

/** What an agency (or an admin) fills in on a host profile. Translatable fields keep only the locales that were written. */
final readonly class HostProfileData
{
    /**
     * @param  array<string, string>  $role
     * @param  array<string, string>  $bio
     * @param  array<string, string>  $quote
     * @param  array<string, list<string>>  $story  paragraphs per locale
     * @param  array<string, list<string>>  $skills  one skill per entry, per locale
     * @param  list<string>  $languages
     */
    public function __construct(
        public string $name,
        public array $role,
        public ?string $city,
        public ?int $destinationId,
        public ?int $interestId,
        public array $bio,
        public array $quote,
        public array $story,
        public array $skills,
        public array $languages,
        public ?int $partnerSince,
        public ?string $licenceNumber,
        public ?UploadedFile $photo = null,
        public ?UploadedFile $cover = null,
    ) {}
}
