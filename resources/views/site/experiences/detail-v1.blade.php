{{-- Experience detail v1 (Figma "09 · Experience detail v1"). Data: ExperiencePageViewModel $page. --}}
@extends('layouts.site-v2')

@section('nav_active', 'experiences')

@push('page-styles')
    <link rel="preload" as="image" href="{{ $page->hero['image'] }}" fetchpriority="high">
    @vite(['resources/css/pages/experience-detail.css', 'resources/css/pages/experience-detail-v1.css'])
@endpush

@section('page')
    <div id="content" x-data="photoViewer(@js($page->gallery))">
        @include('site.experiences.detail-v1.sections.hero')

        <div class="relative overflow-x-clip">
            <x-site.atoms.ornament class="lv-v1-leaves" />

            <div class="lv-container lv-v1"
                 x-data="experienceBookingCard(@js(['store' => $page->booking['store'], 'card' => $page->booking['card'], 'options' => $page->booking['options']]))"
                 @keydown.escape.window="dateOpen = false; closeGuests()">
                <div class="lv-v1__main">
                    @include('site.experiences.detail-v1.sections.tabs')
                    @if($page->highlights)@include('site.experiences.detail-v1.sections.highlights')@endif
                    @if($page->booking['options'])@include('site.experiences.detail-v1.sections.options')@endif
                    @if($page->itinerary)@include('site.experiences.detail-v1.sections.itinerary')@endif
                    @if($page->included['items'])@include('site.experiences.detail-v1.sections.included')@endif
                    @if($page->host)@include('site.experiences.detail-v1.sections.host')@endif
                    @if($page->location)@include('site.experiences.detail-v1.sections.location')@endif
                    @if($page->reviews)@include('site.experiences.detail-v1.sections.reviews')@endif
                </div>

                <aside id="book" class="lv-v1__aside" aria-label="{{ __('experiences.detail.booking.label') }}">
                    @include('site.experiences.detail-v1.sections.booking')
                    @if($page->related)@include('site.experiences.detail-v1.sections.pairs')@endif
                </aside>

                @include('site.experiences.detail-v1.sections.mobile-bar')
            </div>
        </div>

        <x-site.organisms.cta-band
            :eyebrow="__('experiences.detail.v1.cta.eyebrow')"
            :title="__('experiences.detail.v1.cta.title')"
            :accent="__('experiences.detail.v1.cta.accent')"
            :lede="__('experiences.detail.v1.cta.lede')"
            :cta-label="__('experiences.detail.v1.cta.button')"
            :cta-href="route('site.discover.index', ['locale' => app()->getLocale()])" />

        @if($page->gallery)@include('site.experiences.detail.partials.photos')@endif
    </div>
@endsection
