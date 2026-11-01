{{-- My profile (Figma "13 · Account — My profile"). Data: array $page. --}}
@extends('site.account.layout', ['accountActive' => 'profile'])

@section('nav_active', '')

@section('account_title'){{ __('account.profile.welcome') }} <em class="lv-italic">{{ $page['firstName'] }}.</em>@endsection
@section('account_intro'){{ $page['memberLine'] }}@endsection

@section('account_content')
    @if(session('status') === 'profile-updated')<p class="lv-flow__alert lv-flow__alert--ok lv-t-body-s" role="status">{{ __('ui.messages.profile_updated') }}</p>@endif
    @if(session('status') === 'password-updated')<p class="lv-flow__alert lv-flow__alert--ok lv-t-body-s" role="status">{{ __('ui.messages.password_updated') }}</p>@endif

    <section class="lv-flow-card" aria-labelledby="personal-title">
        <div>
            <h2 id="personal-title" class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.profile.personal') }}</h2>
            <p class="lv-t-body-s lv-tone-muted">{{ __('account.profile.personal_help') }}</p>
        </div>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="flex flex-col gap-4">
            @csrf
            @method('patch')
            <div class="flex items-center gap-3">
                @if($page['avatar'])
                    <img src="{{ $page['avatar'] }}" alt="" width="56" height="56" class="lv-avatar lv-avatar--56">
                @else
                    <span class="lv-account-avatar lv-account-avatar--initial lv-account-avatar--small lv-serif" aria-hidden="true">{{ $page['initial'] }}</span>
                @endif
                <div class="flex flex-col gap-1">
                    <label for="avatar" class="lv-t-label-s lv-tone-ink">{{ __('account.profile.photo') }}</label>
                    <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="lv-t-caption lv-tone-muted">
                    @error('avatar')<p class="lv-form-error lv-t-caption" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="lv-form-grid">
                <x-site.molecules.form-field name="first_name" :label="__('account.fields.first_name')" :value="old('first_name', $page['firstName'])" autocomplete="given-name" required />
                <x-site.molecules.form-field name="last_name" :label="__('account.fields.last_name')" :value="old('last_name', $page['lastName'])" autocomplete="family-name" />
                <x-site.molecules.form-field name="email" type="email" :label="__('account.fields.email')" :value="old('email', $page['user']->email)" autocomplete="email" required />
                <x-site.molecules.form-field name="phone" type="tel" :label="__('account.fields.phone')" :value="old('phone', $page['user']->phone)" autocomplete="tel" maxlength="20" />
                <x-site.molecules.form-field name="country" :label="__('account.fields.country')" :value="old('country', $page['user']->country)" autocomplete="country-name" />
                <div class="lv-form-field">
                    <label for="preferred_lang" class="lv-t-label-s lv-tone-ink">{{ __('account.fields.language') }}</label>
                    <select id="preferred_lang" name="preferred_lang" class="lv-form-input">
                        @foreach(config('locales.supported') as $code => $language)
                            <option value="{{ $code }}" @selected(old('preferred_lang', $page['user']->preferred_lang) === $code)>{{ $language['native'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @error('name')<p class="lv-form-error lv-t-caption" role="alert">{{ $message }}</p>@enderror
            <div class="flex flex-wrap items-center gap-4">
                <x-site.atoms.button type="submit">{{ __('account.profile.save') }}</x-site.atoms.button>
                <p class="lv-t-caption lv-tone-muted">{{ __('account.profile.updated', ['date' => $page['updatedAt']]) }}</p>
            </div>
        </form>
    </section>

    @include('site.account.partials.profile-reviews')
    @include('site.account.partials.profile-security')
@endsection
