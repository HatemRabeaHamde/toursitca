<?php

namespace App\Http\Controllers\Site;

use App\Domain\Discover\Models\Host;
use App\Domain\Discover\Services\HostDirectoryService;
use App\Domain\Discover\Services\HostProfileService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\HostDirectoryRequest;
use App\Http\Responses\HostDirectoryResponse;
use Illuminate\View\View;

class HostsController extends Controller
{
    public function index(HostDirectoryRequest $request, HostDirectoryService $service): HostDirectoryResponse
    {
        return new HostDirectoryResponse($service->build($request->toFilters()));
    }

    public function show(string $locale, Host $host, HostProfileService $service): View
    {
        abort_unless($host->is_active, 404);

        return view('site.hosts.show', ['page' => $service->build($host), 'seo' => ['title' => __('hosts.profile.meta_title', ['name' => $host->name])]]);
    }
}
