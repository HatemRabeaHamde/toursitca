{{-- Rate your trip (Figma "15 · Review — Rate your trip"). Data: Booking $booking, trip array $trip. --}}
@extends('layouts.site-v2')

@push('page-styles')
    @vite(['resources/css/pages/account.css'])
@endpush

@php($locale = app()->getLocale())
@php($words = [1 => __('account.review.words.1'), 2 => __('account.review.words.2'), 3 => __('account.review.words.3'), 4 => __('account.review.words.4'), 5 => __('account.review.words.5')])

@section('page')
    <div id="content" class="lv-flow">
        <div class="lv-container lv-flow__inner">
            <a href="{{ route('site.dashboard.bookings', ['locale' => $locale]) }}" class="lv-flow__back lv-t-ui-s lv-tone-muted"><x-site.atoms.icon name="arrow-right-ink" :size="16" class="lv-flow__back-icon" />{{ __('account.review.back') }}</a>
            <x-site.atoms.eyebrow>{{ __('account.review.eyebrow') }}</x-site.atoms.eyebrow>
            <h1 class="lv-t-h1 lv-tone-ink lv-flow__title">{{ __('account.review.title') }} <em class="lv-italic">{{ __('account.review.accent') }}</em></h1>
            <p class="lv-t-body-l lv-tone-muted lv-flow__lede">{{ __('account.review.lede') }}</p>

            <div class="lv-flow__grid mt-8">
                <form method="POST" action="{{ route('site.reviews.store', ['locale' => $locale, 'booking' => $booking->id]) }}" enctype="multipart/form-data" class="lv-flow__main">
                    @csrf

                    <section class="lv-flow-card">
                        <div>
                            <h2 class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.review.overall') }}</h2>
                            <p class="lv-t-body-s lv-tone-muted">{{ __('account.review.overall_help') }}</p>
                        </div>
                        <x-site.molecules.star-input name="rating" :label="__('account.review.overall')" :value="old('rating', 0)" :size="32" :words="$words" />
                        @error('rating')<p class="lv-form-error lv-t-caption" role="alert">{{ $message }}</p>@enderror
                    </section>

                    <section class="lv-flow-card">
                        <h2 class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.review.details') }}</h2>
                        <ul class="flex flex-col gap-5">
                            @foreach(['rating_host' => 'host', 'rating_organisation' => 'organisation', 'rating_pickup' => 'pickup', 'rating_value' => 'value'] as $column => $key)
                                <li class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="lv-t-label-m lv-tone-ink">{{ $key === 'host' && $trip['hostName'] ? __('account.review.host_named', ['name' => $trip['hostName']]) : __('account.review.'.$key) }}</p>
                                        <p class="lv-t-caption lv-tone-muted">{{ __('account.review.'.$key.'_help') }}</p>
                                    </div>
                                    <x-site.molecules.star-input :name="$column" :label="__('account.review.'.$key)" :value="old($column, 0)" />
                                </li>
                            @endforeach
                        </ul>
                    </section>

                    @include('site.account.partials.review-story')

                    <section class="lv-flow-card">
                        <div class="flex flex-wrap items-center gap-4">
                            <x-site.atoms.button type="submit">{{ __('account.review.publish') }}</x-site.atoms.button>
                            <p class="lv-t-caption lv-tone-muted">{{ __('account.review.publish_note') }}</p>
                        </div>
                    </section>
                </form>

                <aside class="lv-flow__aside">
                    <div class="lv-flow-summary">
                        <img src="{{ $trip['image'] }}" alt="" width="316" height="160" class="lv-flow-summary__image">
                        <span class="lv-pill lv-pill--confirmed lv-t-micro self-start">{{ __('account.bookings.status.completed') }} · {{ $trip['dates'] }}</span>
                        <p class="lv-t-h4 lv-serif lv-tone-ink">{{ $trip['title'] }}</p>
                        @if($trip['hostLine'])
                            <p class="flex items-center gap-2 lv-t-label-s lv-tone-ink">@if($trip['hostAvatar'])<img src="{{ $trip['hostAvatar'] }}" alt="" width="28" height="28" class="lv-avatar lv-avatar--28">@endif{{ $trip['hostLine'] }}</p>
                        @endif
                        <p class="lv-t-caption lv-tone-muted">{{ $trip['travellersShort'] }} · {{ $trip['group'] }} · {{ $trip['total'] }}</p>
                    </div>
                    <div class="lv-flow-trust">
                        <p class="lv-t-h5 lv-serif lv-tone-ink">{{ __('account.review.why') }}</p>
                        <ul class="flex flex-col gap-2.5">
                            <x-site.atoms.check-item>{{ __('account.review.why1') }}</x-site.atoms.check-item>
                            <x-site.atoms.check-item>{{ __('account.review.why2') }}</x-site.atoms.check-item>
                            <x-site.atoms.check-item>{{ __('account.review.why3') }}</x-site.atoms.check-item>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </div>
@endsection
