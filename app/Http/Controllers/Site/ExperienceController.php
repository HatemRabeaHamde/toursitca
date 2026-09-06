<?php

namespace App\Http\Controllers\Site;

use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Queries\ExperienceSearchQuery;
use App\Domain\Experience\Services\ExperienceCardPresenter;
use App\Domain\Experience\Services\ExperienceDetailService;
use App\Domain\Experience\Services\ExperienceSearchNavService;
use App\Domain\Landing\Services\LandingContentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\ExperienceSearchRequest;
use Illuminate\Contracts\View\View;

class ExperienceController extends Controller
{
    public function index(
        ExperienceSearchRequest $request,
        ExperienceSearchQuery $experienceSearchQuery,
        ExperienceCardPresenter $experienceCardPresenter,
        ExperienceSearchNavService $experienceSearchNavService,
        LandingContentService $landingContentService,
    ): View {
        $filters = $request->toData();
        $experiences = $experienceSearchQuery->paginate($filters);
        $cards = $experiences
            ->getCollection()
            ->map(fn ($experience) => $experienceCardPresenter->present($experience));

        if ($request->ajax()) {
            return view('site.experiences.partials.results', compact('experiences', 'cards', 'filters'));
        }

        $categories = Experience::query()
            ->published()
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->pluck('total', 'category');

        $stats = [
            'experiences' => Experience::query()->published()->count(),
            'destinations' => Experience::query()->published()->distinct()->count('location_city'),
            'rating' => round((float) Experience::query()->published()->avg('rating_avg'), 1),
        ];

        $city = $filters->city ?: $experiences->getCollection()->first()?->location_city;

        $landmarks = $city ? $landingContentService->landmarksForCity($city) : collect();

        $beyondCards = $city
            ? $experienceSearchQuery->beyondCity($city)->map(fn ($experience) => $experienceCardPresenter->present($experience))
            : collect();
        $searchNav = $experienceSearchNavService->data($filters->city);

        return view('site.experiences.index', compact(
            'experiences', 'cards', 'filters', 'categories', 'stats', 'city', 'landmarks', 'beyondCards', 'searchNav',
        ));
    }

    public function show(string $locale, Experience $experience, ExperienceDetailService $experienceDetailService): View
    {
        abort_unless($experience->status === 'published', 404);

        $detail = $experienceDetailService->get($experience);

        return view('site.experiences.show', compact('detail'));
    }

    public function previewShow(string $locale, Experience $experience, ExperienceDetailService $experienceDetailService): View
    {
        abort_unless($experience->status === 'published', 404);

        $detail = $experienceDetailService->get($experience);

        return view('site.experiences.preview', compact('detail'));
    }
}
