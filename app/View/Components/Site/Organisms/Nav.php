<?php

namespace App\View\Components\Site\Organisms;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Global header for every Tourstica v2 public page.
 * `active` highlights the current section: discover|experiences|hosts|how.
 */
final class Nav extends Component
{
    public function __construct(public ?string $active = null) {}

    /** @return list<array{key: string, href: string, label: string}> */
    public function links(): array
    {
        $locale = app()->getLocale();
        $home = route('site.home', ['locale' => $locale]);

        return [
            ['key' => 'discover', 'href' => $this->routeOr('site.discover.index', $home.'#discover'), 'label' => __('site.nav.discover')],
            ['key' => 'experiences', 'href' => route('site.experiences.index', ['locale' => $locale]), 'label' => __('site.nav.experiences')],
            ['key' => 'hosts', 'href' => $this->routeOr('site.hosts.index', $home.'#people'), 'label' => __('site.nav.hosts')],
            ['key' => 'how', 'href' => $home.'#plan', 'label' => __('site.nav.how_it_works')],
        ];
    }

    /** @return array{href: string, label: string} */
    public function account(): array
    {
        return auth()->check()
            ? ['href' => route('site.dashboard.bookings', ['locale' => app()->getLocale()]), 'label' => __('site.nav.dashboard')]
            : ['href' => route('login'), 'label' => __('site.nav.sign_in')];
    }

    public function homeUrl(): string
    {
        return route('site.home', ['locale' => app()->getLocale()]);
    }

    private function routeOr(string $name, string $fallback): string
    {
        return Route::has($name) ? route($name, ['locale' => app()->getLocale()]) : $fallback;
    }

    public function render(): View
    {
        return view('components.site.organisms.nav');
    }
}
