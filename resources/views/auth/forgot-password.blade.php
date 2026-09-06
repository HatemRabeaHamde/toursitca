<x-auth.shell
    :show-tabs="false"
    :title="__('ui.auth.forgot_title')"
    :subtitle="__('ui.auth.forgot_subtitle')"
>
    <x-auth-session-status class="auth-status" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="auth-field">
            <label for="email">{{ __('ui.fields.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="{{ __('ui.auth.email_placeholder') }}">
            @error('email')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-actions">
            <a class="auth-secondary-link" href="{{ route('login') }}">{{ __('ui.auth.back_to_sign_in') }}</a>
            <button class="auth-submit" type="submit">{{ __('ui.auth.email_reset_link') }}</button>
        </div>
    </form>
</x-auth.shell>
