{{-- Icon + caption (📍 Marrakech · 🕒 1 day · 👥 Small group). --}}
@props(['icon'])

<span {{ $attributes->class(['inline-flex items-center gap-[5px] lv-t-caption lv-tone-muted whitespace-nowrap']) }}>
    <x-site.atoms.icon :name="$icon" :size="14" />
    <span>{{ $slot }}</span>
</span>
