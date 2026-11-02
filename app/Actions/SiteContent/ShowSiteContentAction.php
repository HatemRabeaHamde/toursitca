<?php

namespace App\Actions\SiteContent;

use App\Services\SiteContentEditorData;
use App\Services\SiteContentRegistry;
use Illuminate\View\View;

final class ShowSiteContentAction
{
    public function __construct(private SiteContentRegistry $registry, private SiteContentEditorData $editor) {}

    public function execute(?string $page = null): View
    {
        if ($page === null) {
            return view('admin.site-content.index', ['pages' => $this->registry->pages(), 'seo' => ['title' => 'Website studio | Tourstica admin', 'robots' => 'noindex, nofollow']]);
        }
        $settingsSections = collect(config('site_content.panels'))->pluck('sections')->flatten()->all();
        $canvasSections = array_diff(array_keys($this->registry->page($page)['sections']), $settingsSections);
        $data = $this->editor->get($page, $canvasSections);
        $data['seo'] = ['title' => 'Homepage editor | Tourstica admin', 'robots' => 'noindex, nofollow'];
        $data['definition']['sections'] = array_diff_key($data['definition']['sections'], array_flip($settingsSections));
        $data['fields'] = array_filter($data['fields'], fn ($field) => ! in_array(explode('.', $field['key'])[0], $settingsSections, true));

        return view('admin.site-content.edit', $data);
    }
}
