<?php

namespace App\Http\Controllers\Site;

use App\Domain\Discover\Services\DiscoverPageService;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DiscoverController extends Controller
{
    public function index(DiscoverPageService $service): View
    {
        return view('site.discover.index', ['page' => $service->build()]);
    }
}
