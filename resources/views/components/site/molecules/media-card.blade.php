{{--
    Image + title + description, no frame. variant:
      landmark → 220px image, radius 16, H5 title   (Destination · History & landmarks)
      dish     → 240px image, radius 20, H4 title   (Interest · Signature dishes)
--}}
@props(['title', 'body' => null, 'image', 'variant' => 'landmark', 'alt' => ''])

<article {{ $attributes->class(['lv-media-card', 'lv-media-card--'.$variant]) }}>
    <div class="lv-media-card__image">
        <img src="{{ $image }}" alt="{{ $alt }}" loading="lazy" width="296" height="{{ $variant === 'dish' ? 240 : 220 }}">
    </div>
    <h3 @class(['lv-tone-ink', 'lv-t-h5' => $variant === 'landmark', 'lv-t-h4' => $variant === 'dish'])>{{ $title }}</h3>
    @if($body)<p class="lv-t-body-s lv-tone-muted">{{ $body }}</p>@endif
</article>
