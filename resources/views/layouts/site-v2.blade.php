{{--
    Tourstica v2 public layout — every new page (landing, discover, destination, interest…).
    Pages set:  @section('nav_active', 'discover')   @section('page') … @endsection
    Optional:   @push('page-styles') for a page-only stylesheet / LCP preload.
--}}
@extends('layouts.site')

@section('hide_site_chrome', 'true')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Fraunces:ital,opsz,wght,SOFT,WONK@0,9..144,400,0,1;1,9..144,400,0,1&family=Playfair+Display:ital@0;1&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    {{-- Hide animated elements before first paint; failsafe reveals everything if motion never boots. --}}
    <script>document.documentElement.classList.add('lv-js');try{if(sessionStorage.getItem('lv-intro-seen')){document.documentElement.classList.add('lv-returning');}}catch(e){}setTimeout(function(){var h=document.documentElement;if(!h.classList.contains('lv-motion-ready')){h.classList.add('lv-motion-failed');}},4000);</script>
    @vite(['resources/css/site-v2.css', 'resources/js/site-motion.js'])
    @stack('page-styles')
@endpush

@section('content')
    <div class="lv" data-lv-motion>
        <x-site.organisms.nav :active="trim($__env->yieldContent('nav_active')) ?: null" />
        @yield('page')
        <x-site.organisms.footer />
    </div>
@endsection
