{{-- Section eyebrow. tone: terracotta (default) | gold (on dark) | muted --}}
@props(['tone' => 'terracotta', 'size' => 'm'])

<p {{ $attributes->class([
    $size === 's' ? 'lv-t-eyebrow-s' : 'lv-t-eyebrow',
    'lv-tone-'.$tone,
]) }}>{{ $slot }}</p>
