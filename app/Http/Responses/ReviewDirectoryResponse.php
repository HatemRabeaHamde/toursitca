<?php

namespace App\Http\Responses;

use App\Domain\Review\DTOs\ReviewDirectoryFilters;
use App\Domain\Review\Services\ReviewDirectoryService;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Full reviews page, or only the parts that change as JSON when filtering in place. */
final class ReviewDirectoryResponse implements Responsable
{
    public function __construct(
        private readonly ReviewDirectoryFilters $filters,
        private readonly ReviewDirectoryService $service,
    ) {}

    public function toResponse($request): View|JsonResponse
    {
        if (! $this->wantsFragments($request)) {
            return view('site.reviews.index', [
                'page' => $this->service->build($this->filters),
                'seo' => ['title' => __('reviews.meta_title')],
            ]);
        }

        $data = ['page' => $this->service->results($this->filters)];

        return response()->json([
            'chips' => view('site.reviews.partials.chips', $data)->render(),
            'count' => trim(view('site.reviews.partials.count', $data)->render()),
            'results' => view('site.reviews.partials.results', $data)->render(),
            'more' => view('site.reviews.partials.more', $data)->render(),
        ])->header('Vary', 'Accept');
    }

    private function wantsFragments(Request $request): bool
    {
        return $request->ajax() && $request->expectsJson();
    }
}
