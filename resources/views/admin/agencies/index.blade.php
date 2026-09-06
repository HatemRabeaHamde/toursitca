@extends('layouts.admin')

@section('page-title', __('ui.nav.agencies'))
@section('page-subtitle', 'Manage all travel agencies on the platform')

@section('topbar-actions')
    <a href="{{ route('admin.agencies.create') }}" class="adm-btn adm-btn--primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="19" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        {{ __('ui.nav.create_platform_agency') }}
    </a>
@endsection

@section('content')

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="adm-flash adm-flash--success" x-data x-init="setTimeout(() => $el.remove(), 4000)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Status filter tabs --}}
    <div class="adm-tabs">
        <a href="{{ route('admin.agencies.index') }}"
           class="adm-tab {{ !$status ? 'adm-tab--active' : '' }}">
            All
        </a>
        <a href="{{ route('admin.agencies.index', ['status' => 'pending']) }}"
           class="adm-tab {{ $status === 'pending' ? 'adm-tab--active' : '' }}">
            Pending
        </a>
        <a href="{{ route('admin.agencies.index', ['status' => 'active']) }}"
           class="adm-tab {{ $status === 'active' ? 'adm-tab--active' : '' }}">
            Active
        </a>
        <a href="{{ route('admin.agencies.index', ['status' => 'suspended']) }}"
           class="adm-tab {{ $status === 'suspended' ? 'adm-tab--active' : '' }}">
            Suspended
        </a>
    </div>

    <div class="adm-card">
        @if($agencies->isEmpty())
            <div class="adm-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                <p>No agencies found{{ $status ? ' with status "'.$status.'"' : '' }}.</p>
            </div>
        @else
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Agency</th>
                            <th>Owner</th>
                            <th>City</th>
                            <th>Commission</th>
                            <th>Experiences</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agencies as $agency)
                            @php
                                $statusClass = match($agency->status) {
                                    'active'    => 'adm-badge--active',
                                    'pending'   => 'adm-badge--pending',
                                    'suspended' => 'adm-badge--cancelled',
                                    'rejected'  => 'adm-badge--cancelled',
                                    default     => 'adm-badge--pending',
                                };
                                $isAged = $agency->status === 'pending' && $agency->created_at->diffInDays(now()) >= 3;
                            @endphp
                            <tr class="{{ $isAged ? 'adm-row--warn' : '' }}">
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div class="adm-agency-initials {{ $isAged ? 'adm-agency-initials--aged' : '' }}" style="flex-shrink:0;">
                                            {{ strtoupper(substr($agency->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600;color:var(--adm-text);font-size:.875rem;">
                                                {{ $agency->name }}
                                            </div>
                                            @if($agency->is_platform)
                                                <span class="adm-badge adm-badge--confirmed" style="margin-top:3px;font-size:.65rem;">Platform</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:.875rem;color:var(--adm-text);">{{ $agency->user->name }}</div>
                                    <div style="font-size:.75rem;color:var(--adm-text-muted);">{{ $agency->user->email }}</div>
                                </td>
                                <td style="color:var(--adm-text-muted);font-size:.875rem;">{{ $agency->city }}</td>
                                <td style="font-size:.875rem;color:var(--adm-text);">{{ $agency->commission_rate }}%</td>
                                <td style="font-size:.875rem;text-align:center;">{{ $agency->experiences_count }}</td>
                                <td><span class="adm-badge {{ $statusClass }}">{{ __('ui.status.'.$agency->status) }}</span></td>
                                <td style="font-size:.75rem;color:var(--adm-text-muted);">{{ $agency->created_at->format('M j, Y') }}</td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:6px;justify-content:flex-end;">
                                        <a href="{{ route('admin.agencies.show', $agency) }}" class="adm-btn adm-btn--ghost adm-btn--sm">
                                            View
                                        </a>
                                        @if($agency->status === 'pending')
                                            <form method="POST" action="{{ route('admin.agencies.approve', $agency) }}">
                                                @csrf
                                                <button type="submit" class="adm-btn adm-btn--success adm-btn--sm">{{ __('ui.nav.approve') }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.agencies.reject', $agency) }}">
                                                @csrf
                                                <button type="submit" class="adm-btn adm-btn--danger adm-btn--sm"
                                                        onclick="return confirm('Reject this agency?')">Reject</button>
                                            </form>
                                        @elseif($agency->status === 'active')
                                            <form method="POST" action="{{ route('admin.agencies.suspend', $agency) }}">
                                                @csrf
                                                <button type="submit" class="adm-btn adm-btn--danger adm-btn--sm"
                                                        onclick="return confirm('Suspend this agency?')">Suspend</button>
                                            </form>
                                        @elseif($agency->status === 'suspended')
                                            <form method="POST" action="{{ route('admin.agencies.reactivate', $agency) }}">
                                                @csrf
                                                <button type="submit" class="adm-btn adm-btn--success adm-btn--sm">Reactivate</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($agencies->hasPages())
                <div style="padding:16px 20px;border-top:1px solid var(--adm-border);">
                    {{ $agencies->appends(['status' => $status])->links() }}
                </div>
            @endif
        @endif
    </div>

@endsection
