<section class="agencies-section">
    <div class="sec-head">
        <div>
            <p class="sec-label">{{ __('ui.landing.sections.agencies_label') }}</p>
            <h2 class="sec-h2">{{ __('ui.landing.sections.agencies_title') }}</h2>
        </div>

        <a href="#" class="see-all">
            {{ __('ui.landing.sections.all_agencies') }}
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>

    @if ($landing->topAgencies->isNotEmpty())
        <div class="agencies-grid">
            @foreach ($landing->topAgencies as $agency)
                <article class="ag-card">
                    <div class="ag-avatar">
                        <img src="{{ $agency['image_url'] }}" alt="{{ $agency['name'] }}" loading="lazy">
                    </div>
                    <p class="ag-name">{{ $agency['name'] }}</p>
                    <p class="ag-city">📍 {{ $agency['city'] ?: __('ui.landing.morocco') }}</p>
                    <div class="ag-meta">
                        <div class="ag-m">
                            <strong>{{ number_format($agency['experiences_count']) }}</strong>
                            <span>{{ __('ui.landing.agency_metrics.trips') }}</span>
                        </div>
                        <div class="ag-m">
                            <strong>{{ $agency['rating_avg'] }}★</strong>
                            <span>{{ __('ui.landing.agency_metrics.rating') }}</span>
                        </div>
                        <div class="ag-m">
                            <strong>{{ number_format($agency['reviews_count']) }}</strong>
                            <span>{{ __('ui.landing.agency_metrics.reviews') }}</span>
                        </div>
                    </div>
                    @if ($agency['is_verified'])
                        <p class="ag-verified">✓ {{ __('ui.landing.verified_agency') }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    @else
        <div class="agencies-empty">{{ __('ui.landing.sections.agencies_empty') }}</div>
    @endif
</section>
