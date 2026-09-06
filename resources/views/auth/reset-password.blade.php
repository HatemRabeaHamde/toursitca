<x-auth.shell
    :show-tabs="false"
    :title="__('ui.auth.reset_title')"
    :subtitle="__('ui.auth.reset_subtitle')"
>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-field">
            <label for="email">{{ __('ui.fields.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="{{ __('ui.auth.email_placeholder') }}">
            @error('email')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password">{{ __('ui.fields.password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="{{ __('ui.auth.new_password_placeholder') }}">
            @error('password')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password_confirmation">{{ __('ui.fields.password_confirmation') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('ui.auth.confirm_password_placeholder') }}">
            @error('password_confirmation')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <button class="auth-submit" type="submit">{{ __('ui.auth.reset_submit') }}</button>
    </form>
</x-auth.shell>
