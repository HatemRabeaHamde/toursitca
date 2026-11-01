{{-- ★ 4.8 (94) --}}
@props(['value', 'count' => null, 'size' => 'm'])

<span {{ $attributes->class(['lv-rating-inline']) }}>
    <x-site.atoms.icon name="star" :size="14" />
    <span @class(['lv-t-label-m' => $size === 'm', 'lv-t-label-s' => $size === 's'])>{{ $value }}</span>
    @if($count !== null)<span class="lv-t-caption lv-tone-muted">({{ $count }})</span>@endif
</span>
