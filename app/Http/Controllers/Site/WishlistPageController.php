<?php

namespace App\Http\Controllers\Site;

use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceCardPresenter;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class WishlistPageController extends Controller
{
    public function index(Request $request, ExperienceCardPresenter $experienceCardPresenter): View
    {
        $experienceIds = $request->user()->wishlists()->pluck('experience_id');

        $experiences = Experience::query()
            ->whereIn('id', $experienceIds)
            ->with(ExperienceCardPresenter::eagerLoads())
            ->get();

        $cards = $experiences->map(fn (Experience $experience) => $experienceCardPresenter->present($experience));

        return view('site.dashboard.wishlist', compact('cards'));
    }
}
