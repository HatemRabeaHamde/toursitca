{{-- Shield + licence number (MA-1120) under a host's rating. --}}
@props(['number'])

<span {{ $attributes->class(['inline-flex items-center gap-1']) }}>
    <x-site.atoms.icon name="shield-success-12" :size="12" />
    <span class="lv-t-micro lv-tone-muted">{{ $number }}</span>
</span>
