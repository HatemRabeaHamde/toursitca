<?php

namespace App\Http\Controllers\Admin;

use App\Actions\SiteContent\RestoreSiteContentAction;
use App\Actions\SiteContent\SaveSiteContentAction;
use App\Actions\SiteContent\ShowSiteContentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManageSiteContentRequest;
use App\Http\Requests\Admin\SiteContentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class SiteContentController extends Controller
{
    public function index(ManageSiteContentRequest $request, ShowSiteContentAction $action): View
    {
        return $action->execute();
    }

    public function edit(ManageSiteContentRequest $request, ShowSiteContentAction $action, string $page): View
    {
        return $action->execute($page);
    }

    public function update(SiteContentRequest $request, SaveSiteContentAction $action, string $page): RedirectResponse
    {
        $action->execute($request->toData());

        return back()->with('success', $request->input('mode') === 'publish' ? 'Content published.' : 'Draft saved. The public site has not changed.');
    }

    public function restore(ManageSiteContentRequest $request, RestoreSiteContentAction $action, string $page, int $revision): RedirectResponse
    {
        $action->execute($page, $revision, $request->user()->id);

        return back()->with('success', 'Revision restored to draft. Preview it before publishing.');
    }
}
