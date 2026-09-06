<?php

namespace App\Http\Controllers\Site;

use App\Domain\Experience\Services\ExperienceLandingPageService;
use App\Domain\Experience\Services\ExperienceSearchNavService;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(
        ExperienceLandingPageService $experienceLandingPageService,
        ExperienceSearchNavService $experienceSearchNavService,
    ): View
    {
        $landing = $experienceLandingPageService->get();
        $searchNav = $experienceSearchNavService->data();

        return view('site.home', compact('landing', 'searchNav'));
    }

    public function compass(ExperienceLandingPageService $experienceLandingPageService): View
    {
        $landing = $experienceLandingPageService->get();

        return view('site.home-compass', compact('landing'));
    }
}
