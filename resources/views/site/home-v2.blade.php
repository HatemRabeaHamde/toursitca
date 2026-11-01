@extends('layouts.site-v2')

@push('page-styles')
    <link rel="preload" as="image" href="{{ asset(config('landing_v2.images.hero')) }}" fetchpriority="high">
    @vite(['resources/css/pages/landing.css'])
@endpush

@section('page')
    @include('site.home-v2.sections.hero')
    @include('site.home-v2.sections.soul')
    @include('site.home-v2.sections.discover')
    @include('site.home-v2.sections.featured')
    @include('site.home-v2.sections.people')
    @include('site.home-v2.sections.stories')
    @include('site.home-v2.sections.plan')
    @include('site.home-v2.sections.know-before')
@endsection
