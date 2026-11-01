{{-- Chapter five · People — named, licensed local hosts. --}}
<x-site.organisms.section id="hosts" bg="sand" :divider="true" labelledby="hosts-title" class="lv-zellige-end">
    <x-site.molecules.section-header id="hosts-title"
        :eyebrow="__('discover.hub.hosts.eyebrow')"
        :before="__('discover.hub.hosts.title')"
        :accent="__('discover.hub.hosts.accent')"
        :link-href="$page->urls->hosts()"
        :link-label="__('discover.hub.hosts.link')" />

    <ul class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal="stagger">
        @foreach($page->hosts as $host)
            <li><x-site.organisms.host-card :href="$host['href']" :name="$host['name']" :role="$host['role']" :image="$host['image']" :verified="$host['verified']" /></li>
        @endforeach
    </ul>
</x-site.organisms.section>
