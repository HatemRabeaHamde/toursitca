@php($reviews = $page->reviews)

<section id="reviews" class="lv-xsection" aria-labelledby="reviews-title">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 id="reviews-title" class="lv-t-h2 lv-serif lv-tone-ink">{{ __('experiences.detail.v1.reviews.title') }}</h2>
        <x-site.atoms.link-arrow :href="$reviews['href']" arrow="terracotta">{{ __('experiences.detail.v1.reviews.all') }}</x-site.atoms.link-arrow>
    </div>
    <x-site.organisms.review-summary :reviews="$reviews" />
</section>
