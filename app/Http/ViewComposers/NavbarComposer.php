<?php

namespace App\Http\ViewComposers;

use App\Domain\Landing\Models\NewsletterSubscriber;
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
            'adminUnreadInquiries' => $view->name() === 'layouts.admin'
                ? NewsletterSubscriber::whereNotNull('message')->whereNull('read_at')->count()
                : null,
        ]);
    }
}
