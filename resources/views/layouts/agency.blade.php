<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $dir ?? 'ltr' }}">
    <head>
        @include('partials.head.meta')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900">
        <div class="flex min-h-screen">
            <aside class="hidden w-64 border-r border-slate-200 bg-white p-6 lg:block">
                <a href="{{ route('agency.dashboard') }}" class="text-base font-semibold">{{ __('ui.pages.agency_title') }}</a>
                <nav class="mt-6 space-y-2 text-sm">
                    <a class="{{ request()->routeIs('agency.dashboard') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }} block rounded-md px-3 py-2" href="{{ route('agency.dashboard') }}">{{ __('ui.nav.dashboard') }}</a>
                    <a class="{{ request()->routeIs('agency.experiences.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }} block rounded-md px-3 py-2" href="{{ route('agency.experiences.index') }}">{{ __('ui.nav.experiences') }}</a>
                    <a class="{{ request()->routeIs('agency.availability.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }} block rounded-md px-3 py-2" href="{{ route('agency.availability.index') }}">{{ __('ui.nav.availability') }}</a>
                    <a class="{{ request()->routeIs('agency.bookings.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }} block rounded-md px-3 py-2" href="{{ route('agency.bookings.index') }}">{{ __('ui.nav.bookings') }}</a>
                </nav>
            </aside>
            <div class="flex min-w-0 flex-1 flex-col">
                <header class="border-b border-slate-200 bg-white px-4 py-4 sm:px-6 lg:px-8">
                    <h1 class="text-lg font-semibold">@yield('title', __('ui.pages.agency_title'))</h1>
                </header>
                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                    @include('partials.flash')
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
