{{-- Chapter two · Interests — six guide cards. --}}
<x-site.organisms.section id="interests" bg="cream" :divider="true" ornament="leaves-center" labelledby="interests-title">
    <x-site.molecules.section-header id="interests-title"
        :eyebrow="__('discover.hub.interests.eyebrow')"
        :before="__('discover.hub.interests.title')"
        :accent="__('discover.hub.interests.accent')"
        :lede="__('discover.hub.interests.lede')"
        :link-href="$page->urls->experiences()"
        :link-label="__('discover.hub.interests.link')" />

    <ul class="mt-12 grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3" data-reveal="stagger">
        @foreach($page->interests as $interest)
            <li><x-site.molecules.guide-card :href="$interest['href']" :title="$interest['title']" :body="$interest['body']" :image="$interest['image']" :cta="$interest['cta']" /></li>
        @endforeach
    </ul>
</x-site.organisms.section>
