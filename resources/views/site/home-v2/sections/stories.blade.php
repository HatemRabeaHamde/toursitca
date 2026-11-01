<section id="stories" class="lv-section lv-bg-sand" aria-labelledby="lv-stories-title">
    <div class="lv-container">
        <div data-reveal-group>
        <x-landing-v2.section-heading id="lv-stories-title" size="lv-h2--md" :eyebrow="__('landing_v2.stories.eyebrow')" :before="__('landing_v2.stories.title')" :accent="__('landing_v2.stories.title_accent')" />
        <p class="lv-lede mt-4" data-reveal="text" data-reveal-at="<0.35">{{ __('landing_v2.stories.description') }}</p>
        </div>

        <ul class="mt-12 grid gap-6 md:grid-cols-2 lg:mt-[74px] lg:grid-cols-3 lg:gap-8" data-reveal="stagger">
            @foreach($page->stories() as $story)
                <li><x-landing-v2.story-card :story="$story" /></li>
            @endforeach
        </ul>
    </div>
</section>
