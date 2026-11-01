{{-- Figma "Card / Experience — Compact" (Experiences marketplace): big image, meta, title, host ★ rating, price. --}}
@props(['href', 'title', 'image', 'meta' => null, 'host' => null, 'rating' => null, 'price' => null, 'wishlistUrl' => null, 'saved' => false])

<article {{ $attributes->class(['lv-ccard group']) }}>
    <div class="lv-ccard__media">
        <img src="{{ $image }}" alt="" loading="lazy" width="384" height="264">
        @if($wishlistUrl)<x-site.atoms.wishlist-button :url="$wishlistUrl" :saved="$saved" :label="__('experiences.card.save')" />@endif
    </div>
    <div class="flex flex-col gap-1.5">
        @if($meta)<p class="lv-t-caption lv-tone-muted">{{ $meta }}</p>@endif
        <h3 class="lv-t-h5 lv-tone-ink"><a href="{{ $href }}" class="lv-ccard__link">{{ $title }}</a></h3>
        <div class="flex items-center justify-between gap-3 pt-1">
            <p class="flex min-w-0 items-center gap-1.5">
                @if($host)<span class="lv-t-body-s lv-tone-ink truncate">{{ __('experiences.card.with', ['name' => $host]) }}</span>@endif
                @if($rating)
                    <x-site.atoms.icon name="star-13" :size="13" />
                    <span class="lv-t-label-s lv-tone-ink">{{ $rating }}</span>
                @endif
            </p>
            @if($price)
                <p class="flex shrink-0 items-baseline gap-1">
                    <span class="lv-t-caption lv-tone-muted">{{ __('experiences.card.from') }}</span>
                    <span class="lv-t-label-l lv-tone-ink">{{ $price }}</span>
                </p>
            @endif
        </div>
    </div>
</article>
