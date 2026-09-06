<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('locales.supported', []));
        $default = config('locales.default', 'en');
        $routeLocale = $request->route('locale');
        $locale = in_array($routeLocale, $supported, true) ? $routeLocale : $default;
        $dir = config("locales.supported.$locale.dir", 'ltr');

        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);
        View::share('currentLocale', $locale);
        View::share('dir', $dir);
        View::share('isRtl', $dir === 'rtl');

        return $next($request);
    }
}
