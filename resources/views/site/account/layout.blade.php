{{--
    Traveller account shell (Figma "13 · My profile", "14 · My bookings").
    Sections: account_title, account_intro, account_content. Variables: $accountActive, $accountAvatar, $accountInitial.
--}}
@extends('layouts.site-v2')

@push('page-styles')
    @vite(['resources/css/pages/account.css'])
@endpush

@php($locale = app()->getLocale())
@php($accountUser = auth()->user())
@php($accountAvatarUrl = $accountUser->avatar ? (\Illuminate\Support\Str::startsWith($accountUser->avatar, ['http://', 'https://', '/']) ? $accountUser->avatar : asset($accountUser->avatar)) : null)
@php($accountLinks = [
    'profile' => ['icon' => 'users', 'href' => route('profile.edit'), 'label' => __('account.nav.profile')],
    'bookings' => ['icon' => 'calendar', 'href' => route('site.dashboard.bookings', ['locale' => $locale]), 'label' => __('account.nav.bookings')],
    'reviews' => ['icon' => 'star', 'href' => route('profile.edit').'#reviews', 'label' => __('account.nav.reviews')],
    'wishlist' => ['icon' => 'heart', 'href' => route('site.dashboard.wishlist', ['locale' => $locale]), 'label' => __('account.nav.wishlist')],
])

@section('page')
    <div id="content">
        <header class="lv-account-band">
            <div class="lv-container lv-account-band__inner">
                @if($accountAvatarUrl)
                    <img src="{{ $accountAvatarUrl }}" alt="" width="104" height="104" class="lv-account-avatar">
                @else
                    <span class="lv-account-avatar lv-account-avatar--initial lv-serif" aria-hidden="true">{{ mb_strtoupper(mb_substr($accountUser->name, 0, 1)) }}</span>
                @endif
                <div class="flex min-w-0 flex-col gap-2">
                    <x-site.atoms.eyebrow>{{ __('account.nav.eyebrow') }}</x-site.atoms.eyebrow>
                    <h1 class="lv-t-h1 lv-tone-ink">@yield('account_title')</h1>
                    <p class="lv-t-body-m lv-tone-muted">@yield('account_intro')</p>
                </div>
            </div>
        </header>

        <div class="relative overflow-x-clip">
            <x-site.atoms.ornament class="lv-account-leaves" />
            <div class="lv-container lv-account">
                <nav class="lv-account-nav" aria-label="{{ __('account.nav.eyebrow') }}">
                    @foreach($accountLinks as $key => $link)
                        <a href="{{ $link['href'] }}" @class(['lv-account-nav__link lv-t-ui-s', 'is-active' => ($accountActive ?? '') === $key]) @if(($accountActive ?? '') === $key) aria-current="page" @endif>
                            <x-site.atoms.icon :name="$link['icon']" :size="18" />{{ $link['label'] }}
                        </a>
                    @endforeach
                    <hr class="lv-flow-rule">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="lv-account-nav__link lv-t-ui-s">{{ __('account.nav.sign_out') }}</button>
                    </form>
                </nav>
                <div class="lv-account__main">@yield('account_content')</div>
            </div>
        </div>
    </div>
@endsection
