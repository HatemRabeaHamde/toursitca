@if ($detail->itinerary['has_items'])
    @php($map = $detail->itinerary['map'])

    <section id="itinerary" class="xp-detail-section xp-itinerary-section">
        <div class="xp-itinerary-head">
            <div>
                <p class="xp-section-kicker">{{ __('ui.experience_detail.itinerary.kicker') }}</p>
                <h2 class="xp-section-heading">{{ __('ui.experience_detail.itinerary.title') }}</h2>
            </div>
            <p class="xp-itinerary-summary">
                @if ($detail->itinerary['start_count'] > 0)
                    {{ trans_choice('ui.experience_detail.itinerary.starting_options', $detail->itinerary['start_count'], ['count' => $detail->itinerary['start_count']]) }}
                @endif
                @if ($detail->itinerary['dropoff_count'] > 0)
                    <span>{{ trans_choice('ui.experience_detail.itinerary.dropoff_options', $detail->itinerary['dropoff_count'], ['count' => $detail->itinerary['dropoff_count']]) }}</span>
                @endif
            </p>
        </div>

        <div class="xp-itinerary-layout">
            <div class="xp-itinerary-timeline">
                @foreach ($detail->itinerary['items'] as $index => $item)
                    <article @class([
                        'xp-itinerary-step',
                        'is-main' => $item['is_main_stop'],
                        'is-end' => $loop->last,
                    ])>
                        <span class="xp-itinerary-node" aria-hidden="true">
                            @include('site.experiences.partials.itinerary-icon', ['icon' => $item['icon']])
                        </span>
                        <div class="xp-itinerary-content">
                            @if ($item['type'] === 'start' && $detail->itinerary['start_count'] > 1 && $index === 0)
                                <p class="xp-itinerary-label">{{ trans_choice('ui.experience_detail.itinerary.starting_options', $detail->itinerary['start_count'], ['count' => $detail->itinerary['start_count']]) }}</p>
                            @elseif ($item['type'] === 'dropoff' && $detail->itinerary['dropoff_count'] > 1)
                                <p class="xp-itinerary-label">{{ trans_choice('ui.experience_detail.itinerary.dropoff_options', $detail->itinerary['dropoff_count'], ['count' => $detail->itinerary['dropoff_count']]) }}</p>
                            @endif

                            <h3>{{ $item['title'] ?: $item['location_name'] }}</h3>
                            @if ($item['location_name'] && $item['location_name'] !== $item['title'])
                                <p class="xp-itinerary-place">{{ $item['location_name'] }}</p>
                            @endif
                            @if ($item['description'] || $item['duration'])
                                <p class="xp-itinerary-desc">
                                    {{ $item['description'] }}
                                    @if ($item['duration'])
                                        <span>{{ $item['duration'] }}</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <aside class="xp-itinerary-map-card">
                @if ($map['has_map'])
                    <iframe
                        class="xp-itinerary-map"
                        title="{{ __('ui.experience_detail.itinerary.map_title') }}"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.openstreetmap.org/export/embed.html?bbox={{ urlencode($map['bbox']) }}&amp;layer=mapnik&amp;marker={{ $map['center_lat'] }},{{ $map['center_lng'] }}">
                    </iframe>
                @else
                    <div class="xp-itinerary-map-empty">
                        <strong>{{ __('ui.experience_detail.itinerary.no_map_title') }}</strong>
                        <p>{{ __('ui.experience_detail.itinerary.no_map_body') }}</p>
                    </div>
                @endif
                <div class="xp-itinerary-legend">
                    <span><i class="main"></i>{{ __('ui.experience_detail.itinerary.main_stop') }}</span>
                    <span><i></i>{{ __('ui.experience_detail.itinerary.other_stop') }}</span>
                </div>
            </aside>
        </div>
    </section>
@endif
