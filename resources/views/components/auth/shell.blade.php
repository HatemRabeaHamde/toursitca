@props([
    'active' => null,
    'title' => null,
    'subtitle' => null,
    'showTabs' => true,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $dir ?? 'ltr' }}">
    <head>
        @include('partials.head.meta')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('auth.partials.auth-styles')
    </head>
    <body class="auth-body">
        <main class="auth-wrap">
            <section class="auth-brand-side" aria-label="{{ __('ui.auth.brand_panel_label') }}">
                @include('auth.partials.visual-panel')
            </section>

            <section class="auth-form-side">
                <div class="auth-form-inner">
                    <div class="auth-form-top">
                        <a href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}" class="auth-back">
                            <span aria-hidden="true">←</span>
                            {{ __('ui.auth.back_to_site') }}
                        </a>

                        @if ($showTabs)
                            <nav class="auth-mode-switch" aria-label="{{ __('ui.auth.auth_tabs_label') }}">
                                <a class="auth-mode-link @if ($active === 'login') is-active @endif" href="{{ route('login') }}">
                                    {{ __('ui.auth.sign_in') }}
                                </a>
                                <a class="auth-mode-link @if ($active === 'register') is-active @endif" href="{{ route('register') }}">
                                    {{ __('ui.auth.create_account') }}
                                </a>
                            </nav>
                        @endif
                    </div>

                    @if ($title || $subtitle)
                        <div class="auth-head">
                            @if ($title)
                                <h1 class="auth-title">{{ $title }}</h1>
                            @endif
                            @if ($subtitle)
                                <p class="auth-subtitle">{{ $subtitle }}</p>
                            @endif
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </section>
        </main>
    </body>
</html>
