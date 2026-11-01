{{-- "Verified host · Licensed" trust line. --}}
<span {{ $attributes->class(['inline-flex items-center gap-1.5']) }}>
    <x-site.atoms.icon name="shield-success" :size="15" />
    <span class="lv-t-label-xs lv-tone-success">{{ $slot->isEmpty() ? __('discover.trust.verified_host') : $slot }}</span>
</span>
