@php($reviews = $page->reviews)

<section id="reviews" class="lv-xsection" aria-labelledby="reviews-title" data-reveal-group>
    <h2 id="reviews-title" class="lv-xsection__title lv-t-h2 lv-serif lv-tone-ink" data-reveal="heading">{{ __('experiences.detail.reviews.title') }}</h2>

    <div class="flex items-center gap-6" data-reveal="fade" data-reveal-at="<0.2">
        <p class="lv-xscore">{{ $reviews['average'] }}</p>
        <div class="flex flex-col gap-1">
            <x-site.atoms.stars :value="(int) round((float) $reviews['average'])" :size="16"
                :label="__('experiences.detail.reviews.stars', ['rating' => $reviews['average']])" />
            <p class="lv-t-caption lv-tone-muted">{{ trans_choice('experiences.detail.reviews.summary', $reviews['count'], ['count' => $reviews['count']]) }}</p>
        </div>
    </div>

    @if($reviews['featured'])
        <x-site.molecules.featured-review :review="$reviews['featured']" data-reveal="fade" data-reveal-at="<0.1" />
    @endif

    <x-site.atoms.link-arrow :href="$reviews['href']" arrow="terracotta" data-reveal="fade" data-reveal-at="<0.1">
        {{ trans_choice('experiences.detail.reviews.all', $reviews['count'], ['count' => $reviews['count']]) }}
    </x-site.atoms.link-arrow>
</section>
