@extends('layouts.admin')

@section('page-title', $agency->name)
@section('page-subtitle', 'Agency profile · ' . ucfirst($agency->status))

@section('topbar-actions')
    <a href="{{ route('admin.agencies.edit', $agency) }}" class="adm-btn adm-btn--ghost">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit Agency
    </a>
    <a href="{{ route('admin.agencies.index') }}" class="adm-btn adm-btn--ghost">
        ← Back
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="adm-flash adm-flash--success" x-data x-init="setTimeout(() => $el.remove(), 4000)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="adm-grid-2-1">

        {{-- Left: profile + experiences --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Profile card --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <span class="adm-card-title">Agency Profile</span>
                    @php
                        $statusClass = match($agency->status) {
                            'active'    => 'adm-badge--active',
                            'pending'   => 'adm-badge--pending',
                            'suspended' => 'adm-badge--cancelled',
                            'rejected'  => 'adm-badge--cancelled',
                            default     => 'adm-badge--pending',
                        };
                    @endphp
                    <span class="adm-badge {{ $statusClass }}">{{ __('ui.status.'.$agency->status) }}</span>
                </div>
                <div class="adm-card-body" style="display:flex;gap:20px;align-items:flex-start;">
                    <div class="adm-agency-initials" style="width:56px;height:56px;font-size:1.2rem;flex-shrink:0;">
                        {{ strtoupper(substr($agency->name, 0, 2)) }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:1.1rem;font-weight:700;color:var(--adm-text);margin-bottom:4px;">
                            {{ $agency->name }}
                            @if($agency->is_platform)
                                <span class="adm-badge adm-badge--confirmed" style="margin-left:8px;font-size:.65rem;">Platform</span>
                            @endif
                        </div>
                        <div style="font-size:.875rem;color:var(--adm-text-muted);margin-bottom:12px;">
                            {{ $agency->city }}
                            @if($agency->phone) · {{ $agency->phone }} @endif
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div>
                                <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Owner</div>
                                <div style="font-size:.875rem;color:var(--adm-text);">{{ $agency->user->name }}</div>
                                <div style="font-size:.75rem;color:var(--adm-text-muted);">{{ $agency->user->email }}</div>
                            </div>
                            <div>
                                <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Commission Rate</div>
                                <div style="font-size:1.1rem;font-weight:700;color:var(--adm-navy);">{{ $agency->commission_rate }}%</div>
                            </div>
                            <div>
                                <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Member Since</div>
                                <div style="font-size:.875rem;color:var(--adm-text);">{{ $agency->created_at->format('M j, Y') }}</div>
                            </div>
                            <div>
                                <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Languages</div>
                                <div style="font-size:.875rem;color:var(--adm-text);">
                                    {{ implode(', ', array_map('strtoupper', $agency->languages ?? [])) ?: '—' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent experiences --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <span class="adm-card-title">Recent Experiences</span>
                    <a href="{{ route('admin.experiences.index') }}?agency={{ $agency->id }}" class="adm-card-link">
                        View all
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
                @if($agency->experiences->isEmpty())
                    <div class="adm-empty">No experiences yet.</div>
                @else
                    <div class="adm-card-body--flush">
                        @foreach($agency->experiences as $exp)
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 20px;border-bottom:1px solid var(--adm-border);">
                                <div>
                                    <div style="font-size:.875rem;font-weight:600;color:var(--adm-text);">
                                        {{ $exp->getTranslation('title', app()->getLocale(), false) }}
                                    </div>
                                    <div style="font-size:.75rem;color:var(--adm-text-muted);">
                                        {{ ucfirst($exp->category ?? '') }}
                                        @if($exp->duration_hours) · {{ $exp->duration_hours }}h @endif
                                    </div>
                                </div>
                                <span class="adm-badge {{ $exp->status === 'published' ? 'adm-badge--published' : 'adm-badge--draft' }}">
                                    {{ ucfirst($exp->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- Right: stats + actions --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Quick stats --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <span class="adm-card-title">Quick Stats</span>
                </div>
                <div class="adm-card-body" style="display:flex;flex-direction:column;gap:16px;">
                    @foreach([
                        ['label' => 'Total Experiences', 'value' => $stats['experiences_total'], 'icon' => 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'],
                        ['label' => 'Published', 'value' => $stats['experiences_published'], 'icon' => 'M22 11.08V12a10 10 0 1 1-5.93-9.14'],
                        ['label' => 'Total Bookings', 'value' => $stats['bookings_total'], 'icon' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z'],
                        ['label' => 'Pending Payouts', 'value' => $stats['payouts_pending'], 'icon' => 'M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'],
                    ] as $stat)
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--adm-border);">
                            <div style="font-size:.875rem;color:var(--adm-text-muted);">{{ $stat['label'] }}</div>
                            <div style="font-size:1.1rem;font-weight:700;color:var(--adm-navy);">{{ $stat['value'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Status actions --}}
            @if(!$agency->is_platform)
                <div class="adm-card">
                    <div class="adm-card-header">
                        <span class="adm-card-title">Actions</span>
                    </div>
                    <div class="adm-card-body" style="display:flex;flex-direction:column;gap:8px;">
                        @if($agency->status === 'pending')
                            <form method="POST" action="{{ route('admin.agencies.approve', $agency) }}">
                                @csrf
                                <button type="submit" class="adm-btn adm-btn--success" style="width:100%;">
                                    ✓ Approve Agency
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.agencies.reject', $agency) }}">
                                @csrf
                                <button type="submit" class="adm-btn adm-btn--danger" style="width:100%;"
                                        onclick="return confirm('Reject this agency? This cannot be undone.')">
                                    ✗ Reject Application
                                </button>
                            </form>
                        @elseif($agency->status === 'active')
                            <form method="POST" action="{{ route('admin.agencies.suspend', $agency) }}">
                                @csrf
                                <button type="submit" class="adm-btn adm-btn--danger" style="width:100%;"
                                        onclick="return confirm('Suspend this agency? They will lose access immediately.')">
                                    Suspend Agency
                                </button>
                            </form>
                        @elseif($agency->status === 'suspended')
                            <form method="POST" action="{{ route('admin.agencies.reactivate', $agency) }}">
                                @csrf
                                <button type="submit" class="adm-btn adm-btn--success" style="width:100%;">
                                    Reactivate Agency
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

        </div>

    </div>

@endsection
