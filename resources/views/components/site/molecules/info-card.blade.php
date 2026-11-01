{{-- Title + short text on a white card (practical tips, origins, table manners). --}}
@props(['title', 'body', 'size' => 's'])

<article {{ $attributes->class(['lv-info-card', 'lv-info-card--l' => $size === 'l']) }}>
    <h3 @class(['lv-tone-ink', 'lv-t-h6' => $size === 's', 'lv-t-h4' => $size === 'l'])>{{ $title }}</h3>
    <p @class(['lv-tone-muted', 'lv-t-body-s' => $size === 's', 'lv-t-body-m' => $size === 'l'])>{{ $body }}</p>
</article>
