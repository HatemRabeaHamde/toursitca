<section id="itinerary" class="lv-xsection" aria-labelledby="itinerary-title" data-reveal-group>
    <h2 id="itinerary-title" class="lv-xsection__title lv-t-h2 lv-serif lv-tone-ink" data-reveal="heading">{{ __('experiences.detail.itinerary.title') }}</h2>
    <x-site.organisms.itinerary-accordion :items="$page->itinerary" data-reveal="fade" data-reveal-at="<0.2" />
</section>
