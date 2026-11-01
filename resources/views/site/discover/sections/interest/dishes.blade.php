{{-- Signature dishes — three to taste first + "Also look for". --}}
<x-site.organisms.section id="dishes" bg="cream" :divider="true" ornament="leaves-center" labelledby="dishes-title">
    <x-site.molecules.section-header id="dishes-title" size="h1"
        :eyebrow="__('discover.interest.eyebrows.dishes')"
        :before="$page->copy('dishes.title')"
        :accent="$page->copy('dishes.accent')" />

    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal="stagger">
        @foreach(array_slice($page->dishes, 0, 3) as $dish)
            <x-site.molecules.media-card variant="dish" :title="$dish['title']" :body="$dish['body']" :image="$dish['image']" :alt="$dish['title']" />
        @endforeach
        @if($page->also)
            <aside class="lv-also-box">
                <h3 class="lv-t-h5 lv-tone-ink">{{ __('discover.interest.also_title') }}</h3>
                <p class="lv-t-body-m lv-tone-ink">{{ $page->also['title'] }}</p>
                @if($page->also['body'])<p class="lv-t-body-s lv-tone-muted">{{ $page->also['body'] }}</p>@endif
            </aside>
        @endif
    </div>
</x-site.organisms.section>
