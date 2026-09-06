@php
    $cards = [
        [
            'value'   => number_format($stats['experiences_published']),
            'sub'     => number_format($stats['experiences_total']) . ' ' . __('ui.agency_dashboard.total'),
            'label'   => __('ui.agency_dashboard.experiences_published'),
            'color'   => 'sage',
            'href'    => route('agency.experiences.index'),
            'icon'    => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
        ],
        [
            'value'   => number_format($stats['experiences_draft']),
            'sub'     => $stats['experiences_draft'] > 0 ? __('ui.agency_dashboard.needs_publishing') : __('ui.agency_dashboard.all_published'),
            'label'   => __('ui.agency_dashboard.experiences_draft'),
            'color'   => $stats['experiences_draft'] > 0 ? 'amber' : 'green',
            'href'    => route('agency.experiences.index'),
            'icon'    => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>',
        ],
        [
            'value'   => number_format($stats['bookings_pending']),
            'sub'     => number_format($stats['bookings_confirmed']) . ' ' . __('ui.agency_dashboard.confirmed'),
            'label'   => __('ui.agency_dashboard.bookings_pending'),
            'color'   => $stats['bookings_pending'] > 0 ? 'deal' : 'green',
            'href'    => route('agency.bookings.index', ['status' => 'pending']),
            'icon'    => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        ],
        [
            'value'   => number_format((float) $stats['agency_earnings_total'], 0) . ' MAD',
            'sub'     => number_format((float) $stats['agency_earnings_month'], 0) . ' MAD ' . __('ui.agency_dashboard.this_month'),
            'label'   => __('ui.agency_dashboard.earnings_total'),
            'color'   => 'gold',
            'href'    => route('agency.payouts.index'),
            'icon'    => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
        ],
        [
            'value'   => number_format((float) $stats['pending_payout'], 0) . ' MAD',
            'sub'     => __('ui.agency_dashboard.awaiting_transfer'),
            'label'   => __('ui.agency_dashboard.pending_payout'),
            'color'   => 'clay',
            'href'    => route('agency.payouts.index'),
            'icon'    => '<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
        ],
        [
            'value'   => $stats['reviews_count'] > 0 ? number_format($stats['reviews_avg'], 1) . ' ★' : '—',
            'sub'     => number_format($stats['reviews_count']) . ' ' . __('ui.agency_dashboard.reviews'),
            'label'   => __('ui.agency_dashboard.avg_rating'),
            'color'   => 'indigo',
            'href'    => route('agency.reviews.index'),
            'icon'    => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
        ],
    ];
@endphp

<div class="ag-stats-grid">
    @foreach ($cards as $i => $card)
        <a href="{{ $card['href'] }}" class="ag-stat-card ag-stat-card--{{ $card['color'] }}" style="animation-delay: {{ $i * 0.05 + 0.03 }}s;">
            <div class="ag-stat-icon ag-stat-icon--{{ $card['color'] }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                    {!! $card['icon'] !!}
                </svg>
            </div>
            <div class="ag-stat-body">
                <div class="ag-stat-value">{{ $card['value'] }}</div>
                <div class="ag-stat-sub">{{ $card['sub'] }}</div>
                <div class="ag-stat-label">{{ $card['label'] }}</div>
            </div>
        </a>
    @endforeach
</div>
