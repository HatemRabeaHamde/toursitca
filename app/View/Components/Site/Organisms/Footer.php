<?php

namespace App\View\Components\Site\Organisms;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\View\View;

/** Global footer: brand, link columns, socials and the newsletter form. */
final class Footer extends Component
{
    /** @return array<string, list<array{href: string, label: string}>> */
    public function columns(): array
    {
        $locale = app()->getLocale();
        $home = route('site.home', ['locale' => $locale]);
        $discover = Route::has('site.discover.index') ? route('site.discover.index', ['locale' => $locale]) : $home.'#discover';

        return [
            'company' => [
                ['href' => $home.'#story', 'label' => __('site.footer.about')],
                ['href' => '#', 'label' => __('site.footer.careers')],
                ['href' => '#contact', 'label' => __('site.footer.contact')],
            ],
            'explore' => [
                ['href' => $discover, 'label' => __('site.footer.destinations')],
                ['href' => route('site.experiences.index', ['locale' => $locale]), 'label' => __('site.footer.experiences')],
                ['href' => $discover, 'label' => __('site.footer.guides')],
            ],
            'support' => [
                ['href' => $home.'#know', 'label' => __('site.footer.faqs')],
                ['href' => '#', 'label' => __('site.footer.privacy')],
                ['href' => '#', 'label' => __('site.footer.terms')],
            ],
        ];
    }

    /** @return array<string, string> network => url */
    public function socials(): array
    {
        return array_filter(config('landing_v2.social_urls', []));
    }

    public function brand(): string
    {
        return config('brand.name_first').config('brand.name_second');
    }

    public function render(): View
    {
        return view('components.site.organisms.footer');
    }
}
