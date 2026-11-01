{{-- "You may also like" — same city or interest (existing recommendations). --}}
<section class="lv-container lv-xrelated" aria-labelledby="related-title" data-reveal-group>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h2 id="related-title" class="lv-t-h2 lv-serif lv-tone-ink" data-reveal="heading">{{ __('experiences.detail.related.title') }}</h2>
        <x-site.atoms.link-arrow :href="route('site.experiences.index', ['locale' => app()->getLocale()])" arrow="terracotta" data-reveal="fade">{{ __('experiences.detail.related.link') }}</x-site.atoms.link-arrow>
    </div>
    <ul class="mt-8 grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3" data-reveal="stagger">
        @foreach($page->related as $card)
            <li><x-site.organisms.experience-card-compact :href="$card['href']" :title="$card['title']" :image="$card['image']"
                :meta="$card['meta']" :host="$card['host']" :rating="$card['rating']" :price="$card['price']" /></li>
        @endforeach
    </ul>
</section>
