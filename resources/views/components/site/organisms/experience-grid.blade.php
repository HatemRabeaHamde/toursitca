{{-- Grid of experience cards from GuideExperienceCards::present() arrays. columns: 3 (default) --}}
@props(['experiences'])

<div {{ $attributes->class(['grid gap-6 sm:grid-cols-2 xl:grid-cols-3']) }} data-reveal="stagger">
    @foreach($experiences as $experience)
        <x-site.organisms.experience-card
            :href="$experience['href']"
            :title="$experience['title']"
            :image="$experience['image']"
            :location="$experience['location']"
            :summary="$experience['summary']"
            :host="$experience['host']"
            :rating="$experience['rating']"
            :reviews="$experience['reviews']"
            :licence="$experience['licence']"
            :duration="$experience['duration']"
            :group="$experience['group']"
            :price="$experience['price']"
            :featured="$experience['featured']"
            :wishlist-url="$experience['wishlistUrl']" />
    @endforeach
</div>
