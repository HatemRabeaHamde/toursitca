@php
$cards = [
    [
        'color'  => 'navy',
        'label'  => __('ui.dashboard.agencies_total'),
        'value'  => $stats['agencies_total'],
        'href'   => route('admin.agencies.index'),
        'icon'   => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
    ],
    [
        'color'  => $stats['agencies_pending'] > 0 ? 'amber' : 'indigo',
        'label'  => __('ui.dashboard.agencies_pending'),
        'value'  => $stats['agencies_pending'],
        'href'   => route('admin.agencies.index') . '?status=pending',
        'urgent' => $stats['agencies_pending'] > 0,
        'icon'   => '<circle cx="12" cy="8" r="4"/><path d="M6 20v-1a6 6 0 0 1 12 0v1"/><line x1="12" y1="14" x2="12" y2="22"/>',
    ],
    [
        'color'  => 'indigo',
        'label'  => __('ui.dashboard.experiences_total'),
        'value'  => $stats['experiences_total'],
        'href'   => route('admin.experiences.index'),
        'icon'   => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
    ],
    [
        'color'  => 'sage',
        'label'  => __('ui.dashboard.experiences_published'),
        'value'  => $stats['experiences_published'],
        'href'   => route('admin.experiences.index') . '?status=published',
        'icon'   => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
    ],
    [
        'color'  => $stats['bookings_pending'] > 0 ? 'deal' : 'green',
        'label'  => __('ui.dashboard.bookings_pending'),
        'value'  => $stats['bookings_pending'],
        'href'   => route('admin.bookings.index') . '?status=pending',
        'urgent' => $stats['bookings_pending'] > 0,
        'icon'   => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>',
    ],
    [
        'color'  => 'green',
        'label'  => __('ui.dashboard.bookings_confirmed'),
        'value'  => $stats['bookings_confirmed'],
        'href'   => route('admin.bookings.index') . '?status=confirmed',
        'icon'   => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
    ],
    [
        'color'  => 'gold',
        'label'  => __('ui.dashboard.revenue_confirmed'),
        'value'  => number_format($stats['revenue_confirmed'], 0) . ' MAD',
        'href'   => route('admin.bookings.index') . '?status=confirmed',
        'icon'   => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
    ],
    [
        'color'  => $stats['payouts_pending'] > 0 ? 'clay' : 'indigo',
        'label'  => __('ui.dashboard.payouts_pending_count'),
        'value'  => $stats['payouts_pending'],
        'href'   => '#',
        'urgent' => $stats['payouts_pending'] > 0,
        'icon'   => '<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
    ],
];
@endphp

<div class="adm-stats-grid">
    @foreach($cards as $card)
        <a href="{{ $card['href'] }}"
           class="adm-stat-card adm-stat-card--{{ $card['color'] }}{{ !empty($card['urgent']) ? ' adm-stat-card--urgent' : '' }}"
           style="text-decoration:none;">
            <div class="adm-stat-icon adm-stat-icon--{{ $card['color'] }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    {!! $card['icon'] !!}
                </svg>
            </div>
            <div class="adm-stat-body">
                <div class="adm-stat-value">{{ $card['value'] }}</div>
                <div class="adm-stat-label">{{ $card['label'] }}</div>
                <div class="adm-stat-link">
                    {{ __('ui.nav.view_all') }}
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </div>
            </div>
        </a>
    @endforeach
</div>
