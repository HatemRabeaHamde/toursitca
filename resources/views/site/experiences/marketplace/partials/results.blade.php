{{-- Card grid or empty state. Re-rendered on every filter change (no data-reveal inside). --}}
@if($page->cards)
    <ul class="grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-3" data-list>
        @foreach($page->cards as $card)
            <li data-reveal-item data-card>
                <x-site.organisms.experience-card-compact :href="$card['href']" :title="$card['title']" :image="$card['image']" :meta="$card['meta']"
                    :host="$card['host']" :rating="$card['rating']" :price="$card['price']" :wishlist-url="$card['wishlistUrl']" :saved="$card['saved']" />
            </li>
        @endforeach
    </ul>
@else
    <div class="lv-xempty" data-card>
        <p class="lv-t-h4 lv-tone-ink">{{ __('experiences.empty.title') }}</p>
        <p class="lv-t-body-m lv-tone-muted">{{ __('experiences.empty.body') }}</p>
        <a href="{{ $page->indexUrl }}#results" class="lv-arrow-link lv-t-label-m lv-tone-terracotta" data-filter-link>
            <span>{{ __('experiences.empty.reset') }}</span>
            <x-site.atoms.icon name="arrow-right-ink" :size="16" />
        </a>
    </div>
@endif
