@php($homeUrl = route('site.home', ['locale' => app()->getLocale()]))

<footer class="site-footer">
    <div class="ft-top">
        <div class="ft-brand">
            <p class="ft-logo">{{ config('brand.name_first') }}<em>{{ config('brand.name_second') }}</em></p>
            <p class="ft-desc">{{ config('seo.default_description') }}</p>

            <div class="ft-trust">
                <span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2 3 6v6c0 5 4 8.5 9 10 5-1.5 9-5 9-10V6z"/></svg>
                    {{ __('ui.landing.footer.trust_verified') }}
                </span>
                <span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ __('ui.landing.footer.trust_cancellation') }}
                </span>
            </div>

            <div class="ft-social">
                <a href="#" aria-label="Instagram">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1"/></svg>
                </a>
                <a href="#" aria-label="Facebook">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </a>
                <a href="#" aria-label="TikTok">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
                </a>
            </div>
        </div>

        <div class="ft-links">
            <div class="ft-col">
                <h4>{{ __('ui.landing.footer.explore_heading') }}</h4>
                <a href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}">{{ __('ui.landing.all_experiences') }}</a>
                <a href="{{ $homeUrl }}#destinations">{{ __('ui.landing.sections.destinations_title') }}</a>
                <a href="{{ $homeUrl }}#deals">{{ __('ui.landing.nav.deals') }}</a>
                <a href="{{ $homeUrl }}#categories">{{ __('ui.landing.sections.categories_title') }}</a>
            </div>

            <div class="ft-col">
                <h4>{{ __('ui.landing.footer.company_heading') }}</h4>
                <a href="{{ $homeUrl }}#faq">{{ __('ui.landing.sections.faq_title') }}</a>
                <a href="{{ $homeUrl }}#newsletter">{{ __('ui.landing.sections.newsletter_title') }}</a>
                <a href="{{ $homeUrl }}#agencies">{{ __('ui.landing.sections.agencies_title') }}</a>
            </div>

            <div class="ft-col">
                <h4>{{ __('ui.landing.sections.agency_cta_label') }}</h4>
                <a href="{{ route('agency.register', ['locale' => app()->getLocale()]) }}">{{ __('ui.landing.sections.agency_cta_primary') }}</a>
                <a href="{{ route('login') }}">{{ __('ui.nav.login') }}</a>
            </div>
        </div>
    </div>

    <div class="ft-bottom">
        <p class="ft-copy">&copy; {{ now()->year }} {{ config('brand.name_first') }}{{ config('brand.name_second') }}. {{ __('ui.landing.footer.rights') }}</p>
        <p class="ft-made">{{ __('ui.landing.footer.made_in_morocco') }}</p>
    </div>
</footer>
