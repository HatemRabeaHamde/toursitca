<?php

namespace App\Domain\Agency\Actions;

use App\Domain\Agency\Models\Agency;

final class ReactivateAgencyAction
{
    public function execute(Agency $agency): Agency
    {
        $agency->forceFill(['status' => 'active'])->save();

        return $agency->refresh();
    }
}
