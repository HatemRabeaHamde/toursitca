@extends('layouts.admin')

@section('page-title', __('ui.pages.admin_title'))
@section('page-subtitle', now()->format('l, F j, Y'))

@section('topbar-actions')
    <a href="{{ route('admin.experiences.create') }}" class="adm-btn adm-btn--primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        {{ __('ui.nav.create_experience') }}
    </a>
@endsection

@section('content')

    {{-- ── 8 Stat cards ────────────────────────────────────── --}}
    @include('admin.dashboard.partials.stat-cards', compact('stats'))

    {{-- ── Trend charts ─────────────────────────────────────── --}}
    <div class="adm-grid-2">

        <div class="adm-card" style="animation-delay:.15s">
            <div class="adm-card-header">
                <span class="adm-card-title">{{ __('ui.dashboard.bookings_created_trend') }}</span>
            </div>
            @php $maxCreated = max(1, $bookingsCreatedTrend->max('value')); @endphp
            <div class="adm-chart">
                @foreach($bookingsCreatedTrend as $item)
                    <div class="adm-chart-col">
                        <div class="adm-chart-value">{{ $item['value'] ?: '' }}</div>
                        <div class="adm-chart-bar adm-chart-bar--navy"
                             style="height: {{ (int) round(($item['value'] / $maxCreated) * 56) }}px; min-height: {{ $item['value'] > 0 ? '6' : '3' }}px;">
                        </div>
                        <div class="adm-chart-label">{{ $item['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="adm-card" style="animation-delay:.18s">
            <div class="adm-card-header">
                <span class="adm-card-title">{{ __('ui.dashboard.bookings_confirmed_trend') }}</span>
            </div>
            @php $maxConfirmed = max(1, $bookingsConfirmedTrend->max('value')); @endphp
            <div class="adm-chart">
                @foreach($bookingsConfirmedTrend as $item)
                    <div class="adm-chart-col">
                        <div class="adm-chart-value">{{ $item['value'] ?: '' }}</div>
                        <div class="adm-chart-bar adm-chart-bar--green"
                             style="height: {{ (int) round(($item['value'] / $maxConfirmed) * 56) }}px; min-height: {{ $item['value'] > 0 ? '6' : '3' }}px;">
                        </div>
                        <div class="adm-chart-label">{{ $item['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ── Recent bookings + Pending agencies ─────────────── --}}
    <div class="adm-grid-3-2">

        @include('admin.dashboard.partials.recent-bookings', compact('recentBookings'))

        @include('admin.dashboard.partials.pending-agencies', compact('pendingAgencies'))

    </div>

    {{-- ── Today's trips + Top experiences ────────────────── --}}
    <div class="adm-grid-2">

        @include('admin.dashboard.partials.todays-trips', compact('todaysSlots'))

        @include('admin.dashboard.partials.top-experiences', compact('topExperiences'))

    </div>

    {{-- ── Almost-full slots + Aged pending agencies ───────── --}}
    <div class="adm-grid-2-1">

        @include('admin.dashboard.partials.almost-full-slots', compact('almostFullSlots'))

        <div class="adm-card" style="animation-delay:.35s">
            <div class="adm-card-header">
                <span class="adm-card-title">{{ __('ui.dashboard.pending_agencies_attention') }}</span>
            </div>
            @if($pendingAgenciesAged->isEmpty())
                <div class="adm-empty">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ __('ui.dashboard.no_pending_agencies_attention') }}
                </div>
            @else
                <div class="adm-card-body--flush">
                    @foreach($pendingAgenciesAged as $agency)
                        <div class="adm-agency-row">
                            <div class="adm-agency-initials adm-agency-initials--aged">
                                {{ strtoupper(substr($agency->name, 0, 2)) }}
                            </div>
                            <div class="adm-agency-info">
                                <div class="adm-agency-name">{{ $agency->name }}</div>
                                <div class="adm-agency-meta" style="color:var(--amber);">
                                    {{ __('ui.dashboard.pending_since') }} {{ $agency->created_at->format('M j') }}
                                    · {{ $agency->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="adm-agency-actions">
                                <form method="POST" action="{{ route('admin.agencies.approve', $agency) }}">
                                    @csrf
                                    <button type="submit" class="adm-btn adm-btn--success adm-btn--sm">
                                        {{ __('ui.nav.approve') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

@endsection
