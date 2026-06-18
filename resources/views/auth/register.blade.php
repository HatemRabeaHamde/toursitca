<x-auth.shell
    active="register"
    :title="__('ui.auth.register_title')"
    :subtitle="__('ui.auth.register_subtitle')"
>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-field">
            <label for="name">{{ __('ui.fields.owner_name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="{{ __('ui.auth.name_placeholder') }}">
            @error('name')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="email">{{ __('ui.fields.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="{{ __('ui.auth.email_placeholder') }}">
            @error('email')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="preferred_lang">{{ __('ui.fields.preferred_lang') }}</label>
            <select id="preferred_lang" name="preferred_lang" required>
                @foreach (config('locales.supported') as $code => $locale)
                    <option value="{{ $code }}" @selected(old('preferred_lang', app()->getLocale()) === $code)>
                        {{ $locale['native'] }}
                    </option>
                @endforeach
            </select>
            @error('preferred_lang')
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

        <button class="auth-submit" type="submit">{{ __('ui.auth.create_account') }}</button>

        <p class="auth-switch">
            {{ __('ui.auth.have_account') }}
            <a href="{{ route('login') }}">{{ __('ui.auth.sign_in') }}</a>
        </p>
    </form>
</x-auth.shell>
