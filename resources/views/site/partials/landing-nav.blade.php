@php
    $locale = app()->getLocale();
    $homeUrl = route('site.home', ['locale' => $locale]);
    $experiencesUrl = route('site.experiences.index', ['locale' => $locale]);
    $solid = $solid ?? false;
@endphp

<div x-data="{ menuOpen: false }" @keydown.escape.window="menuOpen = false">
<nav data-landing-nav data-solid="{{ $solid ? 'true' : 'false' }}" @class(['scrolled' => $solid])>
    <a href="{{ $homeUrl }}" class="nav-logo">Rihla<em>Ma</em></a>

    <div class="nav-links">
        <a href="{{ $homeUrl }}#deals">{{ __('ui.landing.nav.deals') }}</a>
        <a href="{{ $homeUrl }}#destinations">{{ __('ui.landing.nav.destinations') }}</a>
        <a href="{{ $experiencesUrl }}">{{ __('ui.landing.nav.experiences') }}</a>
        <a href="{{ route('agency.register', ['locale' => $locale]) }}">{{ __('ui.landing.nav.agency') }}</a>
        <a href="{{ $homeUrl }}#faq">{{ __('ui.landing.nav.faq') }}</a>
    </div>

    <div class="nav-right">
        <button class="nav-lang" type="button">{{ strtoupper(app()->getLocale()) }} · {{ config('payment.currency', 'MAD') }}</button>
        @auth
            <a href="{{ route('dashboard') }}" class="nav-login">{{ __('ui.nav.dashboard') }}</a>
        @else
            <a href="{{ route('login') }}" class="nav-login">{{ __('ui.nav.login') }}</a>
            <a href="{{ route('register') }}" class="nav-cta">{{ __('ui.landing.nav.sign_up') }}</a>
        @endauth

        <button type="button" class="nav-burger" :class="{ 'is-open': menuOpen }" @click="menuOpen = ! menuOpen" :aria-expanded="menuOpen" aria-label="{{ __('ui.landing.nav.menu') }}">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</nav>

<div class="nav-mobile" :class="{ 'is-open': menuOpen }" @click.self="menuOpen = false">
    <div class="nav-mobile-panel">
        <a href="{{ $homeUrl }}#deals" @click="menuOpen = false">{{ __('ui.landing.nav.deals') }}</a>
        <a href="{{ $homeUrl }}#destinations" @click="menuOpen = false">{{ __('ui.landing.nav.destinations') }}</a>
        <a href="{{ $experiencesUrl }}" @click="menuOpen = false">{{ __('ui.landing.nav.experiences') }}</a>
        <a href="{{ route('agency.register', ['locale' => $locale]) }}" @click="menuOpen = false">{{ __('ui.landing.nav.agency') }}</a>
        <a href="{{ $homeUrl }}#faq" @click="menuOpen = false">{{ __('ui.landing.nav.faq') }}</a>

        <div class="nav-mobile-divider"></div>

        <button class="nav-mobile-lang" type="button">{{ strtoupper(app()->getLocale()) }} · {{ config('payment.currency', 'MAD') }}</button>

        @auth
            <a href="{{ route('dashboard') }}" class="nav-mobile-login" @click="menuOpen = false">{{ __('ui.nav.dashboard') }}</a>
        @else
            <a href="{{ route('login') }}" class="nav-mobile-login" @click="menuOpen = false">{{ __('ui.nav.login') }}</a>
            <a href="{{ route('register') }}" class="nav-mobile-cta" @click="menuOpen = false">{{ __('ui.landing.nav.sign_up') }}</a>
        @endauth
    </div>
</div>
</div>
