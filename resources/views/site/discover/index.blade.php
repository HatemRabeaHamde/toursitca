{{-- Discover Morocco hub (Figma 75:952). Data: DiscoverPageViewModel $page. --}}
@extends('layouts.site-v2')

@section('nav_active', 'discover')

@push('page-styles')
    <link rel="preload" as="image" href="{{ asset(config('discover.images.hero')) }}" fetchpriority="high">
    @vite(['resources/css/pages/discover.css'])
@endpush

@section('page')
    <div id="content">
        <x-site.organisms.page-hero
            size="tall"
            :image="asset(config('discover.images.hero'))"
            :eyebrow="__('discover.hub.hero.eyebrow')"
            :title="__('discover.hub.hero.title')"
            :accent="__('discover.hub.hero.accent')"
            :lede="__('discover.hub.hero.lede')"
            :breadcrumb="$page->urls->breadcrumb([['label' => __('discover.breadcrumb.discover'), 'href' => null]])"
            :primary="['label' => __('discover.hub.hero.primary'), 'href' => '#destinations']"
            :secondary="['label' => __('discover.hub.hero.secondary'), 'href' => '#interests']" />

        @include('site.discover.sections.hub.routes')
        @include('site.discover.sections.hub.places')
        @include('site.discover.sections.hub.interests')
        @includeWhen($page->stories, 'site.discover.sections.hub.stories')
        @includeWhen($page->experiences, 'site.discover.sections.hub.experiences')
        @includeWhen($page->hosts, 'site.discover.sections.hub.hosts')
        @includeWhen($page->faqs, 'site.discover.sections.hub.practical')
    </div>

    <x-site.organisms.cta-band
        :eyebrow="__('discover.hub.cta.eyebrow')"
        :title="__('discover.hub.cta.title')"
        :accent="__('discover.hub.cta.accent')"
        :lede="__('discover.hub.cta.lede')"
        :cta-label="__('discover.hub.cta.button')"
        :cta-href="$page->urls->experiences()" />
@endsection
