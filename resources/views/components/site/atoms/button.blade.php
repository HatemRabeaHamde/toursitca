{{-- Figma "Button / Primary" & "Button / Outline". Renders <a> when href is given. --}}
@props(['href' => null, 'variant' => 'primary', 'arrow' => true, 'type' => 'button'])

@php
    $classes = ['lv-button', 'lv-button--'.$variant, 'lv-t-label-l'];
    $icon = $variant === 'outline' ? 'arrow-right-terracotta-18' : 'arrow-right-white';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        <span>{{ $slot }}</span>
        @if($arrow)<x-site.atoms.icon :name="$icon" :size="18" />@endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        <span>{{ $slot }}</span>
        @if($arrow)<x-site.atoms.icon :name="$icon" :size="18" />@endif
    </button>
@endif
