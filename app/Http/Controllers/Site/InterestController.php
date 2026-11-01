<?php

namespace App\Http\Controllers\Site;

use App\Domain\Discover\Models\Interest;
use App\Domain\Discover\Services\InterestGuideService;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class InterestController extends Controller
{
    public function show(string $locale, Interest $interest, InterestGuideService $service): View
    {
        abort_unless($interest->is_active && $interest->guide, 404);

        return view('site.discover.interest', ['page' => $service->build($interest)]);
    }
}
