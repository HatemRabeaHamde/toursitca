<?php

namespace App\Domain\Agency\Actions;

use App\Domain\Agency\Events\AgencyRejected;
use App\Domain\Agency\Models\Agency;

final class RejectAgencyAction
{
    public function execute(Agency $agency): Agency
    {
        $agency->forceFill(['status' => 'rejected'])->save();
        AgencyRejected::dispatch($agency);

        return $agency->refresh();
    }
}
