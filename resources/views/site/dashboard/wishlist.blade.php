@extends('layouts.tourist')

@section('content')
    @php($locale = app()->getLocale())

    <div class="td-page-head">
        <div>
            <p class="td-eyebrow">{{ __('ui.pages.tourist_title') }}</p>
            <h1 class="td-h1">{{ __('ui.pages.my_wishlist_title') }}</h1>
            <p class="td-page-intro">{{ __('ui.dashboard.my_wishlist_intro') }}</p>
        </div>

        <a href="{{ route('site.experiences.index', ['locale' => $locale]) }}" class="td-cta">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            {{ __('ui.nav.experiences') }}
        </a>
    </div>

    @if ($cards->isEmpty())
        <div class="td-empty">
            <span class="td-empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </span>
            <h2>{{ __('ui.messages.no_wishlist') }}</h2>
            <p>{{ __('ui.dashboard.no_wishlist_intro') }}</p>
            <a href="{{ route('site.experiences.index', ['locale' => $locale]) }}" class="td-cta">{{ __('ui.nav.experiences') }}</a>
        </div>
    @else
        <div class="td-wishlist-grid">
            @foreach ($cards as $card)
                @include('site.experiences.partials.card-grid', ['card' => $card])
            @endforeach
        </div>
    @endif
@endsection
