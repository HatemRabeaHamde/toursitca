<section id="stories" class="lv-section lv-bg-sand" aria-labelledby="lv-stories-title">
    <div class="lv-container">
        <div data-reveal-group>
        <x-landing-v2.section-heading id="lv-stories-title" size="lv-h2--md" :eyebrow="site_content('home.stories.eyebrow')" :before="site_content('home.stories.title')" :accent="site_content('home.stories.title_accent')" />
        <div class="lv-lede mt-4" data-reveal="text" data-reveal-at="<0.35"><x-site.content key="home.stories.description" /></div>
        </div>

        <ul class="mt-12 grid gap-6 md:grid-cols-2 lg:mt-[74px] lg:grid-cols-3 lg:gap-8" data-reveal="stagger">
            @foreach($page->stories($content) as $story)
                <li><x-landing-v2.story-card :story="$story" /></li>
            @endforeach
        </ul>
    </div>
</section>
