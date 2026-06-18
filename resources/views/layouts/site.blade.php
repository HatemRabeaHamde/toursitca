<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $dir ?? 'ltr' }}">
    <head>
        @include('partials.head.meta')
        @include('partials.head.og')
        @include('partials.head.schema')
        @include('partials.head.analytics')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="min-h-screen bg-white text-slate-900">
        <main>
            @unless($__env->yieldContent('hide_site_chrome') === 'true' || $__env->yieldContent('hide_site_flash') === 'true')
                <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                    @include('partials.flash')
                </div>
            @endunless
            @yield('content')
        </main>

        @unless($__env->yieldContent('hide_site_chrome') === 'true')
            @include('site.partials.footer')
        @endunless
        @stack('scripts')
    </body>
</html>
