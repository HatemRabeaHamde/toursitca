@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
])

@php
    $classes = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'ghost' => 'btn-ghost',
        'danger' => 'btn-danger',
    ][$variant] ?? 'btn-primary';

    $sizeClasses = [
        'sm' => 'px-3 py-2 text-xs',
        'md' => '',
        'lg' => 'px-5 py-3 text-base',
    ][$size] ?? '';
@endphp

@if ($href)
    <a {{ $attributes->merge(['href' => $href, 'class' => trim($classes.' '.$sizeClasses)]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => $type, 'class' => trim($classes.' '.$sizeClasses)])->class(['opacity-50' => $disabled]) }} @disabled($disabled)>
        {{ $slot }}
    </button>
@endif
