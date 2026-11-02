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
                ['href' => landing_link('footer.link_about', $home.'#story'), 'content_key' => 'footer.about', 'label' => landing_text('site.footer.about')],
                ['href' => landing_link('footer.link_careers', '#'), 'content_key' => 'footer.careers', 'label' => landing_text('site.footer.careers')],
                ['href' => landing_link('footer.link_contact', '#contact'), 'content_key' => 'footer.contact', 'label' => landing_text('site.footer.contact')],
            ],
            'explore' => [
                ['href' => landing_link('footer.link_destinations', $discover), 'content_key' => 'footer.destinations', 'label' => landing_text('site.footer.destinations')],
                ['href' => landing_link('footer.link_experiences', route('site.experiences.index', ['locale' => $locale])), 'content_key' => 'footer.experiences', 'label' => landing_text('site.footer.experiences')],
                ['href' => landing_link('footer.link_guides', $discover), 'content_key' => 'footer.guides', 'label' => landing_text('site.footer.guides')],
            ],
            'support' => [
                ['href' => landing_link('footer.link_faqs', $home.'#know'), 'content_key' => 'footer.faqs', 'label' => landing_text('site.footer.faqs')],
                ['href' => landing_link('footer.link_privacy', '#'), 'content_key' => 'footer.privacy', 'label' => landing_text('site.footer.privacy')],
                ['href' => landing_link('footer.link_terms', '#'), 'content_key' => 'footer.terms', 'label' => landing_text('site.footer.terms')],
            ],
        ];
    }

    /** @return array<string, string> network => url */
    public function socials(): array
    {
        return array_filter(collect(config('landing_v2.social_urls', []))->map(fn ($url, $network) => landing_link('footer.social_'.$network, landing_link('geo.'.$network, $url)))->all());
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
