<?php

namespace App\Domain\Discover\Actions;

use App\Domain\Discover\Models\Host;
use InvalidArgumentException;

final class DeleteHostAction
{
    public function execute(Host $host): void
    {
        if ($host->experiences()->exists()) {
            throw new InvalidArgumentException(__('hosts_admin.errors.has_experiences'));
        }

        $host->delete();
    }
}
