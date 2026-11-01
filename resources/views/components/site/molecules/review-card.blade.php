{{-- Figma "Card / Review": stars, quote, traveller + what they booked. --}}
@props(['rating', 'body', 'author', 'initials' => '?', 'avatar' => null, 'context' => null])

<figure {{ $attributes->class(['lv-rcard']) }}>
    <p class="flex gap-[3px]" role="img" aria-label="{{ __('experiences.reviews.stars', ['rating' => $rating]) }}">
        @for($i = 0; $i < $rating; $i++)<x-site.atoms.icon name="star" :size="16" />@endfor
    </p>
    <blockquote class="lv-t-quote-s lv-tone-ink">“{{ $body }}”</blockquote>
    <figcaption class="mt-auto flex items-center gap-2.5">
        @if($avatar)
            <img src="{{ $avatar }}" alt="" width="40" height="40" loading="lazy" class="lv-avatar">
        @else
            <span class="lv-rcard__initials lv-t-label-s" aria-hidden="true">{{ $initials }}</span>
        @endif
        <span class="flex min-w-0 flex-col">
            <span class="lv-t-label-m lv-tone-ink">{{ $author }}</span>
            @if($context)<span class="lv-t-micro lv-tone-muted truncate">{{ $context }}</span>@endif
        </span>
    </figcaption>
</figure>
