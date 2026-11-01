{{--
    Local host card. variant:
      column → portrait 258×300 (Discover · People)
      row    → photo 120×140 + bio (Destination / Interest · Local hosts)
--}}
@props(['href', 'name', 'role', 'image', 'bio' => null, 'verified' => true, 'variant' => 'column'])

<a href="{{ $href }}" {{ $attributes->class(['lv-hcard group', 'lv-hcard--'.$variant]) }}>
    <div class="lv-hcard__image">
        <img src="{{ $image }}" alt="{{ $name }}" loading="lazy" width="{{ $variant === 'row' ? 120 : 258 }}" height="{{ $variant === 'row' ? 140 : 300 }}">
    </div>
    <div class="lv-hcard__body">
        <h3 class="lv-t-h4 lv-tone-ink">{{ $name }}</h3>
        <p class="lv-t-body-s lv-tone-muted">{{ $role }}</p>
        @if($bio && $variant === 'row')<p class="lv-t-body-s lv-tone-ink max-w-[300px]">{{ $bio }}</p>@endif
        @if($verified)<x-site.atoms.verified-badge :class="$variant === 'column' ? 'pt-1' : ''" />@endif
        <span class="lv-arrow-link lv-t-label-m lv-tone-terracotta">
            <span>{{ __('discover.host.view_profile') }}</span>
            <x-site.atoms.icon name="arrow-right-ink" :size="16" />
        </span>
    </div>
</a>
