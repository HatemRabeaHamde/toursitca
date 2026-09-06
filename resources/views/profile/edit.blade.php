@extends('layouts.tourist')

@section('content')
    <p class="td-eyebrow">{{ __('ui.pages.tourist_title') }}</p>
    <h1 class="td-h1">{{ __('ui.pages.profile_title') }}</h1>
    <p class="td-page-intro">{{ __('ui.dashboard.profile_intro') }}</p>

    @if (session('status') === 'profile-updated')
        <div class="td-alert" style="margin-top:24px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            {{ __('ui.messages.profile_updated') }}
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="td-alert" style="margin-top:24px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            {{ __('ui.messages.password_updated') }}
        </div>
    @endif

    {{-- ── Profile information ──────────────────────────────── --}}
    <div class="td-panel" style="margin-top:24px;">
        <p class="td-panel-title">{{ __('ui.dashboard.profile_info_title') }}</p>
        <p class="td-panel-sub">{{ __('ui.dashboard.profile_info_intro') }}</p>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="td-grid-2">
                <div class="td-field" style="margin-top:24px;">
                    <label for="name" class="td-label">{{ __('ui.fields.name') }}</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="td-input">
                    @error('name')
                        <p class="td-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="td-field" style="margin-top:24px;">
                    <label for="phone" class="td-label">{{ __('ui.fields.phone') }}</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" class="td-input">
                    @error('phone')
                        <p class="td-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="td-field">
                <label for="email" class="td-label">{{ __('ui.fields.email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="td-input">
                @error('email')
                    <p class="td-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="td-field">
                <label for="preferred_lang" class="td-label">{{ __('ui.fields.preferred_lang') }}</label>
                <select id="preferred_lang" name="preferred_lang" class="td-select">
                    @foreach (config('locales.supported', []) as $code => $meta)
                        <option value="{{ $code }}" @selected(old('preferred_lang', $user->preferred_lang) === $code)>
                            {{ $meta['native'] }}
                        </option>
                    @endforeach
                </select>
                @error('preferred_lang')
                    <p class="td-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="td-form-actions">
                <button type="submit" class="td-btn">{{ __('ui.nav.save_changes') }}</button>
            </div>
        </form>
    </div>

    {{-- ── Password ──────────────────────────────────────────── --}}
    <div class="td-panel">
        <p class="td-panel-title">{{ __('ui.dashboard.password_title') }}</p>
        <p class="td-panel-sub">{{ __('ui.dashboard.password_intro') }}</p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="td-field">
                <label for="current_password" class="td-label">{{ __('ui.fields.current_password') }}</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" class="td-input">
                @error('current_password', 'updatePassword')
                    <p class="td-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="td-grid-2">
                <div class="td-field">
                    <label for="password" class="td-label">{{ __('ui.fields.password') }}</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" class="td-input">
                    @error('password', 'updatePassword')
                        <p class="td-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="td-field">
                    <label for="password_confirmation" class="td-label">{{ __('ui.fields.password_confirmation') }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="td-input">
                    @error('password_confirmation', 'updatePassword')
                        <p class="td-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="td-form-actions">
                <button type="submit" class="td-btn">{{ __('ui.dashboard.update_password') }}</button>
            </div>
        </form>
    </div>

    {{-- ── Danger zone ───────────────────────────────────────── --}}
    <div class="td-panel is-danger">
        <p class="td-panel-title">{{ __('ui.dashboard.delete_account') }}</p>
        <p class="td-panel-sub">{{ __('ui.dashboard.delete_account_intro') }}</p>

        <form
            method="POST"
            action="{{ route('profile.destroy') }}"
            onsubmit="return confirm('{{ __('ui.dashboard.delete_account_confirm') }}')"
        >
            @csrf
            @method('delete')

            <div class="td-field">
                <label for="delete_password" class="td-label">{{ __('ui.fields.password') }}</label>
                <input id="delete_password" name="password" type="password" required class="td-input" style="max-width:280px;">
                @error('password', 'userDeletion')
                    <p class="td-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="td-form-actions" style="justify-content:flex-start;">
                <button type="submit" class="td-btn is-danger">{{ __('ui.dashboard.delete_account') }}</button>
            </div>
        </form>
    </div>
@endsection
