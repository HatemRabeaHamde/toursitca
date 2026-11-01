{{-- Figma "Card / Experience" — the bookable unit shown on every discovery page. --}}
@props([
    'href',
    'title',
    'image',
    'location',
    'summary' => null,
    'host' => null,
    'rating' => null,
    'reviews' => null,
    'licence' => null,
    'duration' => null,
    'group' => null,
    'price' => null,
    'featured' => false,
    'wishlistUrl' => null,
    'saved' => false,
])

<article {{ $attributes->class(['lv-xcard group']) }}>
    <div class="lv-xcard__image">
        <img src="{{ $image }}" alt="" loading="lazy" width="296" height="200">
        @if($featured)<span class="lv-xcard__badge lv-t-label-xs">{{ __('discover.experience.featured') }}</span>@endif
        @if($wishlistUrl)
            <x-site.atoms.wishlist-button :url="$wishlistUrl" :saved="$saved" />
        @endif
    </div>

    <div class="lv-xcard__body">
        <x-site.atoms.meta-item icon="pin">{{ $location }}</x-site.atoms.meta-item>
        <h3 class="lv-t-h5 lv-tone-ink lv-clamp-2"><a href="{{ $href }}" class="lv-xcard__link">{{ $title }}</a></h3>
        @if($summary)<p class="lv-t-body-s lv-tone-muted lv-clamp-2">{{ $summary }}</p>@endif

        <hr class="lv-xcard__rule">

        <div class="flex items-center justify-between gap-3">
            @if($host)<x-site.molecules.host-meta :name="$host['name']" :role="$host['role'] ?? null" :avatar="$host['avatar'] ?? null" />@endif
            <div class="flex shrink-0 flex-col items-end gap-0.5">
                @if($rating)<x-site.atoms.rating :value="$rating" :count="$reviews" />@endif
                @if($licence)<x-site.atoms.licence-badge :number="$licence" />@endif
            </div>
        </div>

        <div class="flex items-end justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2.5">
                @if($duration)<x-site.atoms.meta-item icon="clock">{{ $duration }}</x-site.atoms.meta-item>@endif
                @if($group)<x-site.atoms.meta-item icon="users">{{ $group }}</x-site.atoms.meta-item>@endif
            </div>
            @if($price)
                <div class="flex shrink-0 flex-col items-end">
                    <span class="flex items-baseline gap-[5px]">
                        <span class="lv-t-caption lv-tone-muted">{{ __('discover.experience.from') }}</span>
                        <span class="lv-t-h5 lv-tone-ink">{{ $price }}</span>
                    </span>
                    <span class="lv-t-micro lv-tone-muted">{{ __('discover.experience.per_person') }}</span>
                </div>
            @endif
        </div>

        <x-site.atoms.button :href="$href" variant="outline" class="relative z-[2] w-full">{{ __('discover.experience.view_details') }}</x-site.atoms.button>
    </div>
</article>
