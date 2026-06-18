<?php

namespace App\Domain\Agency\Actions;

use App\Domain\Agency\Models\Agency;
use Illuminate\Support\Facades\DB;

final class ApproveAgencyAction
{
    public function execute(Agency $agency): Agency
    {
        return DB::transaction(function () use ($agency): Agency {
            $agency->forceFill(['status' => 'active'])->save();
            $agency->user->assignRole('travel_agency');

            return $agency->refresh();
        });
    }
}
