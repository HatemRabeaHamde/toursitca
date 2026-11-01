<section id="experiences" class="lv-hp-section flex flex-col gap-5" aria-labelledby="experiences-title">
    <x-site.atoms.eyebrow>{{ __('hosts.profile.book_eyebrow', ['name' => $page->name]) }}</x-site.atoms.eyebrow>
    <h2 id="experiences-title" class="lv-t-h2 lv-serif lv-tone-ink">{{ __('hosts.profile.experiences', ['name' => $page->name]) }}</h2>
    <ul class="grid gap-6 sm:grid-cols-2">
        @foreach($page->experiences as $card)
            <li>
                <x-site.organisms.experience-card :href="$card['href']" :title="$card['title']" :image="$card['image']" :location="$card['location']"
                    :summary="$card['summary']" :host="$card['host']" :rating="$card['rating']" :reviews="$card['reviews']" :licence="$card['licence']"
                    :duration="$card['duration']" :group="$card['group']" :price="$card['price']" :featured="$card['featured']" :wishlist-url="$card['wishlistUrl']" class="h-full" />
            </li>
        @endforeach
        @if(count($page->experiences) === 1)
            <li class="lv-hp-soon">
                <x-site.atoms.icon name="sparkle-terracotta" :size="24" />
                <p class="lv-t-h5 lv-serif lv-tone-ink">{{ __('hosts.profile.soon', ['name' => $page->name]) }}</p>
                <p class="lv-t-body-s lv-tone-muted">{{ __('hosts.profile.soon_body') }}</p>
            </li>
        @endif
    </ul>
</section>
