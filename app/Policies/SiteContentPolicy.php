<?php

namespace App\Policies;

use App\Models\User;

final class SiteContentPolicy
{
    public function manage(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
