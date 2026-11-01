{{-- Signature experiences — bookable, with named hosts. --}}
<x-site.organisms.section id="experiences" bg="cream" :divider="true" ornament="leaves-center" labelledby="experiences-title">
    <x-site.organisms.experiences-row
        :eyebrow="__('discover.interest.eyebrows.experiences')"
        :title="$page->copy('experiences.title', $page->name)"
        :lede="$page->copy('experiences.lede')"
        :link-href="$page->experiencesUrl"
        :link-label="$page->copy('experiences.link', __('discover.hub.experiences.link'))"
        :experiences="$page->experiences" />
</x-site.organisms.section>
