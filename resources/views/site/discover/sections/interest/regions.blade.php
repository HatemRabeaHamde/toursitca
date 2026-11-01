{{-- Where to experience it — each region links to its destination guide. --}}
<x-site.organisms.section id="regions" bg="sand" :divider="true" labelledby="regions-title" class="lv-zellige-end">
    <x-site.molecules.section-header id="regions-title" size="h1"
        :eyebrow="__('discover.interest.eyebrows.regions')"
        :before="$page->copy('regions.title')"
        :accent="$page->copy('regions.accent')"
        :link-href="$page->destinationsUrl"
        :link-label="__('discover.interest.see_all_destinations')" />

    <ul class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal="stagger">
        @foreach($page->regions as $region)
            <li><x-site.molecules.guide-card variant="framed" class="h-full" :href="$region['href']" :title="$region['title']" :body="$region['body']" :image="$region['image']" :cta="$region['cta']" /></li>
        @endforeach
    </ul>
</x-site.organisms.section>
