<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $dir ?? 'ltr' }}">
    <head>
        @include('partials.head.meta')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('auth.partials.auth-styles')
    </head>
    <body>
        <main class="auth-agency-page">
            <nav class="agency-top-nav">
                <a href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}" class="agency-logo">{{ config('brand.name_first') }}<em>{{ config('brand.name_second') }}</em></a>
                <a href="{{ route('login') }}" class="auth-switch">{{ __('ui.auth.agency_already_partner') }} <strong>{{ __('ui.auth.sign_in') }}</strong></a>
            </nav>

            <section class="agency-hero">
                <div>
                    <p class="agency-eyebrow">{{ __('ui.auth.agency_eyebrow') }}</p>
                    <h1>{!! __('ui.auth.agency_title') !!}</h1>
                    <p>{{ __('ui.auth.agency_subtitle') }}</p>
                </div>
                <div class="agency-perks">
                    @foreach (__('ui.auth.agency_perks') as $perk)
                        <div class="agency-perk"><span>{{ $perk['icon'] }}</span>{{ $perk['text'] }}</div>
                    @endforeach
                </div>
            </section>

            <section class="agency-main">
                <form method="POST" action="{{ route('agency.register.store') }}" class="agency-card">
                    @csrf

                    <div class="agency-card__head">
                        <p class="agency-section-label" style="border-top: 0; margin-top: 0; padding-top: 0;">{{ __('ui.auth.agency_application') }}</p>
                        <h2 class="auth-title">{{ __('ui.pages.agency_register_title') }}</h2>
                        <p class="auth-subtitle">{{ __('ui.pages.agency_register_intro') }}</p>
                    </div>

                    <div class="agency-card__body">
                        <p class="agency-section-label">{{ __('ui.auth.account_info') }}</p>

                        <div class="auth-field">
                            <label for="owner_name">{{ __('ui.fields.owner_name') }}</label>
                            <input id="owner_name" name="owner_name" type="text" value="{{ old('owner_name') }}" required autofocus placeholder="{{ __('ui.auth.name_placeholder') }}">
                            @error('owner_name') <p class="auth-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="auth-field-row">
                            <div class="auth-field">
                                <label for="email">{{ __('ui.fields.email') }}</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" placeholder="contact@agency.com">
                                @error('email') <p class="auth-error">{{ $message }}</p> @enderror
                            </div>

                            <div class="auth-field">
                                <label for="phone">{{ __('ui.fields.phone') }}</label>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="+212 600 000 000">
                                @error('phone') <p class="auth-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="auth-field-row">
                            <div class="auth-field">
                                <label for="password">{{ __('ui.fields.password') }}</label>
                                <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="{{ __('ui.auth.new_password_placeholder') }}">
                                @error('password') <p class="auth-error">{{ $message }}</p> @enderror
                            </div>

                            <div class="auth-field">
                                <label for="password_confirmation">{{ __('ui.fields.password_confirmation') }}</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="{{ __('ui.auth.confirm_password_placeholder') }}">
                                @error('password_confirmation') <p class="auth-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <p class="agency-section-label">{{ __('ui.auth.agency_details') }}</p>

                        <div class="auth-field">
                            <label for="agency_name">{{ __('ui.fields.agency_name') }}</label>
                            <input id="agency_name" name="agency_name" type="text" value="{{ old('agency_name') }}" required placeholder="Sahara Horizons Travel">
                            @error('agency_name') <p class="auth-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="auth-field">
                            <label for="city">{{ __('ui.fields.city') }}</label>
                            <input id="city" name="city" type="text" value="{{ old('city') }}" required placeholder="Marrakesh">
                            @error('city') <p class="auth-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="auth-field">
                            <label for="description">{{ __('ui.fields.description') }}</label>
                            <textarea id="description" name="description" placeholder="{{ __('ui.auth.agency_description_placeholder') }}">{{ old('description') }}</textarea>
                            @error('description') <p class="auth-error">{{ $message }}</p> @enderror
                        </div>

                        <button class="auth-submit auth-submit--clay" type="submit">{{ __('ui.nav.agency_register') }}</button>
                    </div>
                </form>

                <aside class="agency-sidebar">
                    <div class="agency-side-card">
                        <h3>{{ __('ui.auth.why_list_title') }}</h3>
                        @foreach (__('ui.auth.why_list_items') as $item)
                            <div style="margin-bottom: 16px;">
                                <strong>{{ $item['title'] }}</strong>
                                <p>{{ $item['body'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </aside>
            </section>
        </main>
    </body>
</html>
