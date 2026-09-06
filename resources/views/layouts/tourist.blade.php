<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $dir ?? 'ltr' }}">
<head>
    @include('partials.head.meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/experience-card.css', 'resources/css/tourist-dashboard.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
@php
    $locale = app()->getLocale();
@endphp
<div class="td-shell" x-data="{ navOpen: false }">

    {{-- ── Mobile topbar ───────────────────────────────── --}}
    <header class="td-mobile-bar">
        <a href="{{ route('site.home', ['locale' => $locale]) }}" class="td-mobile-logo">
            {{ config('brand.name_first') }}<em>{{ config('brand.name_second') }}</em>
        </a>
        <button type="button" class="td-mobile-toggle" @click="navOpen = ! navOpen" :aria-expanded="navOpen" aria-label="{{ __('ui.actions.open_menu') }}">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
    </header>

    {{-- ── Sidebar ─────────────────────────────────────── --}}
    <aside class="td-sidebar" :class="{ 'is-open': navOpen }">

        <a href="{{ route('site.home', ['locale' => $locale]) }}" class="td-logo">
            {{ config('brand.name_first') }}<em>{{ config('brand.name_second') }}</em>
        </a>

        <div class="td-user">
            <span class="td-user-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="td-user-info">
                <strong>{{ auth()->user()->name }}</strong>
                <em>{{ auth()->user()->email }}</em>
            </span>
        </div>

        <nav class="td-nav" aria-label="{{ __('ui.nav.main') }}">
            <a href="{{ route('site.dashboard.bookings', ['locale' => $locale]) }}"
               class="td-nav-link {{ request()->routeIs('site.dashboard.bookings*') ? 'is-active' : '' }}">
                <svg class="td-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 7h-9m9 5h-9m9 5h-9M5 7h.01M5 12h.01M5 17h.01"/>
                </svg>
                {{ __('ui.pages.my_trips_title') }}
            </a>

            <a href="{{ route('site.dashboard.reviews', ['locale' => $locale]) }}"
               class="td-nav-link {{ request()->routeIs('site.dashboard.reviews*') ? 'is-active' : '' }}">
                <svg class="td-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
                {{ __('ui.pages.my_reviews_title') }}
            </a>

            <a href="{{ route('site.dashboard.wishlist', ['locale' => $locale]) }}"
               class="td-nav-link {{ request()->routeIs('site.dashboard.wishlist*') ? 'is-active' : '' }}">
                <svg class="td-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                {{ __('ui.pages.my_wishlist_title') }}
            </a>

            <a href="{{ route('profile.edit') }}"
               class="td-nav-link {{ request()->routeIs('profile.edit') ? 'is-active' : '' }}">
                <svg class="td-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                {{ __('ui.pages.profile_title') }}
            </a>
        </nav>

        <div class="td-sidebar-footer">
            <a href="{{ route('site.experiences.index', ['locale' => $locale]) }}" class="td-browse-link">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                {{ __('ui.nav.experiences') }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="td-logout">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    {{ __('ui.nav.logout') }}
                </button>
            </form>
        </div>
    </aside>

    <div class="td-nav-backdrop" x-show="navOpen" x-cloak @click="navOpen = false"></div>

    {{-- ── Main ─────────────────────────────────────────── --}}
    <div class="td-main">
        <div class="td-flash">@include('partials.flash')</div>

        <main class="td-content page-transition-root">
            @yield('content')
        </main>
    </div>

</div>
@stack('scripts')
</body>
</html>
