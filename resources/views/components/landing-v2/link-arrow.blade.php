@props(['href', 'icon' => config('landing_v2.icons.arrow_clay')])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'lv-link-arrow']) }}>
    <span>{{ $slot }}</span>
    <img src="{{ asset($icon) }}" alt="" width="18" height="18" aria-hidden="true">
</a>
