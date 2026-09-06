<div class="adm-card" style="animation-delay:.25s">
    <div class="adm-card-header">
        <span class="adm-card-title">{{ __('ui.dashboard.pending_agencies') }}</span>
        <a href="{{ route('admin.agencies.index') }}?status=pending" class="adm-card-link">
            {{ __('ui.nav.view_all') }}
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>

    @if($pendingAgencies->isEmpty())
        <div class="adm-empty">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ __('ui.dashboard.no_pending_agencies') }}
        </div>
    @else
        <div class="adm-card-body--flush">
            @foreach($pendingAgencies as $agency)
                @php
                    $isAged = $agency->created_at->diffInDays(now()) >= 3;
                    $initials = strtoupper(substr($agency->name, 0, 2));
                @endphp
                <div class="adm-agency-row">
                    <div class="adm-agency-initials {{ $isAged ? 'adm-agency-initials--aged' : '' }}">{{ $initials }}</div>
                    <div class="adm-agency-info">
                        <div class="adm-agency-name">{{ $agency->name }}</div>
                        <div class="adm-agency-meta">
                            {{ $agency->user->email }}
                            @if($isAged)
                                · <span style="color:var(--amber);font-weight:600;">{{ __('ui.dashboard.aged_warning') }}</span>
                            @else
                                · {{ __('ui.dashboard.pending_since') }} {{ $agency->created_at->format('M j') }}
                            @endif
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
