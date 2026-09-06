<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $dir ?? 'ltr' }}">
<head>
    @include('partials.head.meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/agency-dashboard.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
<div class="ag-shell">

    {{-- ── Sidebar ─────────────────────────────────────── --}}
    <aside class="ag-sidebar">

        <a href="{{ route('agency.dashboard') }}" class="ag-logo">
            {{ config('brand.name_first') }}<strong>{{ config('brand.name_second') }}</strong><span class="ag-logo-dot"></span>
        </a>

        <nav class="ag-nav" aria-label="{{ __('ui.nav.main') }}">

            <a href="{{ route('agency.dashboard') }}"
               class="ag-nav-link {{ request()->routeIs('agency.dashboard') ? 'is-active' : '' }}">
                <svg class="ag-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                {{ __('ui.nav.dashboard') }}
            </a>

            <a href="{{ route('agency.experiences.index') }}"
               class="ag-nav-link {{ request()->routeIs('agency.experiences.*') ? 'is-active' : '' }}">
                <svg class="ag-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                {{ __('ui.nav.experiences') }}
                @if($agencyNavDraftCount > 0)
                    <span class="ag-nav-badge">{{ $agencyNavDraftCount }}</span>
                @endif
            </a>

            <a href="{{ route('agency.availability.index') }}"
               class="ag-nav-link {{ request()->routeIs('agency.availability.*') ? 'is-active' : '' }}">
                <svg class="ag-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                {{ __('ui.nav.availability') }}
            </a>

            <a href="{{ route('agency.bookings.index') }}"
               class="ag-nav-link {{ request()->routeIs('agency.bookings.*') ? 'is-active' : '' }}">
                <svg class="ag-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                </svg>
                {{ __('ui.nav.bookings') }}
                @if($agencyNavPendingCount > 0)
                    <span class="ag-nav-badge">{{ $agencyNavPendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('agency.payouts.index') }}"
               class="ag-nav-link {{ request()->routeIs('agency.payouts.*') ? 'is-active' : '' }}">
                <svg class="ag-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                {{ __('ui.nav.payouts') }}
            </a>

            <a href="{{ route('agency.reviews.index') }}"
               class="ag-nav-link {{ request()->routeIs('agency.reviews.*') ? 'is-active' : '' }}">
                <svg class="ag-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                {{ __('ui.nav.reviews') }}
            </a>

            <a href="{{ route('agency.profile.edit') }}"
               class="ag-nav-link {{ request()->routeIs('agency.profile.*') ? 'is-active' : '' }}">
                <svg class="ag-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                {{ __('ui.nav.profile') }}
            </a>

        </nav>

        <div class="ag-sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="ag-logout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="opacity:.7">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    {{ __('ui.nav.logout') }}
                </button>
            </form>
        </div>

    </aside>

    {{-- ── Main ─────────────────────────────────────────── --}}
    <div class="ag-main">

        <header class="ag-topbar">
            <div class="ag-topbar-left">
                <h1>@yield('page-title', __('ui.pages.agency_title'))</h1>
                @hasSection('page-subtitle')
                    <p class="ag-topbar-sub">@yield('page-subtitle')</p>
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                @yield('topbar-actions')
            </div>
        </header>

        <div class="ag-flash">@include('partials.flash')</div>

        <main class="ag-content page-transition-root">
            @yield('content')
        </main>

    </div>

</div>
@stack('scripts')
</body>
</html>
