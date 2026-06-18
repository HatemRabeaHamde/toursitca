<div class="agency-wrap">
    <div class="agency-box">
        <div class="agency-l">
            <div>
                <p class="agency-eyebrow">{{ __('ui.landing.sections.agency_cta_label') }}</p>
                <h2 class="agency-h">{{ __('ui.landing.sections.agency_cta_title') }}</h2>
                <p class="agency-desc">{{ __('ui.landing.sections.agency_cta_body') }}</p>
                <div class="agency-perks">
                    @foreach (__('ui.auth.agency_perks') as $perk)
                        <div class="ap"><span class="ap-ic">{{ $perk['icon'] }}</span> {{ $perk['text'] }}</div>
                    @endforeach
                </div>
            </div>
            <div class="agency-btns">
                <a href="{{ route('agency.register', ['locale' => app()->getLocale()]) }}" class="btn-light">{{ __('ui.landing.sections.agency_cta_primary') }}</a>
                <a href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}" class="btn-ghost-w">
                    {{ __('ui.landing.sections.agency_cta_secondary') }}
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </div>
        <div class="agency-r">
            <img src="https://images.unsplash.com/photo-1539020140153-e479b8c22e70?w=900&q=80" alt="{{ __('ui.landing.sections.agency_cta_title') }}" loading="lazy">
        </div>
    </div>
</div>
