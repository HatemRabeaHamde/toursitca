{{-- Eyebrow + two-tone serif heading used by every chapter. --}}
@props(['eyebrow', 'before' => null, 'accent', 'after' => null, 'size' => '', 'id' => null])

<div {{ $attributes }}>
    <x-landing-v2.eyebrow data-reveal="fade">{{ $eyebrow }}</x-landing-v2.eyebrow>
    <h2 @if($id) id="{{ $id }}" @endif class="lv-serif lv-h2 {{ $size }} mt-5" data-reveal="heading" data-reveal-at="<0.1">
        @if($before){{ $before }} @endif<span class="lv-accent">{{ $accent }}</span>@if($after) {{ $after }}@endif
    </h2>
</div>
