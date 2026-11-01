{{-- Community stories (left) + local hosts (right). --}}
<x-site.organisms.section id="community" bg="sand" :divider="true" class="lv-zellige-start">
    <div class="grid gap-14 lg:grid-cols-[minmax(0,384px)_minmax(0,1fr)] lg:gap-12">
        @if($page->stories)
            <div class="flex flex-col gap-6" data-reveal-group>
                <x-site.atoms.eyebrow data-reveal="fade">{{ __('discover.interest.eyebrows.stories') }}</x-site.atoms.eyebrow>
                <h2 class="lv-t-h2 lv-tone-ink" data-reveal="heading" data-reveal-at="<0.1">{{ $page->copy('stories.title') }}</h2>
                <x-site.organisms.story-card :href="$page->stories[0]['href']" :title="$page->stories[0]['title']" :image="$page->stories[0]['image']" :location="$page->stories[0]['location']" :days="$page->stories[0]['days']" :rating="$page->stories[0]['rating']" data-reveal="fade" />
                <x-site.atoms.link-arrow :href="$page->storiesUrl" class="self-start pt-2" data-reveal="fade">{{ __('discover.interest.see_all_stories') }}</x-site.atoms.link-arrow>
            </div>
        @endif

        <div class="flex flex-col gap-6" data-reveal-group>
            <x-site.atoms.eyebrow data-reveal="fade">{{ __('discover.interest.eyebrows.hosts') }}</x-site.atoms.eyebrow>
            <h2 class="lv-t-h2 lv-tone-ink" data-reveal="heading" data-reveal-at="<0.1">{{ $page->copy('hosts.title') }}</h2>
            @foreach($page->hosts as $host)
                <x-site.organisms.host-card variant="row" :href="$host['href']" :name="$host['name']" :role="$host['role']" :image="$host['image']" :bio="$host['bio']" :verified="$host['verified']" data-reveal="fade" />
            @endforeach
            @if($page->copy('hosts.empty'))
                <p class="lv-host-empty lv-t-body-s lv-tone-muted" data-reveal="fade">
                    <x-site.atoms.icon name="globe" :size="20" class="lv-icon--ink" />
                    <span>{{ $page->copy('hosts.empty') }}</span>
                </p>
            @endif
        </div>
    </div>
</x-site.organisms.section>
