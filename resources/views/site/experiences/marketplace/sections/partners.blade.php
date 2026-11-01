{{-- Local partners: the hosts behind the experiences (best rated first). --}}
<x-site.organisms.section id="partners" bg="sand" :divider="true" labelledby="partners-title" class="lv-zellige-end">
    <x-site.molecules.section-header id="partners-title"
        :eyebrow="__('experiences.partners.eyebrow')"
        :before="__('experiences.partners.title')"
        :accent="__('experiences.partners.accent')"
        :lede="__('experiences.partners.lede')"
        :link-href="$page->hostsUrl"
        :link-label="__('experiences.partners.link')" />

    <ul class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3" data-reveal="stagger">
        @foreach($page->partners as $partner)
            <li>
                <x-site.molecules.partner-card :href="$partner['href']" :name="$partner['name']" :role="$partner['role']" :avatar="$partner['avatar']"
                    :rating="$partner['rating']" :reviews="$partner['reviews']" :licence="$partner['licence']" :experiences="$partner['experiences']" />
            </li>
        @endforeach
    </ul>
</x-site.organisms.section>
