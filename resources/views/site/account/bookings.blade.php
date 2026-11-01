{{-- My bookings (Figma "14 · Account — My bookings"). Data: array $page. --}}
@extends('site.account.layout', ['accountActive' => 'bookings'])

@section('nav_active', '')

@section('account_title'){{ __('account.bookings.title') }} <em class="lv-italic">{{ __('account.bookings.accent') }}</em>@endsection
@section('account_intro'){{ __('account.bookings.intro') }}@endsection

@section('account_content')
    @if(session('status'))<p class="lv-flow__alert lv-flow__alert--ok lv-t-body-s" role="status">{{ session('status') }}</p>@endif

    <nav class="lv-account-tabs" aria-label="{{ __('account.bookings.title') }} {{ __('account.bookings.accent') }}">
        @foreach($page['tabs'] as $tab)
            <a href="{{ $tab['href'] }}" @class(['lv-xtabs__link', 'is-active' => $page['tab'] === $tab['key']]) @if($page['tab'] === $tab['key']) aria-current="page" @endif>{{ $tab['label'] }} ({{ $tab['count'] }})</a>
        @endforeach
    </nav>

    @forelse($page['groups'] as $group => $cards)
        <section class="flex flex-col gap-4" aria-labelledby="group-{{ $group }}">
            <h2 id="group-{{ $group }}" class="lv-account-group lv-t-label-s">{{ __('account.bookings.groups.'.$group) }}</h2>
            @foreach($cards as $card)
                @include('site.account.partials.booking-card', ['card' => $card])
            @endforeach
        </section>
    @empty
        <div class="lv-xempty">
            <p class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.bookings.empty') }}</p>
            <x-site.atoms.link-arrow :href="route('site.experiences.index', ['locale' => app()->getLocale()])" arrow="terracotta">{{ __('account.bookings.browse') }}</x-site.atoms.link-arrow>
        </div>
    @endforelse

    <div class="lv-account-next">
        <div>
            <p class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.bookings.next') }}</p>
            <p class="lv-t-body-s lv-tone-muted">{{ __('account.bookings.next_body') }}</p>
        </div>
        <x-site.atoms.link-arrow :href="route('site.discover.index', ['locale' => app()->getLocale()])" arrow="terracotta">{{ __('account.bookings.next_link') }}</x-site.atoms.link-arrow>
    </div>
@endsection
