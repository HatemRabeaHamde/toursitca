<section id="discover" class="lv-section lv-bg-sand" aria-labelledby="lv-discover-title">
    <div class="lv-container">
        <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:gap-10" data-reveal-group>
            <div class="lg:w-2/3">
                <x-landing-v2.section-heading id="lv-discover-title" :eyebrow="site_content('home.discover.eyebrow')" :before="site_content('home.discover.title_before')" :accent="site_content('home.discover.title_accent')" :after="site_content('home.discover.title_after')" />
            </div>
            <div class="lg:w-[360px]">
                <div class="lv-body text-[17px] leading-[1.6]" data-reveal="text" data-reveal-at="<0.4"><x-site.content key="home.discover.description" /></div>
                <x-landing-v2.link-arrow :href="landing_link('discover.link_url', $page->discoverUrl())" class="mt-[18px]" data-reveal="fade" data-reveal-at="<0.2">{{ site_content('home.discover.link') }}</x-landing-v2.link-arrow>
            </div>
        </div>

        <ul data-reveal="wave" class="lv-interest-grid mt-12 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:mt-[68px] lg:grid-cols-6 lg:gap-[18px]">
            @foreach($page->interests() as $interest)
                <li><x-landing-v2.interest-card :interest="$interest" /></li>
            @endforeach
        </ul>
    </div>
</section>
