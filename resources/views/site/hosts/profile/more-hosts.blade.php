<x-site.organisms.section id="more-hosts" bg="sand" :divider="true" labelledby="more-hosts-title" class="lv-zellige-end">
    <x-site.molecules.section-header id="more-hosts-title"
        :eyebrow="__('hosts.profile.more_eyebrow')"
        :before="__('hosts.profile.more')"
        :accent="__('hosts.profile.more_accent')"
        :link-href="$page->hostsUrl"
        :link-label="__('hosts.profile.more_link')" />
    <ul class="mt-10 grid gap-6 md:grid-cols-3">
        @foreach($page->moreHosts as $host)
            <li><x-site.organisms.host-card variant="row" :href="$host['href']" :name="$host['name']" :role="$host['role']" :image="$host['image']" :verified="$host['verified']" class="h-full" /></li>
        @endforeach
    </ul>
</x-site.organisms.section>
