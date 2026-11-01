<x-site.molecules.guide-chapter id="tips" :eyebrow="__('discover.destination.eyebrows.tips')" :title="$page->copy('tips.title', __('discover.destination.chapters.tips'))">
    <div class="grid gap-4 sm:grid-cols-2 lg:gap-5" data-reveal="stagger">
        @foreach($page->tips as $tip)
            <x-site.molecules.info-card :title="$tip['title']" :body="$tip['body']" />
        @endforeach
    </div>
    <x-site.atoms.link-arrow :href="$page->guideUrl" class="mt-8" data-reveal="fade">{{ __('discover.destination.guide_link') }}</x-site.atoms.link-arrow>
</x-site.molecules.guide-chapter>
