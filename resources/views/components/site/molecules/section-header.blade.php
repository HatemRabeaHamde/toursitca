{{--
    Chapter header: eyebrow · serif title with italic accent · lede · optional "See all →".
    size: section (56px, Discover chapters) | h1 (48px, interest chapters) | h2 (36px, guide columns)
    accentTone: ink (new design system) | terracotta (landing chapters)
--}}
@props([
    'eyebrow',
    'before' => null,
    'accent' => null,
    'after' => null,
    'lede' => null,
    'linkHref' => null,
    'linkLabel' => null,
    'size' => 'section',
    'accentTone' => 'ink',
    'id' => null,
    'tone' => 'terracotta',
])

<div {{ $attributes->class(['flex flex-col gap-6 md:flex-row md:items-end md:justify-between']) }} data-reveal-group>
    <div @class(['flex max-w-[760px] flex-col', 'gap-3.5' => $size !== 'h2', 'gap-6' => $size === 'h2'])>
        <x-site.atoms.eyebrow :tone="$tone" data-reveal="fade">{{ $eyebrow }}</x-site.atoms.eyebrow>
        <h2 @if($id) id="{{ $id }}" @endif @class(['lv-t-section' => $size === 'section', 'lv-t-h1' => $size === 'h1', 'lv-t-h2' => $size === 'h2', 'lv-tone-ink']) data-reveal="heading" data-reveal-at="<0.1">
            @if($before){{ $before }}@endif
            @if($accent)<em @class(['lv-italic', 'lv-tone-terracotta-deep' => $accentTone === 'terracotta'])>{{ $accent }}</em>@endif
            @if($after){{ $after }}@endif
        </h2>
        @if($lede)
            <p class="lv-t-body-l lv-tone-muted max-w-[640px]" data-reveal="text" data-reveal-at="<0.3">{{ $lede }}</p>
        @endif
    </div>

    @if($linkHref)
        <x-site.atoms.link-arrow :href="$linkHref" class="shrink-0 md:pb-1" data-reveal="fade" data-reveal-at="<0.2">{{ $linkLabel }}</x-site.atoms.link-arrow>
    @endif
</div>
