<section id="overview" class="lv-xsection" aria-labelledby="overview-title" data-reveal-group>
    <h2 id="overview-title" class="lv-xsection__title lv-t-h2 lv-serif lv-tone-ink" data-reveal="heading">{{ __('experiences.detail.overview.title') }}</h2>
    @if($page->description)
        <p class="lv-t-body-l lv-tone-ink" data-reveal="text" data-reveal-at="<0.2">{{ $page->description }}</p>
    @endif
    @if($page->highlights)
        <ul class="flex flex-wrap gap-3" aria-label="{{ __('experiences.detail.overview.highlights') }}" data-reveal="fade" data-reveal-at="<0.2">
            @foreach($page->highlights as $highlight)
                <x-site.molecules.highlight-pill>{{ $highlight }}</x-site.molecules.highlight-pill>
            @endforeach
        </ul>
    @endif
</section>
