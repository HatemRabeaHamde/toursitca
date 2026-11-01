<x-site.molecules.guide-chapter id="landmarks" :eyebrow="__('discover.destination.eyebrows.landmarks')" :title="$page->copy('landmarks.title', __('discover.destination.chapters.landmarks'))">
    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3" data-reveal="stagger">
        @foreach($page->landmarks as $landmark)
            <x-site.molecules.media-card :title="$landmark['title']" :body="$landmark['body']" :image="$landmark['image']" :alt="$landmark['title']" />
        @endforeach
    </div>
</x-site.molecules.guide-chapter>
