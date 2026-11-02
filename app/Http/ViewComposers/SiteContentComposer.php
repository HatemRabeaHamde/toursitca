<?php

namespace App\Http\ViewComposers;

use App\Services\SiteContentMetadata;
use App\Services\SiteContentReader;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SiteContentComposer
{
    public function __construct(private SiteContentReader $reader, private Request $request, private SiteContentMetadata $metadata) {}

    public function compose(View $view): void
    {
        $page = collect(config('site_content.pages'))->search(fn ($definition) => $definition['view'] === $view->name());
        $content = $this->reader->read((string) $page, app()->getLocale(), $this->request->has('content_preview'));
        $this->request->attributes->set('site_content', $content);
        $view->with('content', $content)->with('seo', $this->metadata->build($content, app()->getLocale()));
    }
}
