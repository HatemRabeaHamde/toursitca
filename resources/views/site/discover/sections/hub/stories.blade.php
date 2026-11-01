{{-- Chapter three · Stories — post-trip stories from travellers. --}}
<x-site.organisms.section id="stories" bg="sand" :divider="true" labelledby="stories-title" class="lv-zellige-start">
    <x-site.molecules.section-header id="stories-title"
        :eyebrow="__('discover.hub.stories.eyebrow')"
        :before="__('discover.hub.stories.title')"
        :accent="__('discover.hub.stories.accent')"
        :link-href="$page->urls->landing('stories')"
        :link-label="__('discover.hub.stories.link')" />

    <ul class="mt-12 grid gap-6 md:grid-cols-3" data-reveal="stagger">
        @foreach($page->stories as $story)
            <li><x-site.organisms.story-card :href="$story['href']" :title="$story['title']" :image="$story['image']" :location="$story['location']" :days="$story['days']" :rating="$story['rating']" /></li>
        @endforeach
    </ul>
</x-site.organisms.section>
