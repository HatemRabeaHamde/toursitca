{{-- Two ways in: by destination or by interest. --}}
<x-site.organisms.section bg="cream" ornament="leaves-right" labelledby="routes-title">
    <x-site.molecules.section-header id="routes-title"
        :eyebrow="__('discover.hub.routes.eyebrow')"
        :before="__('discover.hub.routes.title')"
        :accent="__('discover.hub.routes.accent')"
        :lede="__('discover.hub.routes.lede')" />

    <div class="mt-12 grid gap-6 md:grid-cols-2" data-reveal="stagger">
        @foreach(['destination' => '#destinations', 'interest' => '#interests'] as $route => $anchor)
            <x-site.molecules.route-card
                :href="$anchor"
                :image="asset(config('discover.images.routes.'.$route))"
                :title="__('discover.hub.routes.'.$route.'.title')"
                :body="__('discover.hub.routes.'.$route.'.body')"
                :link="__('discover.hub.routes.'.$route.'.link')" />
        @endforeach
    </div>
</x-site.organisms.section>
