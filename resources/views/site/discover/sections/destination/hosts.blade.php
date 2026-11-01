<x-site.molecules.guide-chapter id="hosts" :eyebrow="__('discover.destination.eyebrows.hosts')" :title="$page->copy('hosts.title', __('discover.destination.chapters.hosts'))">
    <div class="grid gap-6 xl:grid-cols-2" data-reveal="stagger">
        @foreach($page->hosts as $host)
            <x-site.organisms.host-card variant="row" :href="$host['href']" :name="$host['name']" :role="$host['role']" :image="$host['image']" :bio="$host['bio']" :verified="$host['verified']" />
        @endforeach
    </div>
</x-site.molecules.guide-chapter>
