{{-- Thank you (Figma "16 · Review — Thank you"). Data: Booking $booking, Review $review. --}}
@extends('layouts.site-v2')

@push('page-styles')
    @vite(['resources/css/pages/account.css'])
@endpush

@php($locale = app()->getLocale())
@php($experienceTitle = $booking->experience->getTranslation('title', $locale, true))

@section('page')
    <div id="content" class="lv-flow">
        <div class="lv-container lv-sent lv-sent--narrow">
            <span class="lv-flow-tick"><x-site.atoms.icon name="check-dot" :size="28" /></span>
            <x-site.atoms.eyebrow>{{ __('account.thanks.eyebrow') }}</x-site.atoms.eyebrow>
            <h1 class="lv-t-h1 lv-tone-ink">{{ __('account.thanks.title') }} <em class="lv-italic">{{ $firstName }}.</em></h1>
            <p class="lv-t-body-l lv-tone-muted lv-sent__lede">{{ $booking->experience->host ? __('account.thanks.lede_host', ['name' => $booking->experience->host->name]) : __('account.thanks.lede') }}</p>

            <figure class="lv-thanks-card">
                <div class="flex items-center justify-between gap-3">
                    <x-site.atoms.stars :value="$review->rating" :size="18" />
                    <span class="lv-pill lv-pill--confirmed lv-t-micro">{{ __('account.profile.published') }}</span>
                </div>
                <blockquote class="lv-t-quote-s lv-tone-ink">“{{ $review->body }}”</blockquote>
                <figcaption class="flex items-center gap-2.5">
                    @if($avatar)<img src="{{ $avatar }}" alt="" width="40" height="40" class="lv-avatar">@endif
                    <span class="flex min-w-0 flex-col">
                        <span class="lv-t-label-m lv-tone-ink">{{ $author }}</span>
                        <span class="lv-t-caption lv-tone-muted">{{ $experienceTitle }} · {{ __('account.thanks.verified') }}</span>
                    </span>
                </figcaption>
                @if($review->photos->isNotEmpty())
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($review->photos as $photo)
                            <img src="{{ $photo->url() }}" alt="" width="96" height="96" loading="lazy" class="lv-account-photo lv-account-photo--small">
                        @endforeach
                    </div>
                @endif
            </figure>

            <x-site.atoms.button :href="route('site.dashboard.bookings', ['locale' => $locale])">{{ __('account.thanks.back') }}</x-site.atoms.button>

            <div class="lv-account-next lv-account-next--wide">
                <div>
                    <p class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.bookings.next') }}</p>
                    <p class="lv-t-body-s lv-tone-muted">{{ __('account.bookings.next_body') }}</p>
                </div>
                <x-site.atoms.link-arrow :href="route('site.discover.index', ['locale' => $locale])" arrow="terracotta">{{ __('account.bookings.next_link') }}</x-site.atoms.link-arrow>
            </div>
        </div>
    </div>
@endsection
