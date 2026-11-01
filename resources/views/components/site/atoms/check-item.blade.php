{{-- "What's included" line with a filled check. --}}
<li {{ $attributes->class(['flex items-start gap-2.5 lv-t-body-s lv-tone-ink']) }}>
    <x-site.atoms.icon name="check-dot" :size="18" class="mt-px" />
    <span>{{ $slot }}</span>
</li>
