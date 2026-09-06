<?php

namespace App\View\Composers;

use Illuminate\View\View;

final class WishlistComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();

        $view->with('wishlisted', $user ? $user->wishlistedExperienceIds() : []);
    }
}
