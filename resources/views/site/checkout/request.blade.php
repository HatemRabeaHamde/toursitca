{{-- Request to book (Figma "11 · Booking — Request to book"). Data: CheckoutSession $checkoutSession, trip array $trip. --}}
@extends('layouts.site-v2')

@section('nav_active', 'experiences')

@push('page-styles')
    @vite(['resources/css/pages/account.css'])
@endpush

@php($host = $trip['hostName'] ?? __('account.sent.your_host'))

@section('page')
    <div id="content" class="lv-flow relative overflow-x-clip">
        <x-site.atoms.ornament class="lv-flow__leaves" />

        <div class="lv-container lv-flow__inner">
            <a href="{{ $trip['href'] }}" class="lv-flow__back lv-t-ui-s lv-tone-muted"><x-site.atoms.icon name="arrow-right-ink" :size="16" class="lv-flow__back-icon" />{{ __('account.checkout.back', ['title' => $trip['title']]) }}</a>
            <x-site.atoms.eyebrow>{{ __('account.checkout.eyebrow') }}</x-site.atoms.eyebrow>
            <h1 class="lv-t-h1 lv-tone-ink lv-flow__title">{{ __('account.checkout.title') }} <em class="lv-italic">{{ __('account.checkout.accent') }}</em></h1>
            <p class="lv-t-body-l lv-tone-muted">{{ __('account.checkout.lede', ['name' => $host]) }}</p>

            @include('site.checkout.sections.steps', ['current' => 1])

            @foreach(['checkout_session', 'availability_id', 'contact', 'auth'] as $problem)
                @error($problem)<p class="lv-flow__alert lv-t-body-s" role="alert">{{ $message }}</p>@enderror
            @endforeach

            <div class="lv-flow__grid">
                <div class="lv-flow__main">
                    @include('site.checkout.sections.trip')

                    @guest
                        @include('site.checkout.sections.auth')
                    @else
                        @include('site.checkout.sections.contact')
                    @endguest
                </div>

                <aside class="lv-flow__aside" aria-label="{{ __('account.checkout.summary') }}">
                    @include('site.checkout.sections.summary')
                </aside>
            </div>
        </div>
    </div>
@endsection
