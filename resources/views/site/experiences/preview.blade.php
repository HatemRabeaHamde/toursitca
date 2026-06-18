@extends('layouts.site')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    @vite(['resources/css/landing.css', 'resources/css/experience-detail.css'])
@endpush

@section('hide_site_flash', 'true')

@section('content')
    @php($experience = $detail->experience)

    @include('site.partials.landing-nav', ['solid' => true])

    <div class="experience-detail">
        <div class="ed-wrap">
            @include('site.experiences.partials.detail-hero', ['detail' => $detail, 'experience' => $experience])

            <nav class="ed-section-nav" aria-label="{{ __('ui.experience_detail.page_sections_label') }}">
                <a href="#description">{{ __('ui.experience_detail.the_experience_title') }}</a>
                <a href="#overview">{{ __('ui.experience_detail.what_you_will_do_title') }}</a>
                @if ($experience->inclusions || $experience->exclusions)
                    <a href="#included">{{ __('ui.experience_detail.included_title') }}</a>
                @endif
                <a href="#important">{{ __('ui.experience_detail.important_info_title') }}</a>
                <a href="#reviews">{{ __('ui.experience_detail.reviews_preview_title') }}</a>
            </nav>

            <div class="ed-main">
                @include('site.experiences.partials.detail-overview', ['detail' => $detail, 'experience' => $experience])
                @include('site.experiences.partials.detail-extra-sections', ['detail' => $detail])
            </div>

            @include('site.experiences.partials.recommendations', ['recommendations' => $detail->recommendations])
        </div>
    </div>

    <div class="ed-mobile-cta">
        <div class="ed-mobile-cta-inner">
            <div>
                <p class="text-xs text-[var(--ink-70)]">{{ __('ui.labels.from') }}</p>
                <p class="text-base font-semibold text-[var(--ink)]">{{ $detail->bookingSummary['currency'] }} {{ $detail->bookingSummary['price_from'] }}</p>
            </div>
            <x-ui.button href="{{ $detail->bookingUrl }}" class="shrink-0 !rounded-full !bg-[var(--ink)]">
                {{ __('ui.nav.book_now') }}
            </x-ui.button>
        </div>
    </div>
@endsection
