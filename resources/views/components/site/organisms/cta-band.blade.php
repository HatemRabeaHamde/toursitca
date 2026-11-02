{{-- Dark green closing band ("Still exploring?"). Copy on the left, one large action on the right. --}}
@props(['eyebrow', 'title', 'accent' => null, 'lede' => null, 'ctaLabel', 'ctaHref'])

<section {{ $attributes->class(['lv-cta-band']) }} data-reveal-group>
    <div class="lv-cta-band__pattern" style="background-image: url('{{ asset(config('landing_v2.images.footer_pattern')) }}')" aria-hidden="true"></div>
    <div class="lv-container lv-cta-band__inner">
        <div class="lv-cta-band__copy">
            <x-site.atoms.eyebrow tone="gold" data-reveal="fade">{{ $eyebrow }}</x-site.atoms.eyebrow>
            <h2 class="lv-t-section lv-cta-band__title" data-reveal="heading" data-reveal-at="<0.1">
                {{ $title }}
                @if($accent)<em class="lv-italic">{{ $accent }}</em>@endif
            </h2>
            @if($lede)<p class="lv-t-body-l lv-cta-band__lede" data-reveal="text" data-reveal-at="<0.3">{{ $lede }}</p>@endif
        </div>
        <x-site.atoms.button :href="$ctaHref" class="lv-cta-band__button" data-reveal="fade" data-reveal-at="<0.2">{{ $ctaLabel }}</x-site.atoms.button>
    </div>
</section>
