<?php

namespace App\Http\Controllers\Site;

use App\Domain\Experience\Services\ExperienceLandingPageService;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(ExperienceLandingPageService $experienceLandingPageService): View
    {
        $landing = $experienceLandingPageService->get();

        return view('site.home', compact('landing'));
    }
}
