<x-auth.shell
    :show-tabs="false"
    :title="__('ui.auth.confirm_title')"
    :subtitle="__('ui.auth.confirm_subtitle')"
>
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="auth-field">
            <label for="password">{{ __('ui.fields.password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="{{ __('ui.auth.password_placeholder') }}">
            @error('password')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <button class="auth-submit" type="submit">{{ __('ui.auth.confirm_submit') }}</button>
    </form>
</x-auth.shell>
