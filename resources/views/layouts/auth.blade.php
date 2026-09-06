<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $dir ?? 'ltr' }}">
    <head>
        @include('partials.head.meta')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900">
        <main class="page-transition-root flex min-h-screen items-center justify-center px-4 py-10">
            <section class="w-full max-w-md rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                <a href="{{ route('site.root') }}" class="mb-6 block text-center text-lg font-semibold text-slate-950">
                    {{ __('ui.brand') }}
                </a>

                @include('partials.flash')

                {{ $slot ?? '' }}
                @yield('content')
            </section>
        </main>
    </body>
</html>
