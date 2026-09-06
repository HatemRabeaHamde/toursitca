<?php

namespace App\Domain\Agency\Events;

use App\Domain\Agency\Models\Agency;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class AgencyRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Agency $agency) {}
}
