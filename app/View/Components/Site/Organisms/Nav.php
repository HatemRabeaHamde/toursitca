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
            ['key' => 'discover', 'href' => landing_link('nav.link_discover', $this->routeOr('site.discover.index', $home.'#discover')), 'content_key' => 'nav.discover', 'label' => landing_text('site.nav.discover')],
            ['key' => 'experiences', 'href' => landing_link('nav.link_experiences', route('site.experiences.index', ['locale' => $locale])), 'content_key' => 'nav.experiences', 'label' => landing_text('site.nav.experiences')],
            ['key' => 'hosts', 'href' => landing_link('nav.link_hosts', $this->routeOr('site.hosts.index', $home.'#people')), 'content_key' => 'nav.hosts', 'label' => landing_text('site.nav.hosts')],
            ['key' => 'how', 'href' => landing_link('nav.link_how', $home.'#plan'), 'content_key' => 'nav.how_it_works', 'label' => landing_text('site.nav.how_it_works')],
        ];
    }

    /** @return array{href: string, label: string} */
    public function account(): array
    {
        return auth()->check()
            ? ['href' => route('site.dashboard.bookings', ['locale' => app()->getLocale()]), 'content_key' => 'nav.dashboard', 'label' => landing_text('site.nav.dashboard')]
            : ['href' => route('login'), 'content_key' => 'nav.sign_in', 'label' => landing_text('site.nav.sign_in')];
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
