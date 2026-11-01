<?php

namespace App\Http\Controllers\Site;

use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceDetailService;
use App\Domain\Experience\Services\ExperienceMarketplaceService;
use App\Domain\Experience\Services\ExperiencePageService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\ExperienceSearchRequest;
use App\Http\Responses\ExperienceMarketplaceResponse;
use Illuminate\Contracts\View\View;

class ExperienceController extends Controller
{
    public function index(ExperienceSearchRequest $request, ExperienceMarketplaceService $marketplace): ExperienceMarketplaceResponse
    {
        return new ExperienceMarketplaceResponse($request->toData(), $marketplace);
    }

    public function show(string $locale, Experience $experience, ExperiencePageService $pages): View
    {
        abort_unless($experience->status === 'published', 404);

        return view('site.experiences.detail-v1', ['page' => $pages->build($experience)]);
    }

    public function previewShow(string $locale, Experience $experience, ExperienceDetailService $experienceDetailService): View
    {
        abort_unless($experience->status === 'published', 404);

        $detail = $experienceDetailService->get($experience);

        return view('site.experiences.preview', compact('detail'));
    }
}
