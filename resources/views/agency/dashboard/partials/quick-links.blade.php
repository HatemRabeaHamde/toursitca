@php
    $links = [
        [
            'label'  => __('ui.agency_dashboard.quick_add_experience'),
            'href'   => route('agency.experiences.create'),
            'color'  => 'clay',
            'icon'   => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        ],
        [
            'label'  => __('ui.agency_dashboard.quick_availability'),
            'href'   => route('agency.availability.index'),
            'color'  => 'sage',
            'icon'   => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        ],
        [
            'label'  => __('ui.agency_dashboard.quick_bookings'),
            'href'   => route('agency.bookings.index'),
            'color'  => 'indigo',
            'icon'   => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        ],
        [
            'label'  => __('ui.agency_dashboard.quick_experiences'),
            'href'   => route('agency.experiences.index'),
            'color'  => 'gold',
            'icon'   => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
        ],
    ];
@endphp

<div class="ag-card" style="animation: agFadeUp .4s .25s both;">

    <div class="ag-card-header">
        <span class="ag-card-title">{{ __('ui.agency_dashboard.quick_links') }}</span>
    </div>

    <ul class="ag-quick-list ag-card-body--flush">
        @foreach ($links as $link)
            <li>
                <a href="{{ $link['href'] }}" class="ag-quick-item">
                    <div class="ag-quick-icon ag-stat-icon--{{ $link['color'] }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            {!! $link['icon'] !!}
                        </svg>
                    </div>
                    {{ $link['label'] }}
                    <svg class="ag-quick-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </li>
        @endforeach
    </ul>

</div>
