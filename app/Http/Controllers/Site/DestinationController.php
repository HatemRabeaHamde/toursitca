<?php

namespace App\Http\Controllers\Site;

use App\Domain\Discover\Services\DestinationGuideService;
use App\Domain\Landing\Models\Destination;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function show(string $locale, Destination $destination, DestinationGuideService $service): View
    {
        abort_unless($destination->is_active && $destination->guide, 404);

        return view('site.discover.destination', ['page' => $service->build($destination)]);
    }
}
