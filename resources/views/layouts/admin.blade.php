<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    @include('partials.head.meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/admin-dashboard.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
<div class="adm-shell">

    {{-- ── Sidebar ─────────────────────────────────────────── --}}
    <aside class="adm-sidebar">

        <a href="{{ route('admin.dashboard') }}" class="adm-logo">
            {{ config('brand.name_first') }}<strong>{{ config('brand.name_second') }}</strong><span class="adm-logo-dot"></span>
            <span class="adm-logo-label">Admin</span>
        </a>

        <nav class="adm-nav" aria-label="{{ __('ui.nav.main') }}">

            <span class="adm-nav-section">{{ __('ui.nav.platform') }}</span>

            <a href="{{ route('admin.dashboard') }}"
               class="adm-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                {{ __('ui.nav.dashboard') }}
            </a>

            <a href="{{ route('admin.agencies.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.agencies.*') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                {{ __('ui.nav.agencies') }}
                @php($adminAgenciesPending = \App\Domain\Agency\Models\Agency::where('status','pending')->count())
                @if($adminAgenciesPending > 0)
                    <span class="adm-nav-badge">{{ $adminAgenciesPending }}</span>
                @endif
            </a>

            <a href="{{ route('admin.experiences.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.experiences.*') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                {{ __('ui.nav.experiences') }}
            </a>

            <a href="{{ route('admin.availability.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.availability.*') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                {{ __('ui.nav.availability') }}
            </a>

            <a href="{{ route('admin.bookings.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.bookings.*') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                {{ __('ui.nav.bookings') }}
                @php($adminBookingsPending = \App\Domain\Booking\Models\Booking::where('status','pending')->count())
                @if($adminBookingsPending > 0)
                    <span class="adm-nav-badge">{{ $adminBookingsPending }}</span>
                @endif
            </a>

            <a href="{{ route('admin.payouts.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.payouts.*') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                {{ __('ui.nav.payouts') }}
                @php($adminPayoutsPending = \App\Domain\Payout\Models\Payout::where('status','pending')->count())
                @if($adminPayoutsPending > 0)
                    <span class="adm-nav-badge">{{ $adminPayoutsPending }}</span>
                @endif
            </a>

            <a href="{{ route('admin.reviews.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.reviews.*') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                {{ __('ui.nav.reviews') }}
            </a>

            <a href="{{ route('admin.newsletter.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.newsletter.*') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16v16H4z" opacity="0"/><path d="M22 6l-10 7L2 6"/><rect x="2" y="4" width="20" height="16" rx="2"/>
                </svg>
                {{ __('ui.nav.newsletter') }}
                @if($adminUnreadInquiries ?? 0)
                    <span class="adm-nav-badge">{{ $adminUnreadInquiries }}</span>
                @endif
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                {{ __('ui.nav.users') }}
            </a>

            <span class="adm-nav-section">{{ __('ui.nav.content') }}</span>

            <a href="{{ route('admin.landing.categories.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.landing.categories.*') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M4 12h8m-8 6h16"/>
                </svg>
                {{ __('ui.nav.categories') }}
            </a>

            <a href="{{ route('admin.landing.destinations.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.landing.destinations.*') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                </svg>
                {{ __('ui.nav.destinations') }}
            </a>

            <a href="{{ route('admin.landing.landmarks.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.landing.landmarks.*') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="3 11 22 2 13 21 11 13 3 11"/>
                </svg>
                {{ __('ui.nav.landmarks') }}
            </a>

            <a href="{{ route('admin.landing.faqs.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.landing.faqs.*') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                {{ __('ui.nav.faqs') }}
            </a>

            <a href="{{ route('admin.landing.reels.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.landing.reels.*') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/>
                </svg>
                {{ __('ui.nav.reels') }}
            </a>

            <a href="{{ route('admin.landing.testimonials.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.landing.testimonials.*') ? 'is-active' : '' }}">
                <svg class="adm-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                {{ __('ui.nav.testimonials') }}
            </a>

        </nav>

        <div class="adm-sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="adm-logout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    {{ __('ui.nav.logout') }}
                </button>
            </form>
        </div>

    </aside>

    {{-- ── Main ─────────────────────────────────────────────── --}}
    <div class="adm-main">

        <header class="adm-topbar">
            <div class="adm-topbar-left">
                <h1>@yield('page-title', __('ui.pages.admin_title'))</h1>
                @hasSection('page-subtitle')
                    <span class="adm-topbar-sub">@yield('page-subtitle')</span>
                @endif
            </div>
            <div class="adm-topbar-actions">
                @yield('topbar-actions')
            </div>
        </header>

        <div class="adm-flash">@include('partials.flash')</div>

        <main class="adm-content page-transition-root">
            @yield('content')
        </main>

    </div>

</div>
    @stack('scripts')
</body>
</html>
