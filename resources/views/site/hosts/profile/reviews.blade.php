<section id="reviews" class="lv-hp-section flex flex-col gap-5" aria-labelledby="reviews-title">
    <x-site.atoms.eyebrow>{{ __('hosts.profile.reviews_eyebrow', ['name' => $page->name]) }}</x-site.atoms.eyebrow>
    <h2 id="reviews-title" class="lv-t-h2 lv-serif lv-tone-ink">{{ __('hosts.profile.reviews') }}</h2>
    <x-site.organisms.review-summary :reviews="$page->reviews" />
    <x-site.atoms.link-arrow :href="$page->reviews['href']" arrow="terracotta" class="self-start">{{ trans_choice('hosts.profile.reviews_all', $page->reviews['count'], ['count' => $page->reviews['count']]) }}</x-site.atoms.link-arrow>
</section>
