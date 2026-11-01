{{-- Light sand recruitment band ("Are you a local guide…? → Become a host"). Sits right above the footer. --}}
@props(['title', 'lede' => null, 'ctaLabel', 'ctaHref'])

<section {{ $attributes->class(['lv-join-band']) }} data-reveal-group>
    <div class="lv-container flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="flex max-w-[640px] flex-col gap-2">
            <h2 class="lv-t-h3 lv-tone-ink" data-reveal="heading">{{ $title }}</h2>
            @if($lede)<p class="lv-t-body-m lv-tone-muted" data-reveal="text" data-reveal-at="<0.2">{{ $lede }}</p>@endif
        </div>
        <x-site.atoms.button :href="$ctaHref" class="self-start md:self-auto" data-reveal="fade" data-reveal-at="<0.2">{{ $ctaLabel }}</x-site.atoms.button>
    </div>
</section>
