<?php

namespace App\Domain\Discover\Actions;

use App\Domain\Discover\Models\Host;
use InvalidArgumentException;

/** An admin decides whether a host is visible and whether the "Verified" badge is earned. */
final class ReviewHostAction
{
    public function execute(Host $host, bool $active, bool $verified, ?int $sortOrder = null): Host
    {
        if ($verified && blank($host->licence_number)) {
            throw new InvalidArgumentException(__('hosts_admin.errors.licence_required'));
        }

        $host->forceFill([
            'is_active' => $active,
            'is_verified' => $verified,
            'reviewed_at' => now(),
            'sort_order' => $sortOrder ?? $host->sort_order,
        ])->save();

        return $host;
    }
}
