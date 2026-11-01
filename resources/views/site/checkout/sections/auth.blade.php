{{-- Guests sign in or create an account here; the booking is tied to that account. --}}
@php($authMode = ($errors->has('name') || $errors->has('preferred_lang') || $errors->has('password_confirmation') || old('name')) ? 'register' : 'login')

<section class="lv-flow-card" x-data="{ mode: @js($authMode) }" aria-labelledby="auth-title">
    <div>
        <h2 id="auth-title" class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.checkout.contact') }}</h2>
        <p class="lv-t-body-s lv-tone-muted">{{ __('ui.checkout.auth_intro') }}</p>
    </div>

    <div class="lv-flow-tabs" role="group" aria-label="{{ __('ui.checkout.auth_title') }}">
        <button type="button" class="lv-t-label-s" :class="{ 'is-active': mode === 'login' }" @click="mode = 'login'" :aria-pressed="(mode === 'login').toString()">{{ __('ui.auth.sign_in') }}</button>
        <button type="button" class="lv-t-label-s" :class="{ 'is-active': mode === 'register' }" @click="mode = 'register'" :aria-pressed="(mode === 'register').toString()">{{ __('ui.auth.create_account') }}</button>
    </div>

    <form method="POST" action="{{ route('site.checkout.login', ['locale' => app()->getLocale(), 'checkoutSession' => $checkoutSession]) }}" class="flex flex-col gap-4" x-show="mode === 'login'">
        @csrf
        <x-site.molecules.form-field name="email" id="login_email" type="email" :label="__('account.fields.email')" :value="old('email')" autocomplete="username" required />
        <x-site.molecules.form-field name="password" id="login_password" type="password" :label="__('ui.fields.password')" autocomplete="current-password" required />
        <label class="lv-check lv-t-body-s"><input type="checkbox" name="remember">{{ __('ui.auth.remember_me') }}</label>
        <x-site.atoms.button type="submit" class="self-start">{{ __('ui.checkout.sign_in_continue') }}</x-site.atoms.button>
    </form>

    <form method="POST" action="{{ route('site.checkout.register', ['locale' => app()->getLocale(), 'checkoutSession' => $checkoutSession]) }}" class="flex flex-col gap-4" x-show="mode === 'register'" x-cloak>
        @csrf
        <input type="hidden" name="preferred_lang" value="{{ app()->getLocale() }}">
        <x-site.molecules.form-field name="name" :label="__('ui.fields.owner_name')" :value="old('name')" autocomplete="name" required />
        <x-site.molecules.form-field name="email" id="register_email" type="email" :label="__('account.fields.email')" :value="old('email')" autocomplete="email" required />
        <div class="lv-form-grid">
            <x-site.molecules.form-field name="password" id="register_password" type="password" :label="__('ui.fields.password')" autocomplete="new-password" required />
            <x-site.molecules.form-field name="password_confirmation" type="password" :label="__('account.fields.password_confirm')" autocomplete="new-password" required />
        </div>
        <x-site.atoms.button type="submit" class="self-start">{{ __('ui.auth.create_account') }}</x-site.atoms.button>
    </form>
</section>
