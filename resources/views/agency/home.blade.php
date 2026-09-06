@extends('layouts.agency')

@section('page-title')
    {{ __('ui.agency_dashboard.welcome_back') }} <em>{{ auth()->user()->name }}</em>
@endsection

@section('page-subtitle', __('ui.agency_dashboard.welcome_subtitle'))

@section('topbar-actions')
    <a href="{{ route('agency.experiences.create') }}" class="ag-btn-new">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        {{ __('ui.agency_dashboard.new_experience') }}
    </a>
@endsection

@section('content')

    {{-- ── Stat cards ──────────────────────────────────── --}}
    @include('agency.dashboard.partials.stat-cards', compact('stats'))

    {{-- ── Bookings · Actions · Links ─────────────────── --}}
    <div class="ag-grid-3-2">

        @include('agency.dashboard.partials.recent-bookings', compact('recentBookings'))

        <div style="display:flex;flex-direction:column;gap:16px;">
            @include('agency.dashboard.partials.pending-actions', compact('draftExperiences','upcomingIn48h'))
            @include('agency.dashboard.partials.quick-links')
        </div>

    </div>

    {{-- ── 7-day trend charts ───────────────────────────── --}}
    <div class="ag-grid-2">

        <div class="ag-card" style="animation: agFadeUp .4s .3s both;">
            <div class="ag-card-header">
                <span class="ag-card-title">{{ __('ui.agency_dashboard.bookings_created_trend') }}</span>
            </div>
            @php $maxCreated = max(1, $bookingsCreatedTrend->max('value')); @endphp
            <div class="ag-chart">
                @foreach ($bookingsCreatedTrend as $item)
                    <div class="ag-chart-col">
                        <div class="ag-chart-value">{{ $item['value'] ?: '' }}</div>
                        <div class="ag-chart-bar ag-chart-bar--clay"
                             style="height: {{ (int) round(($item['value'] / $maxCreated) * 56) }}px; min-height: {{ $item['value'] > 0 ? '6' : '3' }}px;">
                        </div>
                        <div class="ag-chart-label">{{ $item['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="ag-card" style="animation: agFadeUp .4s .35s both;">
            <div class="ag-card-header">
                <span class="ag-card-title">{{ __('ui.agency_dashboard.bookings_confirmed_trend') }}</span>
            </div>
            @php $maxConfirmed = max(1, $bookingsConfirmedTrend->max('value')); @endphp
            <div class="ag-chart">
                @foreach ($bookingsConfirmedTrend as $item)
                    <div class="ag-chart-col">
                        <div class="ag-chart-value">{{ $item['value'] ?: '' }}</div>
                        <div class="ag-chart-bar ag-chart-bar--sage"
                             style="height: {{ (int) round(($item['value'] / $maxConfirmed) * 56) }}px; min-height: {{ $item['value'] > 0 ? '6' : '3' }}px;">
                        </div>
                        <div class="ag-chart-label">{{ $item['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ── Upcoming & almost-full slots ────────────────── --}}
    <div class="ag-grid-2">

        <div class="ag-card" style="animation: agFadeUp .4s .4s both;">
            <div class="ag-card-header">
                <span class="ag-card-title">{{ __('ui.agency_dashboard.upcoming_slots') }}</span>
                <a href="{{ route('agency.availability.index') }}" class="ag-section-link">
                    {{ __('ui.agency_dashboard.view_all') }}
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>

            @if ($upcomingSlots->isEmpty())
                <div class="ag-empty">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ __('ui.messages.no_availability') }}
                </div>
            @else
                <div class="ag-slot-grid">
                    @foreach ($upcomingSlots as $slot)
                        @php
                            $avail = $slot->availableSeats();
                            $pct   = $slot->max_seats > 0 ? round(($avail / $slot->max_seats) * 100) : 0;
                            $fillClass = $pct <= 20 ? 'ag-slot-seats-fill--full' : ($pct <= 50 ? 'ag-slot-seats-fill--warn' : '');
                        @endphp
                        <div class="ag-slot-item">
                            <div class="ag-slot-exp">{{ $slot->experience->getTranslation('title', app()->getLocale(), false) }}</div>
                            <div class="ag-slot-date">{{ $slot->date->format('M j, Y') }} · {{ substr($slot->time_slot, 0, 5) }}</div>
                            <div class="ag-slot-seats">
                                <span style="color: var(--ink-70); font-weight:400;">{{ $avail }}/{{ $slot->max_seats }}</span>
                                <div class="ag-slot-seats-bar">
                                    <div class="ag-slot-seats-fill {{ $fillClass }}" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="ag-card" style="animation: agFadeUp .4s .45s both;">
            <div class="ag-card-header">
                <span class="ag-card-title">{{ __('ui.agency_dashboard.almost_full_slots') }}</span>
                <a href="{{ route('agency.availability.index') }}" class="ag-section-link">
                    {{ __('ui.agency_dashboard.view_all') }}
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>

            @if ($almostFullSlots->isEmpty())
                <div class="ag-empty">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ __('ui.agency_dashboard.no_almost_full_slots') }}
                </div>
            @else
                <div class="ag-slot-grid">
                    @foreach ($almostFullSlots as $slot)
                        @php
                            $avail = $slot->availableSeats();
                            $pct   = $slot->max_seats > 0 ? round(($avail / $slot->max_seats) * 100) : 0;
                        @endphp
                        <div class="ag-slot-item">
                            <div class="ag-slot-exp">{{ $slot->experience->getTranslation('title', app()->getLocale(), false) }}</div>
                            <div class="ag-slot-date">{{ $slot->date->format('M j, Y') }} · {{ substr($slot->time_slot, 0, 5) }}</div>
                            <div class="ag-slot-seats">
                                <span style="color: var(--amber); font-weight:600;">{{ $avail }} / {{ $slot->max_seats }}</span>
                                <div class="ag-slot-seats-bar">
                                    <div class="ag-slot-seats-fill ag-slot-seats-fill--warn" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

@endsection
