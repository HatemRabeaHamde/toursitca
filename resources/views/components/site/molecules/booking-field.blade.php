{{-- Booking card field: label + one control (button, select or read-only value). --}}
@props(['label', 'for' => null])

<div {{ $attributes->class(['lv-bfield']) }}>
    @if($for)
        <label for="{{ $for }}" class="lv-t-label-s lv-tone-ink">{{ $label }}</label>
    @else
        <span class="lv-t-label-s lv-tone-ink">{{ $label }}</span>
    @endif
    {{ $slot }}
</div>
