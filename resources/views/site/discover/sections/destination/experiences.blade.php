<x-site.molecules.guide-chapter id="experiences" :eyebrow="__('discover.destination.eyebrows.experiences')" :title="$page->copy('experiences.title', __('discover.destination.chapters.experiences'))">
    <x-site.organisms.experience-grid :experiences="$page->experiences" />
    <x-site.atoms.link-arrow :href="$page->experiencesUrl" class="mt-8" data-reveal="fade">{{ $page->copy('experiences.link', __('discover.destination.start.link')) }}</x-site.atoms.link-arrow>
</x-site.molecules.guide-chapter>
