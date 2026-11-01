{{-- Text link + arrow ("See all →", "Open guide →", "Read the story →").
     arrow: ink (default) | terracotta | light --}}
@props(['href', 'arrow' => 'ink', 'tone' => 'terracotta'])

@php
    $icon = match ($arrow) {
        'terracotta' => 'arrow-right-terracotta',
        'light' => 'arrow-right-light',
        default => 'arrow-right-ink',
    };
@endphp

<a href="{{ $href }}" {{ $attributes->class(['lv-arrow-link lv-t-label-m', 'lv-tone-'.$tone]) }}>
    <span>{{ $slot }}</span>
    <x-site.atoms.icon :name="$icon" :size="16" />
</a>
