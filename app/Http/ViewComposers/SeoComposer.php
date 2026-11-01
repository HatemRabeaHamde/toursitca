<?php

namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class SeoComposer
{
    public function compose(View $view): void
    {
        // Values a page passes (its own title, say) win over the defaults.
        $view->with('seo', array_merge([
            'title' => config('seo.default_title'),
            'description' => config('seo.default_description'),
            'image' => config('seo.default_image'),
            'site_name' => config('seo.site_name'),
            'locale' => App::getLocale(),
            'robots' => 'index, follow',
        ], $view->getData()['seo'] ?? []));
    }
}
