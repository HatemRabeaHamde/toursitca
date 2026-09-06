@extends('layouts.site')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    @vite(['resources/css/landing.css', 'resources/css/experience-card.css', 'resources/css/experience-detail.css'])
@endpush

@section('hide_site_chrome', 'true')
@section('hide_site_flash', 'true')

@section('content')
    @php($experience = $detail->experience)

    @include('site.partials.xp-nav', ['experience' => $experience, 'searchNav' => $detail->searchNav])

    <div class="experience-detail">
        <div class="xp-container">

            {{-- Title block --}}
            @include('site.experiences.partials.detail-hero', ['detail' => $detail, 'experience' => $experience])

            <div class="xp-top-grid">
                <main class="xp-detail-main">
                    @include('site.experiences.partials.gallery', ['gallery' => $detail->gallery, 'title' => $detail->title])
                    @include('site.experiences.partials.detail-description', ['detail' => $detail])
                    @include('site.experiences.partials.detail-about-activity', ['detail' => $detail, 'experience' => $experience])

                    @if (count($detail->experienceOptions) > 0)
                        @include('site.experiences.partials.options-list', ['detail' => $detail])
                    @endif

                    <nav class="xp-tabs" aria-label="{{ __('ui.experience_detail.page_sections_label') }}">
                        <a href="#overview">{{ __('ui.experience_detail.the_experience_title') }}</a>
                        @if ($experience->inclusions || $experience->exclusions)
                            <a href="#included">{{ __('ui.experience_detail.included_title') }}</a>
                        @endif
                        @if ($detail->itinerary['has_items'])
                            <a href="#itinerary">{{ __('ui.experience_detail.itinerary.title') }}</a>
                        @endif
                        <a href="#important">{{ __('ui.experience_detail.important_info_title') }}</a>
                        <a href="#reviews">{{ __('ui.experience_detail.reviews_preview_title') }}</a>
                    </nav>

                    <div class="xp-body">
                        @include('site.experiences.partials.detail-experience', ['detail' => $detail, 'experience' => $experience])
                        @include('site.experiences.partials.detail-included', ['detail' => $detail, 'experience' => $experience])
                        @include('site.experiences.partials.detail-itinerary', ['detail' => $detail])
                        @include('site.experiences.partials.detail-important', ['detail' => $detail])
                        @include('site.experiences.partials.detail-reviews', ['detail' => $detail])
                    </div>
                </main>

                <aside id="booking-options" class="xp-sidebar">
                    <div class="xp-sidebar-sticky">
                        @include('site.experiences.partials.booking-panel', ['detail' => $detail, 'experience' => $experience])
                    </div>
                </aside>
            </div>
        </div>

        @include('site.experiences.partials.provider-experience-sections', ['detail' => $detail])

        @include('site.experiences.partials.detail-cta', ['detail' => $detail, 'experience' => $experience])

        @include('site.partials.footer')

    </div>

    {{-- Mobile bottom CTA (hidden on desktop) --}}
    <div class="xp-mobile-cta">
        <div class="xp-mobile-cta-inner">
            <div class="xp-mobile-cta-price">
                <p class="xp-mobile-cta-label">{{ __('ui.labels.from') }}</p>
                <p class="xp-mobile-cta-amount">{{ $detail->bookingSummary['currency'] }} {{ $detail->bookingSummary['price_from'] }}</p>
            </div>
            <a href="#booking-options" class="xp-mobile-book-btn">
                {{ __('ui.experience_detail.booking_panel.check_availability') }}
            </a>
        </div>
    </div>
@endsection
