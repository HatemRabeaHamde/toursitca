<?php

namespace App\Http\Controllers\Site;

use App\Domain\Discover\Services\HostCards;
use App\Domain\Discover\Services\LandingDiscoverLinks;
use App\Domain\Experience\Services\ExperienceLandingPageService;
use App\Domain\Landing\ViewModels\HomeV2ViewModel;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(ExperienceLandingPageService $experienceLandingPageService, LandingDiscoverLinks $discoverLinks, HostCards $hosts): View
    {
        $page = new HomeV2ViewModel($experienceLandingPageService->get(), $discoverLinks->build(), $hosts->verified());

        return view('site.home-v2', compact('page'));
    }

    public function compass(ExperienceLandingPageService $experienceLandingPageService): View
    {
        $landing = $experienceLandingPageService->get();

        return view('site.home-compass', compact('landing'));
    }
}
