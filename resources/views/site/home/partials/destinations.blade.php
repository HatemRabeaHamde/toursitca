@if ($landing->destinations->isNotEmpty())
    <section class="dest-section" id="destinations">
        <div class="sec-head">
            <div>
                <p class="sec-label">{{ __('ui.landing.sections.destinations_label') }}</p>
                <h2 class="sec-h2">{{ __('ui.landing.sections.destinations_title') }}</h2>
            </div>
            <a href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}" class="see-all">
                {{ __('ui.landing.sections.all_destinations') }}
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <div class="dest-grid">
            @foreach ($landing->destinations->take(5) as $destination)
                <a class="dc" href="{{ $destination['url'] }}">
                    <img src="{{ $destination['image_url'] }}" alt="{{ $destination['name'] }}" loading="lazy">
                    <div class="dc-veil"></div>
                    <div class="dc-body">
                        <h3 class="dc-name">{{ $destination['name'] }}</h3>
                        <p class="dc-cnt">{{ trans_choice('ui.messages.experience_results', $destination['experiences_count'], ['count' => $destination['experiences_count']]) }}</p>
                        @if ($destination['is_popular'])
                            <span class="dc-tag">{{ __('ui.landing.popular_destination') }}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif
