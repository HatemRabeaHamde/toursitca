<?php

namespace App\Domain\Experience\Events;

use App\Domain\Experience\Models\ExperienceChangeRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class ExperienceReviewChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly ExperienceChangeRequest $review) {}
}
