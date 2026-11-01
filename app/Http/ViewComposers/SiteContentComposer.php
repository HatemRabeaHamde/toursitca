<?php

namespace App\Http\ViewComposers;

use App\Services\SiteContentReader;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SiteContentComposer
{
    public function __construct(private SiteContentReader $reader, private Request $request) {}

    public function compose(View $view): void
    {
        $page = collect(config('site_content.pages'))->search(fn ($definition) => $definition['view'] === $view->name());
        $content = $this->reader->read((string) $page, app()->getLocale(), $this->request->has('content_preview'));
        $this->request->attributes->set('site_content', $content);
        $view->with('content', $content)->with('seo', [
            'title' => $content->get('seo.title'), 'description' => $content->get('seo.description'),
            'robots' => $content->preview ? 'noindex, nofollow' : 'index, follow',
        ]);
    }
}
