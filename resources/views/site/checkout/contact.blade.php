@extends('layouts.site')

@section('hide_site_chrome', 'true')
@section('hide_site_flash', 'true')

@push('styles')
    @vite(['resources/css/booking-flow.css'])
@endpush

@php
    $authMode = ($errors->has('name') || $errors->has('preferred_lang') || $errors->has('password_confirmation') || old('name') || old('preferred_lang')) ? 'register' : 'login';
    $user = auth()->user();
    $userName = trim((string) ($user?->name ?? ''));
    $userNameParts = $userName !== '' ? preg_split('/\s+/', $userName, 2) : [];
    $userFirstName = $userNameParts[0] ?? '';
    $userLastName = $userNameParts[1] ?? '';
@endphp

@section('content')
    <div class="booking-flow">
        <div class="booking-shell">
            @include('site.checkout.partials.steps', ['currentStep' => 'contact'])

            <div class="booking-grid">
                <main class="booking-main">
                    @guest
                        <section class="booking-intro">
                            <p class="booking-kicker">{{ __('ui.checkout.steps.contact') }}</p>
                            <h1 class="booking-title">{{ __('ui.checkout.auth_title') }}</h1>
                            <p class="booking-copy">{{ __('ui.checkout.auth_intro') }}</p>
                        </section>

                        @error('auth')
                            <p class="booking-error">{{ $message }}</p>
                        @enderror

                        <section class="booking-card booking-auth-shell" x-data="{ mode: @js($authMode) }">
                            <div class="booking-auth-head">
                                <div>
                                    <h2 class="booking-section-title" x-text="mode === 'login' ? @js(__('ui.checkout.sign_in_title')) : @js(__('ui.checkout.register_title'))"></h2>
                                    <p class="booking-auth-copy" x-text="mode === 'login' ? @js(__('ui.checkout.sign_in_copy')) : @js(__('ui.checkout.register_copy'))"></p>
                                </div>

                                <div class="booking-auth-tabs" role="tablist" aria-label="{{ __('ui.checkout.auth_title') }}">
                                    <button type="button" class="booking-auth-tab" :class="{ 'is-active': mode === 'login' }" @click="mode = 'login'">
                                        {{ __('ui.auth.sign_in') }}
                                    </button>
                                    <button type="button" class="booking-auth-tab" :class="{ 'is-active': mode === 'register' }" @click="mode = 'register'">
                                        {{ __('ui.auth.create_account') }}
                                    </button>
                                </div>
                            </div>

                            <div class="booking-auth-body">
                                <form method="POST" action="{{ route('site.checkout.login', ['locale' => app()->getLocale(), 'checkoutSession' => $checkoutSession]) }}" data-checkout-form x-show="mode === 'login'" x-transition.opacity.duration.150ms>
                                    @csrf

                                    <div class="booking-field">
                                        <label for="checkout_login_email" class="booking-label">{{ __('ui.fields.email') }}</label>
                                        <input id="checkout_login_email" name="email" type="email" class="booking-input" value="{{ old('email') }}" required autocomplete="username">
                                        @error('email')
                                            <p class="booking-error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="booking-field">
                                        <label for="checkout_login_password" class="booking-label">{{ __('ui.fields.password') }}</label>
                                        <input id="checkout_login_password" name="password" type="password" class="booking-input" required autocomplete="current-password">
                                        @error('password')
                                            <p class="booking-error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <label class="booking-auth-check">
                                        <input type="checkbox" name="remember">
                                        <span>{{ __('ui.auth.remember_me') }}</span>
                                    </label>

                                    <button type="submit" class="booking-primary booking-primary--full">
                                        {{ __('ui.checkout.sign_in_continue') }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('site.checkout.register', ['locale' => app()->getLocale(), 'checkoutSession' => $checkoutSession]) }}" data-checkout-form x-show="mode === 'register'" x-transition.opacity.duration.150ms x-cloak>
                                    @csrf

                                    <div class="booking-form-grid">
                                        <div class="booking-field">
                                            <label for="checkout_register_name" class="booking-label">{{ __('ui.fields.owner_name') }}</label>
                                            <input id="checkout_register_name" name="name" class="booking-input" value="{{ old('name') }}" required autocomplete="name">
                                            @error('name')
                                                <p class="booking-error">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="booking-field">
                                            <label for="checkout_register_preferred_lang" class="booking-label">{{ __('ui.fields.preferred_lang') }}</label>
                                            <select id="checkout_register_preferred_lang" name="preferred_lang" class="booking-input" required>
                                                @foreach (config('locales.supported') as $code => $locale)
                                                    <option value="{{ $code }}" @selected(old('preferred_lang', app()->getLocale()) === $code)>
                                                        {{ $locale['native'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('preferred_lang')
                                                <p class="booking-error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="booking-field">
                                        <label for="checkout_register_email" class="booking-label">{{ __('ui.fields.email') }}</label>
                                        <input id="checkout_register_email" name="email" type="email" class="booking-input" value="{{ old('email') }}" required autocomplete="username">
                                        @error('email')
                                            <p class="booking-error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="booking-form-grid">
                                        <div class="booking-field">
                                            <label for="checkout_register_password" class="booking-label">{{ __('ui.fields.password') }}</label>
                                            <input id="checkout_register_password" name="password" type="password" class="booking-input" required autocomplete="new-password">
                                            @error('password')
                                                <p class="booking-error">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="booking-field">
                                            <label for="checkout_register_password_confirmation" class="booking-label">{{ __('ui.fields.password_confirmation') }}</label>
                                            <input id="checkout_register_password_confirmation" name="password_confirmation" type="password" class="booking-input" required autocomplete="new-password">
                                        </div>
                                    </div>

                                    <button type="submit" class="booking-primary booking-primary--full">
                                        {{ __('ui.checkout.register_continue') }}
                                    </button>
                                </form>
                            </div>
                        </section>
                    @else
                        <section class="booking-intro booking-intro--contact">
                            <p class="booking-kicker">{{ __('ui.checkout.steps.contact') }}</p>
                            <h1 class="booking-title">{{ __('ui.pages.checkout_contact_title') }}</h1>
                            <p class="booking-copy">{{ __('ui.checkout.contact_intro') }}</p>
                            <div class="booking-user-strip">
                                <span>{{ strtoupper(mb_substr($user?->name ?? $user?->email ?? 'T', 0, 1)) }}</span>
                                <strong>{{ $user?->email }}</strong>
                            </div>
                        </section>

                        @error('contact')
                            <p class="booking-error">{{ $message }}</p>
                        @enderror

                        <form method="POST" action="{{ route('site.checkout.contact.store', ['locale' => app()->getLocale(), 'checkoutSession' => $checkoutSession]) }}" class="booking-card booking-card-pad booking-contact-card" data-checkout-form>
                            @csrf

                            <div class="booking-form-grid">
                                <div class="booking-field">
                                    <label for="contact_first_name" class="booking-label">{{ __('ui.fields.contact_first_name') }}</label>
                                    <input id="contact_first_name" name="contact_first_name" class="booking-input" value="{{ old('contact_first_name', $checkoutSession->contact_first_name ?? $userFirstName) }}" required autocomplete="given-name">
                                    @error('contact_first_name')
                                        <p class="booking-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="booking-field">
                                    <label for="contact_last_name" class="booking-label">{{ __('ui.fields.contact_last_name') }}</label>
                                    <input id="contact_last_name" name="contact_last_name" class="booking-input" value="{{ old('contact_last_name', $checkoutSession->contact_last_name ?? $userLastName) }}" required autocomplete="family-name">
                                    @error('contact_last_name')
                                        <p class="booking-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="booking-field">
                                <label for="contact_email" class="booking-label">{{ __('ui.fields.contact_email') }}</label>
                                <input id="contact_email" name="contact_email" type="email" class="booking-input" value="{{ old('contact_email', $checkoutSession->contact_email ?? $user?->email) }}" required autocomplete="email">
                                @error('contact_email')
                                    <p class="booking-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="booking-form-grid">
                                <div class="booking-field">
                                    <label for="contact_phone" class="booking-label">{{ __('ui.fields.contact_phone') }}</label>
                                    <input id="contact_phone" name="contact_phone" class="booking-input" value="{{ old('contact_phone', $checkoutSession->contact_phone ?? $user?->phone) }}" autocomplete="tel">
                                    @error('contact_phone')
                                        <p class="booking-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="booking-field">
                                    <label for="contact_country" class="booking-label">{{ __('ui.fields.contact_country') }}</label>
                                    <input id="contact_country" name="contact_country" class="booking-input" value="{{ old('contact_country', $checkoutSession->contact_country) }}" autocomplete="country-name">
                                    @error('contact_country')
                                        <p class="booking-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="booking-field">
                                <label for="special_requests" class="booking-label">{{ __('ui.fields.special_requests') }}</label>
                                <textarea id="special_requests" name="special_requests" class="booking-textarea">{{ old('special_requests', $checkoutSession->special_requests) }}</textarea>
                                @error('special_requests')
                                    <p class="booking-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="booking-actions">
                                <button type="submit" class="booking-primary">
                                    {{ __('ui.nav.next_payment') }}
                                </button>
                            </div>
                        </form>
                    @endguest
                </main>

                @include('site.checkout.partials.summary', ['checkoutSession' => $checkoutSession])
            </div>
        </div>
    </div>
@endsection
