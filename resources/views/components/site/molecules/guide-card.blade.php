{{--
    Card that opens a guide ("Open guide →"). variant:
      plain  → interest card on Discover (image 260, radius 20, H4)
      framed → destination card on Interest pages (white frame, image 180, H5)
--}}
@props(['href', 'title', 'body' => null, 'image', 'variant' => 'plain', 'cta' => null])

<a href="{{ $href }}" {{ $attributes->class(['lv-guide-card group', 'lv-guide-card--'.$variant]) }}>
    <div class="lv-guide-card__image">
        <img src="{{ $image }}" alt="" loading="lazy" width="384" height="{{ $variant === 'framed' ? 180 : 260 }}">
    </div>
    <div class="lv-guide-card__body">
        <h3 @class(['lv-tone-ink', 'lv-t-h4' => $variant === 'plain', 'lv-t-h5' => $variant === 'framed'])>{{ $title }}</h3>
        @if($body)<p class="lv-t-body-s lv-tone-muted">{{ $body }}</p>@endif
        <span class="lv-arrow-link lv-t-label-m lv-tone-terracotta">
            <span>{{ $cta ?? __('discover.actions.open_guide') }}</span>
            <x-site.atoms.icon name="arrow-right-ink" :size="16" />
        </span>
    </div>
</a>
