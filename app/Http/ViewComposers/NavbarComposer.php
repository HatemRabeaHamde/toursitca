<?php

namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class NavbarComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'locales' => config('locales.supported', []),
            'currentLocale' => App::getLocale(),
            'authUser' => auth()->user(),
            'categories' => trans('ui.categories'),
        ]);
    }
}
