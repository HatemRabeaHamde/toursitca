<?php

namespace App\Http\Responses;

use App\Domain\Experience\DTOs\ExperienceSearchData;
use App\Domain\Experience\Services\ExperienceMarketplaceService;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Full marketplace page on a normal visit; when the page filters itself in
 * place (or loads more), only the parts that change are returned as JSON.
 */
final class ExperienceMarketplaceResponse implements Responsable
{
    public function __construct(
        private readonly ExperienceSearchData $filters,
        private readonly ExperienceMarketplaceService $service,
    ) {}

    public function toResponse($request): View|JsonResponse
    {
        if (! $this->wantsFragments($request)) {
            return view('site.experiences.marketplace', [
                'page' => $this->service->build($this->filters),
                'seo' => ['title' => __('experiences.meta_title')],
            ]);
        }

        $data = ['page' => $this->service->results($this->filters)];

        return response()->json([
            'chips' => view('site.experiences.marketplace.partials.chips', $data)->render(),
            'count' => trim(view('site.experiences.marketplace.partials.count', $data)->render()),
            'results' => view('site.experiences.marketplace.partials.results', $data)->render(),
            'more' => view('site.experiences.marketplace.partials.more', $data)->render(),
            'badge' => view('site.experiences.marketplace.partials.badge', $data)->render(),
        ])->header('Vary', 'Accept');
    }

    private function wantsFragments(Request $request): bool
    {
        return $request->ajax() && $request->expectsJson();
    }
}
