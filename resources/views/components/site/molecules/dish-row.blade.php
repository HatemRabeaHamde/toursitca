{{-- Horizontal dish card (Destination · Food): small image, name, one line. --}}
@props(['title', 'body' => null, 'image' => null])

<article {{ $attributes->class(['lv-dish-row']) }}>
    @if($image)<img src="{{ $image }}" alt="" loading="lazy" width="120" height="90" class="lv-dish-row__image">@endif
    <div class="flex flex-col gap-1.5">
        <h3 class="lv-t-h5 lv-tone-ink">{{ $title }}</h3>
        @if($body)<p class="lv-t-body-s lv-tone-muted max-w-[560px]">{{ $body }}</p>@endif
    </div>
</article>
