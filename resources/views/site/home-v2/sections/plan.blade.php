@php($icons = config('landing_v2.icons'))

<section id="plan" class="lv-section lv-bg-cream" aria-labelledby="lv-plan-title">
    <div class="lv-container">
        <x-landing-v2.section-heading id="lv-plan-title" size="lv-h2--md" :eyebrow="__('landing_v2.plan.eyebrow')" :before="__('landing_v2.plan.title')" :accent="__('landing_v2.plan.title_accent')" />

        <ol class="mt-12 grid gap-10 sm:grid-cols-2 lg:mt-[62px] lg:grid-cols-4 lg:gap-6" data-reveal-group>
            @foreach(__('landing_v2.plan.steps') as $key => $step)
                <li>
                    <span class="lv-serif lv-plan-num block" aria-hidden="true" data-reveal="text" data-reveal-at="<0.1">{{ str_pad((string) ($loop->iteration), 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="lv-plan-rule mt-3.5" data-reveal="rule" data-reveal-at="<0.06"></div>
                    <h3 class="mt-[22px] flex items-center gap-3" data-reveal="fade" data-reveal-at="<0.06">
                        <img src="{{ asset($icons['steps'][$key]) }}" alt="" width="26" height="26" aria-hidden="true">
                        <span class="lv-serif lv-plan-title">{{ $step['title'] }}</span>
                    </h3>
                    <p class="lv-body mt-3 max-w-[250px] text-base" data-reveal="fade" data-reveal-at="<0.06">{{ $step['body'] }}</p>
                </li>
            @endforeach
        </ol>

        <p data-reveal="fade" class="mt-10 flex items-center gap-3.5 text-[17px] text-[color:var(--lv-ink)] lg:mt-3.5">
            <span class="lv-check-dot"><img src="{{ asset($icons['check_light']) }}" alt="" width="16" height="16" aria-hidden="true"></span>
            <span><strong class="font-bold">{{ __('landing_v2.plan.reassurance_strong') }}</strong> {{ __('landing_v2.plan.reassurance') }}</span>
        </p>
    </div>
</section>
