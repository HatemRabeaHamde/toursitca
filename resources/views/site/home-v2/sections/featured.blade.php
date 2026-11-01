@php
    $lead = $page->featuredLead();
    $rest = $page->featuredRest();
@endphp

<section id="experiences" class="lv-section lv-bg-forest" aria-labelledby="lv-featured-title">
    <div class="lv-container">
        <div data-reveal-group>
        <x-landing-v2.section-heading id="lv-featured-title" size="lv-h2--xl" :eyebrow="site_content('home.featured.eyebrow')" :before="site_content('home.featured.title')" :accent="site_content('home.featured.title_accent')" />
        <div class="lv-lede mt-5 max-w-[600px]" data-reveal="text" data-reveal-at="<0.35"><x-site.content key="home.featured.description" /></div>
        </div>

        @if($lead)
            <div class="mt-14 grid gap-6 md:grid-cols-2 lg:mt-[108px] lg:grid-cols-[2fr_1fr_1fr]" data-reveal="stagger">
                <x-landing-v2.featured-card :card="$lead" lead :pairings="$page->pairings()" class="md:col-span-2 lg:col-span-1 lg:row-span-2" />
                @foreach($rest as $card)
                    <x-landing-v2.featured-card :card="$card" />
                @endforeach
            </div>
        @endif

        @include('site.home-v2.sections.partials.route-bar')

        @if(! $lead)
            <p class="lv-lede mt-14">{{ site_content('home.featured.empty') }}</p>
        @endif
    </div>
</section>
