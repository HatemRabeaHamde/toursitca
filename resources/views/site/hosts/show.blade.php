{{-- Host profile (Figma "06 · Host profile — Hassan"). Data: HostProfileViewModel $page. --}}
@extends('layouts.site-v2')

@section('nav_active', 'hosts')

@push('page-styles')
    <link rel="preload" as="image" href="{{ $page->cover }}" fetchpriority="high">
    @vite(['resources/css/pages/host-profile.css'])
@endpush

@section('page')
    <div id="content">
        @include('site.hosts.profile.hero')

        <div class="relative overflow-x-clip">
            <x-site.atoms.ornament class="lv-hp-leaves" />

            <div class="lv-container lv-hp">
                <div class="lv-hp__main">
                    @if($page->story)@include('site.hosts.profile.about')@endif
                    @if($page->skills)@include('site.hosts.profile.skills')@endif
                    @if($page->experiences)@include('site.hosts.profile.experiences')@endif
                    @if($page->reviews)@include('site.hosts.profile.reviews')@endif
                </div>

                @if($page->featured() || $page->trust)
                    <aside class="lv-hp__aside">@include('site.hosts.profile.sidebar')</aside>
                @endif
            </div>
        </div>

        @if($page->moreHosts)@include('site.hosts.profile.more-hosts')@endif
    </div>
@endsection
