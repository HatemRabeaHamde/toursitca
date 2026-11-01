{{-- Local hosts directory (Figma: Local hosts). Data: HostsPageViewModel $page. --}}
@extends('layouts.site-v2')

@section('nav_active', 'hosts')

@push('page-styles')
    <link rel="preload" as="image" href="{{ asset(config('discover.images.hosts_hero')) }}" fetchpriority="high">
    @vite(['resources/css/pages/hosts.css'])
@endpush

@section('page')
    <div id="content">
        <x-site.organisms.page-hero
            class="lv-page-hero--compact"
            :image="asset(config('discover.images.hosts_hero'))"
            :eyebrow="__('hosts.hero.eyebrow')"
            :title="__('hosts.hero.title')"
            :accent="__('hosts.hero.accent')"
            :lede="__('hosts.hero.lede')"
            :breadcrumb="$page->breadcrumb" />

        @include('site.hosts.sections.directory')

        <x-site.organisms.vetting-steps id="vetting"
            :eyebrow="__('hosts.vetting.eyebrow')"
            :title="__('hosts.vetting.title')"
            :accent="__('hosts.vetting.accent')"
            :steps="$page->vettingSteps()" />
    </div>

    <x-site.organisms.join-band
        :title="__('hosts.join.title')"
        :lede="__('hosts.join.lede')"
        :cta-label="__('hosts.join.button')"
        :cta-href="$page->becomeHostUrl" />
@endsection
