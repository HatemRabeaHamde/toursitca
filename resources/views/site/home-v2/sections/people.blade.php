@php($trustIcons = config('landing_v2.icons.trust'))

<section id="people" class="lv-section lv-bg-cream" aria-labelledby="lv-people-title">
    <div class="lv-container">
        <div class="grid gap-12 lg:grid-cols-[440px_1fr] lg:gap-[60px]">
            <div data-reveal-group>
                <x-landing-v2.section-heading id="lv-people-title" size="lv-h2--md" :eyebrow="__('landing_v2.people.eyebrow')" :before="__('landing_v2.people.title')" :accent="__('landing_v2.people.title_accent')" />
                <p class="lv-body mt-6 text-[17px] leading-[1.6]" data-reveal="text" data-reveal-at="<0.35">{{ __('landing_v2.people.description') }}</p>
                <x-landing-v2.link-arrow :href="$page->discoverUrl('hosts')" class="mt-6" data-reveal="fade" data-reveal-at="<0.2">{{ __('landing_v2.people.link') }}</x-landing-v2.link-arrow>
            </div>

            @if($page->hosts()->isNotEmpty())
                <ul data-reveal="stagger" class="grid gap-5 sm:grid-cols-3" aria-label="{{ __('site.nav.hosts') }}">
                    @foreach($page->hosts() as $host)
                        <li><x-landing-v2.host-card :host="$host" /></li>
                    @endforeach
                </ul>
            @endif
        </div>

        <hr class="my-16 border-[color:var(--lv-line)] lg:my-[74px]" data-reveal="rule">

        <div class="max-w-[900px]" data-reveal-group>
            <x-landing-v2.eyebrow data-reveal="fade">{{ __('landing_v2.people.trust_eyebrow') }}</x-landing-v2.eyebrow>
            <h3 class="lv-serif lv-h2 lv-h2--sm mt-[18px]" data-reveal="heading" data-reveal-at="<0.1">
                {{ __('landing_v2.people.trust_title_before') }} <span class="lv-accent">{{ __('landing_v2.people.trust_title_accent') }}</span> {{ __('landing_v2.people.trust_title_after') }}
            </h3>
        </div>

        <ul class="mt-9 grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal="stagger">
            @foreach(__('landing_v2.people.trust') as $key => $trust)
                <li class="lv-card p-6">
                    <div class="flex items-center gap-3.5">
                        <span class="lv-icon-box"><img src="{{ asset($trustIcons[$key]) }}" alt="" width="23" height="23" aria-hidden="true"></span>
                        <h4 class="lv-serif lv-trust-title">{{ $trust['title'] }}</h4>
                    </div>
                    <p class="lv-body mt-3">{{ $trust['body'] }}</p>
                </li>
            @endforeach
        </ul>

        <p class="lv-subhead mt-12" data-reveal="fade">{{ __('landing_v2.people.steps_title') }}</p>
        <ol class="mt-7 grid gap-8 md:grid-cols-3 md:gap-6" data-reveal="stagger">
            @foreach(__('landing_v2.people.steps') as $index => $step)
                <li class="flex gap-4">
                    <span class="lv-step-dot lv-serif">{{ $index + 1 }}</span>
                    <div class="max-w-[320px] pt-1.5">
                        <p class="lv-step-title">{{ $step['title'] }}</p>
                        <p class="lv-body mt-1">{{ $step['body'] }}</p>
                    </div>
                </li>
            @endforeach
        </ol>

        <div data-reveal-group>
        <dl data-reveal="fade" class="lv-stats mt-12 grid grid-cols-2 gap-8 px-6 py-6 sm:px-10 lg:grid-cols-4">
            @foreach($page->stats() as $stat)
                <div class="flex flex-col-reverse">
                    <dt class="lv-stat-label mt-1.5">{{ __('landing_v2.people.stats.'.$stat['label_key']) }}</dt>
                    <dd class="lv-serif lv-stat-value" data-reveal="count" data-reveal-at="<0.12" data-count="{{ $stat['value'] }}">{{ $stat['value'] }}</dd>
                </div>
            @endforeach
        </dl>
        </div>
    </div>
</section>
