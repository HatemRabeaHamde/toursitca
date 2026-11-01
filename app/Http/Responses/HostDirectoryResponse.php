<?php

namespace App\Http\Responses;

use App\Domain\Discover\ViewModels\HostsPageViewModel;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Full page on a normal visit; only the parts that change (chips, count,
 * results) as JSON when the directory filters itself in place.
 */
final class HostDirectoryResponse implements Responsable
{
    public function __construct(private readonly HostsPageViewModel $page) {}

    public function toResponse($request): View|JsonResponse
    {
        if (! $this->wantsFragments($request)) {
            return view('site.hosts.index', [
                'page' => $this->page,
                'seo' => ['title' => __('hosts.meta_title')],
            ]);
        }

        $data = ['page' => $this->page];

        return response()->json([
            'chips' => view('site.hosts.sections.partials.chips', $data)->render(),
            'count' => trim(view('site.hosts.sections.partials.count', $data)->render()),
            'results' => view('site.hosts.sections.partials.results', $data)->render(),
        ])->header('Vary', 'Accept');
    }

    private function wantsFragments(Request $request): bool
    {
        return $request->ajax() && $request->expectsJson();
    }
}
