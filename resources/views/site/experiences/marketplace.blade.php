{{-- Experiences marketplace (Figma "07 · Experiences — Marketplace v2"). Data: ExperienceMarketplaceViewModel $page. --}}
@extends('layouts.site-v2')

@section('nav_active', 'experiences')

@push('page-styles')
    <link rel="preload" as="image" href="{{ asset(config('experiences.marketplace.hero_image')) }}" fetchpriority="high">
    @vite(['resources/css/pages/experiences.css'])
@endpush

@section('page')
    <div id="content" x-data="experienceMarketplace" @click="navigate($event)">
        @include('site.experiences.marketplace.sections.hero')
        @include('site.experiences.marketplace.sections.sticky-bar')
        @include('site.experiences.marketplace.sections.results')
    </div>

    @if($page->partners)
        @include('site.experiences.marketplace.sections.partners')
    @endif

    @if($page->reviews)
        @include('site.experiences.marketplace.sections.reviews')
    @endif
@endsection
