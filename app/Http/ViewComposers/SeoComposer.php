<?php

namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class SeoComposer
{
    public function compose(View $view): void
    {
        $view->with('seo', [
            'title' => config('seo.default_title'),
            'description' => config('seo.default_description'),
            'image' => config('seo.default_image'),
            'site_name' => config('seo.site_name'),
            'locale' => App::getLocale(),
            'robots' => 'index, follow',
        ]);
    }
}
