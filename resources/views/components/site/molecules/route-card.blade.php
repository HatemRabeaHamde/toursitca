{{-- Large image card that opens one way into Discover ("By destination" / "By interest"). --}}
@props(['href', 'title', 'body', 'link', 'image'])

<a href="{{ $href }}" {{ $attributes->class(['lv-route-card group']) }}>
    <img src="{{ $image }}" alt="" loading="lazy" width="588" height="380" class="lv-route-card__image">
    <span class="lv-route-card__veil" aria-hidden="true"></span>
    <span class="lv-route-card__body">
        <span class="lv-t-h2 lv-tone-cream">{{ $title }}</span>
        <span class="lv-t-body-s lv-route-card__text">{{ $body }}</span>
        <span class="lv-arrow-link lv-t-label-m lv-tone-gold">
            <span>{{ $link }}</span>
            <x-site.atoms.icon name="arrow-right-light" :size="16" />
        </span>
    </span>
</a>
