{{-- Traveller reviews (Figma "08 · Traveller reviews — All reviews"). Data: ReviewsPageViewModel $page. --}}
@extends('layouts.site-v2')

@section('nav_active', 'experiences')

@push('page-styles')
    <link rel="preload" as="image" href="{{ asset(config('experiences.reviews.hero_image')) }}" fetchpriority="high">
    @vite(['resources/css/pages/reviews.css'])
@endpush

@section('page')
    <div id="content">
        <x-site.organisms.page-hero
            class="lv-page-hero--short"
            :image="asset(config('experiences.reviews.hero_image'))"
            :eyebrow="__('reviews.hero.eyebrow')"
            :title="__('reviews.hero.title')"
            :accent="__('reviews.hero.accent')"
            :lede="__('reviews.hero.lede')"
            :breadcrumb="$page->breadcrumb()" />

        @if($page->summary)
            @include('site.reviews.sections.summary')
        @endif

        @include('site.reviews.sections.directory')
    </div>
@endsection
