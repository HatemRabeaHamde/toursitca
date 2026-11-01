{{--
    "Live it" row: a narrow intro column + up to three experience cards
    (Discover · Chapter four, Interest · Signature experiences).
--}}
@props(['eyebrow', 'title', 'lede' => null, 'linkHref' => null, 'linkLabel' => null, 'experiences' => []])

<div {{ $attributes->class(['grid gap-10 lg:grid-cols-[240px_1fr] lg:gap-8']) }}>
    <div class="flex flex-col gap-4 lg:pt-1" data-reveal-group>
        <x-site.atoms.eyebrow data-reveal="fade">{{ $eyebrow }}</x-site.atoms.eyebrow>
        <h2 class="lv-t-h2 lv-tone-ink" data-reveal="heading" data-reveal-at="<0.1">{{ $title }}</h2>
        @if($lede)<p class="lv-t-body-m lv-tone-muted" data-reveal="text" data-reveal-at="<0.3">{{ $lede }}</p>@endif
        @if($linkHref)
            <x-site.atoms.link-arrow :href="$linkHref" class="self-start pt-2" data-reveal="fade" data-reveal-at="<0.2">{{ $linkLabel }}</x-site.atoms.link-arrow>
        @endif
    </div>

    <x-site.organisms.experience-grid :experiences="$experiences" />
</div>
