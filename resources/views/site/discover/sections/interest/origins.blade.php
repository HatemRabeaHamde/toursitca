{{-- Origins & traditions — where it comes from. --}}
<x-site.organisms.section id="origins" bg="sand" :divider="true" labelledby="origins-title" class="lv-zellige-start">
    <x-site.molecules.section-header id="origins-title" size="h1"
        :eyebrow="__('discover.interest.eyebrows.origins')"
        :before="$page->copy('origins.title')"
        :accent="$page->copy('origins.accent')"
        :lede="$page->copy('origins.lede')" />

    <div class="mt-10 grid gap-6 md:grid-cols-3" data-reveal="stagger">
        @foreach($page->origins as $origin)
            <x-site.molecules.info-card size="l" :title="$origin['title']" :body="$origin['body']" />
        @endforeach
    </div>
</x-site.organisms.section>
