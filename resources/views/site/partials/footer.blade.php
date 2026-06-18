@php($homeUrl = route('site.home', ['locale' => app()->getLocale()]))

<footer class="site-footer">
    <div class="ft-main">
        <div class="ft-brand">
            <p class="ft-logo">Rihla<em>Ma</em></p>
            <p class="ft-desc">{{ config('seo.default_description') }}</p>
        </div>

        <div class="ft-col">
            <h4>{{ __('ui.nav.experiences') }}</h4>
            <a href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}">{{ __('ui.landing.all_experiences') }}</a>
            <a href="{{ $homeUrl }}#destinations">{{ __('ui.landing.sections.destinations_title') }}</a>
            <a href="{{ $homeUrl }}#deals">{{ __('ui.landing.nav.deals') }}</a>
        </div>

        <div class="ft-col">
            <h4>{{ __('ui.landing.sections.agency_cta_label') }}</h4>
            <a href="{{ route('agency.register', ['locale' => app()->getLocale()]) }}">{{ __('ui.landing.sections.agency_cta_primary') }}</a>
            <a href="{{ route('login') }}">{{ __('ui.nav.login') }}</a>
        </div>

        <div class="ft-col">
            <h4>{{ __('ui.landing.sections.faq_label') }}</h4>
            <a href="{{ $homeUrl }}#faq">{{ __('ui.landing.sections.faq_title') }}</a>
            <a href="{{ $homeUrl }}#newsletter">{{ __('ui.landing.sections.newsletter_title') }}</a>
        </div>
    </div>

    <div class="ft-bottom">
        <p class="ft-copy">&copy; {{ now()->year }} {{ __('ui.brand') }}. All rights reserved.</p>
    </div>
</footer>
