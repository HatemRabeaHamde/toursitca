<section id="overview" class="lv-xsection" aria-labelledby="overview-title">
    <h2 id="overview-title" class="lv-t-h2 lv-serif lv-tone-ink">{{ __('experiences.detail.overview.highlights') }}</h2>
    <ul class="lv-v1-highlights">
        @foreach($page->highlights as $highlight)
            <li class="lv-v1-highlights__item lv-t-ui-s lv-tone-ink">
                <x-site.atoms.icon name="sparkle-terracotta" :size="26" />{{ $highlight }}
            </li>
        @endforeach
    </ul>
</section>
