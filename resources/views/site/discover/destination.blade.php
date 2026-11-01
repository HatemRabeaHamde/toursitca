{{-- Destination guide template (Figma 76:1269 — Marrakech). Data: DestinationPageViewModel $page. --}}
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
            :location="$page->copy('hero.location')"
            :title="$page->copy('hero.title', $page->name)"
            :accent="$page->copy('hero.accent')"
            :lede="$page->copy('hero.lede')"
            :breadcrumb="$page->breadcrumb"
            :primary="['label' => __('discover.destination.primary', ['city' => $page->name]), 'href' => $page->experiencesUrl]"
            :secondary="$page->hosts ? ['label' => __('discover.destination.secondary'), 'href' => '#hosts'] : null" />

        @if($page->facts())
            <div class="lv-container relative z-10">
                <x-site.molecules.fact-bar :facts="$page->facts()" />
            </div>
        @endif

        <div class="lv-guide relative">
            <x-site.atoms.ornament class="lv-ornament-leaves--guide" data-parallax="16" />

            <div class="lv-container relative grid gap-12 lg:grid-cols-[216px_minmax(0,1fr)] lg:gap-12">
                <aside class="hidden lg:block">
                    <x-site.molecules.guide-toc class="lv-toc--sticky" :title="__('discover.destination.in_this_guide')"
                        :links="$page->tocLinks()">
                        <div class="lv-toc__start">
                            <p class="lv-t-h6 lv-tone-ink">{{ __('discover.destination.start.title') }}</p>
                            @if($page->copy('start.body'))<p class="lv-t-body-s lv-tone-muted">{{ $page->copy('start.body') }}</p>@endif
                            <x-site.atoms.link-arrow :href="$page->experiencesUrl" class="pt-1">{{ __('discover.destination.start.link') }}</x-site.atoms.link-arrow>
                        </div>
                    </x-site.molecules.guide-toc>
                </aside>

                <div class="flex min-w-0 flex-col gap-20">
                    @foreach($page->chapters() as $chapter)
                        @include('site.discover.sections.destination.'.$chapter)
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <x-site.organisms.cta-band
        :eyebrow="$page->copy('cta.eyebrow')"
        :title="$page->copy('cta.title')"
        :accent="$page->copy('cta.accent')"
        :lede="$page->copy('cta.lede')"
        :cta-label="__('discover.destination.cta_button')"
        :cta-href="$page->experiencesUrl" />
@endsection
