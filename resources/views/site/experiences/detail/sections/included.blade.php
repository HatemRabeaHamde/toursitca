<section id="included" class="lv-xsection" aria-labelledby="included-title" data-reveal-group>
    <h2 id="included-title" class="lv-xsection__title lv-t-h2 lv-serif lv-tone-ink" data-reveal="heading">{{ __('experiences.detail.included.title') }}</h2>
    <ul class="grid gap-3 sm:grid-cols-2" data-reveal="fade" data-reveal-at="<0.2">
        @foreach($page->included['items'] as $item)
            <x-site.atoms.check-item>{{ $item }}</x-site.atoms.check-item>
        @endforeach
    </ul>
    @if($page->included['excluded'])
        <p class="lv-t-caption lv-tone-muted" data-reveal="fade" data-reveal-at="<0.1">{{ $page->included['excluded'] }}</p>
    @endif
</section>
