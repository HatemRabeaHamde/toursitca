{{-- Host quote with a terracotta rule (Destination · Overview). --}}
@props(['quote', 'by' => null])

<figure {{ $attributes->class(['lv-pull-quote']) }}>
    <blockquote class="lv-t-quote-l lv-tone-ink">“{{ $quote }}”</blockquote>
    @if($by)<figcaption class="lv-t-caption lv-tone-muted">— {{ $by }}</figcaption>@endif
</figure>
