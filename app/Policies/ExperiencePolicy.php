<?php

namespace App\Policies;

use App\Domain\Experience\Models\Experience;
use App\Models\User;

class ExperiencePolicy
{
    public function update(User $user, Experience $experience): bool
    {
        return $user->hasRole('admin') || $user->agency?->id === $experience->agency_id;
    }

    public function submit(User $user, Experience $experience): bool
    {
        return $this->update($user, $experience);
    }

    public function publish(User $user, Experience $experience): bool
    {
        return $user->hasRole('admin');
    }
}
