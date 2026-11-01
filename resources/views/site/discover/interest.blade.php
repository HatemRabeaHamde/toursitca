{{-- Interest guide template (Figma 77:1564 — Cuisine). Data: InterestPageViewModel $page. --}}
@extends('layouts.site-v2')

@section('nav_active', 'discover')

@push('page-styles')
    <link rel="preload" as="image" href="{{ $page->heroImage }}" fetchpriority="high">
    @vite(['resources/css/pages/discover.css'])
@endpush

@section('page')
    <div id="content">
        <x-site.organisms.page-hero
            :image="$page->heroImage"
            :eyebrow="$page->copy('hero.eyebrow', $page->name)"
            :title="$page->copy('hero.title', $page->name)"
            :accent="$page->copy('hero.accent')"
            :lede="$page->copy('hero.lede')"
            :breadcrumb="$page->breadcrumb"
            :primary="$page->copy('hero.primary') ? ['label' => $page->copy('hero.primary'), 'href' => $page->experiencesUrl] : null"
            :secondary="$page->copy('hero.secondary') && $page->regions ? ['label' => $page->copy('hero.secondary'), 'href' => '#regions'] : null" />

        @includeWhen($page->copy('meaning'), 'site.discover.sections.interest.meaning')
        @includeWhen($page->origins, 'site.discover.sections.interest.origins')
        @includeWhen($page->dishes, 'site.discover.sections.interest.dishes')
        @includeWhen($page->regions, 'site.discover.sections.interest.regions')
        @includeWhen($page->experiences, 'site.discover.sections.interest.experiences')
        @includeWhen($page->stories || $page->hosts, 'site.discover.sections.interest.community')
        @includeWhen($page->notes, 'site.discover.sections.interest.notes')
    </div>

    <x-site.organisms.cta-band
        :eyebrow="$page->copy('cta.eyebrow')"
        :title="$page->copy('cta.title')"
        :accent="$page->copy('cta.accent')"
        :lede="$page->copy('cta.lede')"
        :cta-label="$page->copy('cta.button', __('discover.destination.cta_button'))"
        :cta-href="$page->experiencesUrl" />
@endsection
