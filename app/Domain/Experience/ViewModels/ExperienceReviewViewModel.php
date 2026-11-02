<?php

namespace App\Domain\Experience\ViewModels;

use App\Domain\Experience\Models\ExperienceChangeRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ExperienceReviewViewModel
{
    public function __construct(
        public ?LengthAwarePaginator $reviews = null,
        public ?ExperienceChangeRequest $review = null,
        public array $sections = [],
    ) {}
}
