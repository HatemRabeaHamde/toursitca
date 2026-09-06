<?php

namespace App\Providers;

use App\Http\ViewComposers\NavbarComposer;
use App\Http\ViewComposers\SeoComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', SeoComposer::class);
        View::composer(['layouts.site', 'layouts.admin', 'layouts.agency'], NavbarComposer::class);
    }
}
