{{-- Experience detail v2 (Figma "09 · Experience detail v2 — Erg Chigaga"). Data: ExperiencePageViewModel $page. --}}
@extends('layouts.site-v2')

@section('nav_active', 'experiences')

@push('page-styles')
    <link rel="preload" as="image" href="{{ $page->hero['image'] }}" fetchpriority="high">
    @vite(['resources/css/pages/experience-detail.css'])
@endpush

@section('page')
    <div id="content" x-data="photoViewer(@js($page->gallery))">
        @include('site.experiences.detail.sections.hero')
        @include('site.experiences.detail.sections.tabs')

        <div class="lv-container lv-xdetail">
            <div class="lv-xdetail__main">
                @include('site.experiences.detail.sections.overview')
                @if($page->itinerary)@include('site.experiences.detail.sections.itinerary')@endif
                @if($page->included['items'])@include('site.experiences.detail.sections.included')@endif
                @if($page->host)@include('site.experiences.detail.sections.host')@endif
                @if($page->reviews)@include('site.experiences.detail.sections.reviews')@endif
            </div>

            <aside id="book" class="lv-xdetail__aside" aria-label="{{ __('experiences.detail.booking.label') }}">
                @include('site.experiences.detail.sections.booking')
            </aside>
        </div>

        @if($page->related)@include('site.experiences.detail.sections.related')@endif

        @if($page->gallery)@include('site.experiences.detail.partials.photos')@endif
        @include('site.experiences.detail.partials.mobile-bar')
    </div>
@endsection
