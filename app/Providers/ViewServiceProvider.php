<?php

namespace App\Providers;

use App\Http\ViewComposers\NavbarComposer;
use App\Http\ViewComposers\SeoComposer;
use App\Http\ViewComposers\SiteContentComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        require_once app_path('Support/site_content.php');
        View::composer(array_column(config('site_content.pages'), 'view'), SiteContentComposer::class);
        View::composer('*', SeoComposer::class);
        View::composer(['layouts.site', 'layouts.admin', 'layouts.agency'], NavbarComposer::class);
    }
}
