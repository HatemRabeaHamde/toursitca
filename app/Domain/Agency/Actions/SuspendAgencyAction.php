<?php

namespace App\Domain\Agency\Actions;

use App\Domain\Agency\Models\Agency;

final class SuspendAgencyAction
{
    public function execute(Agency $agency): Agency
    {
        $agency->forceFill(['status' => 'suspended'])->save();

        return $agency->refresh();
    }
}
