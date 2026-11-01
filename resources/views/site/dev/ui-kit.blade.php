{{-- Dev-only living style guide for the Tourstica v2 UI kit (local/testing). Sample data mirrors the Figma frames. --}}
@extends('layouts.site-v2')

@php
    $img = fn (string $path) => asset('images/discover/'.$path);
    $home = route('site.home', ['locale' => app()->getLocale()]);
@endphp

@section('page')
    <x-site.organisms.page-hero
        size="tall"
        :image="$img('heroes/discover.jpg')"
        eyebrow="Discover Morocco"
        title="Discover Morocco beyond the"
        accent="postcard."
        lede="Places, traditions, food and landscapes — explained by the people who live there, and connected to experiences you can actually book."
        :breadcrumb="[['label' => 'Home', 'href' => $home], ['label' => 'Discover Morocco']]"
        :primary="['label' => 'Explore destinations', 'href' => '#cards']"
        :secondary="['label' => 'Explore by interest', 'href' => '#guides']" />

    <x-site.organisms.section id="cards" bg="cream" divider ornament="leaves-right">
        <x-site.molecules.section-header eyebrow="UI kit · Cards" before="Live Marrakech with a" accent="local host." lede="Experience, story and host cards straight from the Figma components." link-href="#" link-label="See all experiences" />
        <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3" data-reveal="stagger">
            <x-site.organisms.experience-card href="#" :image="$img('experiences/food-journey.jpg')" location="Marrakech" title="A local food journey in Marrakech"
                summary="Hidden food stalls, a family kitchen and the real flavours of the medina."
                :host="['name' => 'Fatima', 'role' => 'Home cook', 'avatar' => $img('hosts/fatima-avatar.png')]"
                rating="4.8" reviews="94" licence="MA-1120" duration="1 day" group="Small group" price="€95" wishlist-url="#" />
            <x-site.organisms.experience-card href="#" :image="$img('landmarks/jemaa-el-fnaa.jpg')" location="Marrakech" title="A morning in the souk" featured
                summary="Shop with a local, meet the artisans and find the stalls tourists miss."
                :host="['name' => 'Hassan', 'role' => 'Desert guide', 'avatar' => $img('hosts/hassan.jpg')]"
                rating="4.9" reviews="128" licence="MA-3847" duration="Half day" group="Small group" price="€70" />
            <x-site.organisms.story-card href="#" :image="$img('stories/courtyard-kitchen-fes.jpg')" title="A courtyard kitchen in Fès" location="Fès" :days="2" rating="4.9" />
        </div>
    </x-site.organisms.section>

    <x-site.organisms.section bg="sand" divider>
        <x-site.molecules.section-header eyebrow="UI kit · People" before="The people who make Morocco" accent="unforgettable." />
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal="stagger">
            @foreach(['hassan' => 'Desert guide · M’Hamid', 'fatima' => 'Home cook · Marrakech', 'youssef' => 'Cultural guide · Fès', 'omar' => 'Historian · Aït Benhaddou'] as $name => $role)
                <x-site.organisms.host-card href="#" :name="ucfirst($name)" :role="$role" :image="$img('hosts/'.$name.'.jpg')" />
            @endforeach
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-2" data-reveal="stagger">
            <x-site.organisms.host-card variant="row" href="#" name="Fatima" role="Home cook · Marrakech" bio="Leads the food journey through her neighbourhood." :image="$img('hosts/fatima.jpg')" />
            <x-site.organisms.host-card variant="row" href="#" name="Hassan" role="Desert guide · M’Hamid" bio="Picks you up in Marrakech for the Erg Chigaga trip." :image="$img('hosts/hassan.jpg')" />
        </div>
    </x-site.organisms.section>

    <x-site.organisms.section id="guides" bg="cream" divider ornament="leaves-center">
        <x-site.molecules.section-header eyebrow="UI kit · Guides" before="What kind of Morocco are you looking" accent="for?" lede="Each interest opens a guide written with local hosts." link-href="#" link-label="See all interests" />
        <div class="mt-12 grid gap-x-6 gap-y-10 md:grid-cols-3" data-reveal="stagger">
            <x-site.molecules.guide-card href="#" title="Cuisine" body="Tagines, bread ovens and family recipes." :image="$img('interests/cuisine.jpg')" />
            <x-site.molecules.guide-card href="#" title="Culture & Traditions" body="Music, festivals and everyday rituals." :image="$img('interests/culture.jpg')" />
            <x-site.molecules.guide-card href="#" title="History & Heritage" body="Kasbahs, medinas and imperial cities." :image="$img('interests/history.jpg')" />
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal="stagger">
            @foreach(['marrakech' => 'Tanjia and evening food stalls.', 'fes' => 'Pastilla and old-medina kitchens.', 'essaouira' => 'Grilled fish fresh from the port.', 'high-atlas' => 'Home cooking in Amazigh villages.'] as $slug => $note)
                <x-site.molecules.guide-card variant="framed" href="#" :title="ucwords(str_replace('-', ' ', $slug))" :body="$note" :image="$img('destinations/'.$slug.'.jpg')" />
            @endforeach
        </div>
        <div class="mt-12 grid gap-6 md:grid-cols-3" data-reveal="stagger">
            <x-site.molecules.media-card :image="$img('landmarks/jemaa-el-fnaa.jpg')" title="Jemaa el-Fnaa" body="The main square: storytellers and musicians by day, open-air food stalls after dusk." />
            <x-site.molecules.media-card variant="dish" :image="$img('dishes/tagine.jpg')" title="Tagine" body="Meat or vegetables cooked for hours under a cone-shaped lid." />
            <x-site.molecules.info-card title="Getting around" body="The medina is walkable only. Taxis wait at the gates; agree the fare or ask for the meter." />
        </div>
    </x-site.organisms.section>

    <x-site.organisms.cta-band eyebrow="Ready for Marrakech?" title="See every experience in" accent="Marrakech."
        lede="Food walks, souk mornings and trips to the mountains and desert — all with local hosts."
        cta-label="Explore experiences" cta-href="#" />
@endsection
