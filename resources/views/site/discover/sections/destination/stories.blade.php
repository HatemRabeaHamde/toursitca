<x-site.molecules.guide-chapter id="stories" :eyebrow="__('discover.destination.eyebrows.stories')" :title="$page->copy('stories.title', __('discover.destination.chapters.stories'))">
    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3" data-reveal="stagger">
        @foreach($page->stories as $story)
            <x-site.organisms.story-card :href="$story['href']" :title="$story['title']" :image="$story['image']" :location="$story['location']" :days="$story['days']" :rating="$story['rating']" />
        @endforeach
    </div>
</x-site.molecules.guide-chapter>
