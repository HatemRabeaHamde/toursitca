<x-auth.shell
    active="login"
    :title="__('ui.auth.login_title')"
    :subtitle="__('ui.auth.login_subtitle')"
>
    <x-auth-session-status class="auth-status" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-field">
            <label for="email">{{ __('ui.fields.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="{{ __('ui.auth.email_placeholder') }}">
            @error('email')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password">{{ __('ui.fields.password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="{{ __('ui.auth.password_placeholder') }}">
            @error('password')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-form-row">
            <div class="auth-check">
                <input id="remember_me" type="checkbox" name="remember">
                <label for="remember_me">{{ __('ui.auth.remember_me') }}</label>
            </div>

            @if (Route::has('password.request'))
                <a class="auth-forgot" href="{{ route('password.request') }}">{{ __('ui.auth.forgot_password') }}</a>
            @endif
        </div>

        <button class="auth-submit" type="submit">{{ __('ui.auth.sign_in') }}</button>

        <p class="auth-switch">
            {{ __('ui.auth.no_account') }}
            <a href="{{ route('register') }}">{{ __('ui.auth.sign_up_free') }}</a>
        </p>
    </form>
</x-auth.shell>
