{{-- Chapter four · Live it — the bookable step. --}}
<x-site.organisms.section id="experiences" bg="cream" :divider="true" ornament="leaves-right" labelledby="experiences-title">
    <x-site.organisms.experiences-row
        :eyebrow="__('discover.hub.experiences.eyebrow')"
        :title="__('discover.hub.experiences.title')"
        :lede="__('discover.hub.experiences.lede')"
        :link-href="$page->urls->experiences()"
        :link-label="__('discover.hub.experiences.link')"
        :experiences="$page->experiences" />
</x-site.organisms.section>
