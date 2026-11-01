{{-- Dark green closing band ("Ready for Marrakech?"). --}}
@props(['eyebrow', 'title', 'accent' => null, 'lede' => null, 'ctaLabel', 'ctaHref'])

<section {{ $attributes->class(['lv-cta-band']) }} data-reveal-group>
    <div class="lv-container flex flex-col gap-10 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex max-w-[820px] flex-col gap-3">
            <x-site.atoms.eyebrow tone="gold" data-reveal="fade">{{ $eyebrow }}</x-site.atoms.eyebrow>
            <h2 class="lv-t-section lv-tone-cream" data-reveal="heading" data-reveal-at="<0.1">
                {{ $title }}
                @if($accent)<em class="lv-italic">{{ $accent }}</em>@endif
            </h2>
            @if($lede)<p class="lv-t-body-l lv-tone-cream" data-reveal="text" data-reveal-at="<0.3">{{ $lede }}</p>@endif
        </div>
        <x-site.atoms.button :href="$ctaHref" class="self-start lg:self-auto" data-reveal="fade" data-reveal-at="<0.2">{{ $ctaLabel }}</x-site.atoms.button>
    </div>
</section>
