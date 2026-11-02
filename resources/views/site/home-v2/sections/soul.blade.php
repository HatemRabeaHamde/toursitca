@php
    $tiles = site_content('home.soul.tiles');
@endphp

<section id="story" class="lv-section lv-bg-cream" aria-labelledby="lv-soul-title">
    <img src="{{ site_content('home.soul.decoration')['url'] }}" alt="" aria-hidden="true" loading="lazy" width="520" height="347" data-parallax="24" class="lv-leaves absolute right-0 top-0 hidden w-[36%] max-w-[520px] md:block">
    <img src="{{ site_content('home.soul.decoration')['url'] }}" alt="" aria-hidden="true" loading="lazy" width="520" height="347" class="lv-leaves lv-leaves--faded absolute bottom-0 left-0 hidden w-[36%] max-w-[520px] md:block">

    <div class="lv-container relative">
        <div class="grid gap-12 lg:grid-cols-12 lg:gap-5">
            <div class="lg:col-span-5" data-reveal-group>
                <x-landing-v2.section-heading id="lv-soul-title" :eyebrow="site_content('home.soul.eyebrow')" :before="site_content('home.soul.title')" :accent="site_content('home.soul.title_accent')" />
                <div class="lv-lede mt-6 max-w-[480px]" data-reveal="text" data-reveal-at="<0.35"><x-site.content key="home.soul.description" /></div>
                <x-landing-v2.link-arrow :href="landing_link('soul.link_url', $page->discoverUrl())" class="mt-6" data-reveal="fade" data-reveal-at="<0.2">{{ site_content('home.soul.link') }}</x-landing-v2.link-arrow>
            </div>

            <div class="grid grid-cols-2 gap-5 lg:col-span-7 lg:ps-10" data-reveal-group data-parallax="-8">
                <figure class="lv-tile row-span-2 aspect-[300/380]" data-reveal="image">
                    <img src="{{ site_content('home.soul.people_image')['url'] }}" alt="{{ site_content('home.soul.people_image')['alt'] }}" loading="lazy" width="300" height="380">
                    <figcaption class="lv-tile-caption absolute bottom-6 start-6">{{ $tiles['people'] }}</figcaption>
                </figure>
                <figure class="lv-tile aspect-[300/180]" data-reveal="image" data-reveal-at="<0.18">
                    <img src="{{ site_content('home.soul.places_image')['url'] }}" alt="{{ site_content('home.soul.places_image')['alt'] }}" loading="lazy" width="300" height="180">
                    <figcaption class="lv-tile-caption absolute bottom-5 start-5">{{ $tiles['places'] }}</figcaption>
                </figure>
                <figure class="lv-tile aspect-[300/180]" data-reveal="image" data-reveal-at="<0.18">
                    <img src="{{ site_content('home.soul.moments_image')['url'] }}" alt="{{ site_content('home.soul.moments_image')['alt'] }}" loading="lazy" width="300" height="180">
                    <figcaption class="lv-tile-caption absolute bottom-5 start-5">{{ $tiles['moments'] }}</figcaption>
                </figure>
            </div>
        </div>

        <ol class="mt-14 grid gap-10 md:grid-cols-3 lg:mt-16" data-reveal="stagger">
            @foreach(site_content('home.soul.pillars') as $index => $pillar)
                <li class="lv-pillar flex gap-[18px] pt-[22px]">
                    <span class="lv-pillar-num pt-[5px]">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="max-w-[290px]">
                        <h3 class="lv-serif lv-pillar-title"><span data-content-key="soul.pillars.{{ $index }}.title">{{ $pillar['title'] }}</span></h3>
                        <p class="lv-body mt-2 text-[15.5px]"><span data-content-key="soul.pillars.{{ $index }}.body">{{ $pillar['body'] }}</span></p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>
