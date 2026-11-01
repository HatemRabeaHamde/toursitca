{{-- One highlighted traveller quote under the score (Experience detail · Reviews). --}}
@props(['review'])

<figure {{ $attributes->class(['lv-freview']) }}>
    <blockquote class="lv-t-quote-m lv-serif lv-tone-ink">“{{ $review['body'] }}”</blockquote>
    <figcaption class="flex items-center gap-2.5">
        @if($review['avatar'])
            <img src="{{ $review['avatar'] }}" alt="" width="32" height="32" loading="lazy" class="lv-avatar lv-avatar--32">
        @else
            <span class="lv-rcard__initials lv-avatar--32 lv-t-label-xs" aria-hidden="true">{{ $review['initials'] }}</span>
        @endif
        <span class="lv-t-label-s lv-tone-ink">{{ $review['author'] }}</span>
    </figcaption>
</figure>
