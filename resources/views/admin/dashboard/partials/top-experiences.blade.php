<div class="adm-card" style="animation-delay:.33s">
    <div class="adm-card-header">
        <span class="adm-card-title">{{ __('ui.dashboard.top_experiences') }}</span>
        <a href="{{ route('admin.experiences.index') }}" class="adm-card-link">
            {{ __('ui.nav.view_all') }}
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>

    @if($topExperiences->isEmpty())
        <div class="adm-empty">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            {{ __('ui.dashboard.no_experiences_yet') }}
        </div>
    @else
        <div class="adm-card-body--flush">
            @foreach($topExperiences as $i => $exp)
                <div class="adm-top-exp-row">
                    <div class="adm-top-exp-rank">{{ $i + 1 }}</div>
                    <div class="adm-top-exp-info">
                        <div class="adm-top-exp-title">
                            {{ Str::limit($exp->getTranslation('title', app()->getLocale(), false), 32) }}
                        </div>
                        <div class="adm-top-exp-agency">{{ $exp->agency?->name }}</div>
                    </div>
                    <div class="adm-top-exp-stats">
                        <div class="adm-top-exp-total">{{ $exp->bookings_total }}</div>
                        <div class="adm-top-exp-label">bookings</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
