{{-- Sand card with a host quote and avatar (Interest · What it means). --}}
@props(['quote', 'by' => null, 'avatar' => null])

<figure {{ $attributes->class(['lv-quote-card']) }}>
    <blockquote class="lv-t-quote-m lv-tone-ink">“{{ $quote }}”</blockquote>
    @if($by)
        <figcaption class="flex items-center gap-3">
            @if($avatar)<img src="{{ $avatar }}" alt="" width="44" height="44" loading="lazy" class="lv-avatar lv-avatar--44">@endif
            <span class="lv-t-label-s lv-tone-ink">{{ $by }}</span>
        </figcaption>
    @endif
</figure>
