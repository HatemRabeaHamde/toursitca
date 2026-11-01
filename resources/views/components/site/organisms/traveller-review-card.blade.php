{{-- Figma "Review" card: traveller, trip date, verified booking, stars, quote, photos, trip, host reply. --}}
@props(['review'])

<article {{ $attributes->class(['lv-trcard']) }}>
    <header class="flex items-center justify-between gap-3">
        <div class="flex min-w-0 items-center gap-3">
            @if($review['avatar'])
                <img src="{{ $review['avatar'] }}" alt="" width="40" height="40" loading="lazy" class="lv-avatar">
            @else
                <span class="lv-rcard__initials lv-t-label-s" aria-hidden="true">{{ $review['initials'] }}</span>
            @endif
            <span class="flex min-w-0 flex-col gap-0.5">
                <span class="lv-t-label-m lv-tone-ink truncate">{{ $review['author'] }}</span>
                @if($review['travelled'])<span class="lv-t-caption lv-tone-muted">{{ $review['travelled'] }}</span>@endif
            </span>
        </div>
        <span class="lv-verified-pill lv-t-label-xs">
            <x-site.atoms.icon name="shield-check-success" :size="13" />
            <span>{{ __('reviews.card.verified') }}</span>
        </span>
    </header>

    <x-site.atoms.stars :value="$review['rating']" />

    <blockquote class="lv-t-quote-s lv-tone-ink">“{{ $review['body'] }}”</blockquote>

    @if($review['photos'])
        <ul class="flex flex-wrap gap-2">
            @foreach($review['photos'] as $photo)
                <li><img src="{{ $photo }}" alt="{{ __('reviews.card.photo', ['name' => $review['author']]) }}" width="96" height="72" loading="lazy" class="lv-trcard__photo"></li>
            @endforeach
        </ul>
    @endif

    @if($review['trip'])
        @if($review['tripUrl'])
            <a href="{{ $review['tripUrl'] }}" class="lv-trip-chip lv-t-label-xs">
                <x-site.atoms.icon name="pin-terracotta" :size="14" /><span>{{ $review['trip'] }}</span>
            </a>
        @else
            <span class="lv-trip-chip lv-t-label-xs"><x-site.atoms.icon name="pin-terracotta" :size="14" /><span>{{ $review['trip'] }}</span></span>
        @endif
    @endif

    @if($review['reply'])
        <div class="lv-trcard__reply">
            <p class="lv-t-label-xs lv-tone-terracotta">{{ $review['reply']['from'] ? __('reviews.card.reply_from', ['name' => $review['reply']['from']]) : __('reviews.card.reply') }}</p>
            <p class="lv-t-body-s lv-tone-ink">{{ $review['reply']['body'] }}</p>
        </div>
    @endif
</article>
