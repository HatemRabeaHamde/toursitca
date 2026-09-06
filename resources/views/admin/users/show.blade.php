@extends('layouts.admin')

@section('page-title', $user->name)
@section('page-subtitle', $user->email . ' · Joined ' . $user->created_at->format('M j, Y'))

@section('topbar-actions')
    <a href="{{ route('admin.users.index') }}" class="adm-btn adm-btn--ghost">← Back</a>
@endsection

@section('content')

    @if(session('success'))
        <div class="adm-flash adm-flash--success" x-data x-init="setTimeout(() => $el.remove(), 4000)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="adm-grid-2-1">

        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Profile --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <span class="adm-card-title">Profile</span>
                    @if($user->status === 'banned')
                        <span class="adm-badge adm-badge--cancelled">Banned</span>
                    @else
                        <span class="adm-badge adm-badge--active">Active</span>
                    @endif
                </div>
                <div class="adm-card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Name</div>
                        <div style="font-size:.9rem;font-weight:600;color:var(--adm-text);">{{ $user->name }}</div>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Email</div>
                        <div style="font-size:.875rem;color:var(--adm-text);">{{ $user->email }}</div>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Roles</div>
                        <div style="display:flex;gap:4px;flex-wrap:wrap;">
                            @foreach($user->getRoleNames() as $r)
                                <span class="adm-badge adm-badge--confirmed" style="font-size:.7rem;">{{ $r }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Language</div>
                        <div style="font-size:.875rem;color:var(--adm-text);">{{ strtoupper($user->preferred_lang ?? 'EN') }}</div>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Joined</div>
                        <div style="font-size:.875rem;color:var(--adm-text);">{{ $user->created_at->format('M j, Y') }}</div>
                    </div>
                    @if($user->agency)
                        <div>
                            <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Agency</div>
                            <a href="{{ route('admin.agencies.show', $user->agency) }}" style="font-size:.875rem;color:var(--adm-navy);font-weight:500;">
                                {{ $user->agency->name }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent bookings --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <span class="adm-card-title">Booking History</span>
                    <span style="font-size:.8rem;color:var(--adm-text-muted);">{{ $user->bookings->count() }} total</span>
                </div>
                @if($user->bookings->isEmpty())
                    <div class="adm-empty">No bookings yet.</div>
                @else
                    <div class="adm-card-body--flush">
                        @foreach($user->bookings->take(10) as $booking)
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 20px;border-bottom:1px solid var(--adm-border);">
                                <div>
                                    <div style="font-size:.8rem;font-weight:600;color:var(--adm-text);">
                                        {{ Str::limit($booking->experience?->getTranslation('title', app()->getLocale(), false), 36) }}
                                    </div>
                                    <div style="font-size:.7rem;color:var(--adm-text-muted);">{{ $booking->created_at->format('M j, Y') }}</div>
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-weight:700;font-size:.875rem;color:var(--adm-navy);">{{ number_format((float)$booking->total_price,0) }} MAD</div>
                                    @php $sc = match($booking->status) {'confirmed'=>'adm-badge--confirmed','completed'=>'adm-badge--completed','cancelled'=>'adm-badge--cancelled',default=>'adm-badge--pending'}; @endphp
                                    <span class="adm-badge {{ $sc }}" style="margin-top:3px;">{{ ucfirst($booking->status) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- Right: actions --}}
        <div class="adm-card" style="align-self:start;">
            <div class="adm-card-header"><span class="adm-card-title">Actions</span></div>
            <div class="adm-card-body" style="display:flex;flex-direction:column;gap:8px;">
                @if($user->status === 'banned')
                    <form method="POST" action="{{ route('admin.users.restore', $user) }}">
                        @csrf
                        <button class="adm-btn adm-btn--success" style="width:100%;">Restore User</button>
                    </form>
                @elseif(!$user->getRoleNames()->contains('admin'))
                    <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                        @csrf
                        <button class="adm-btn adm-btn--danger" style="width:100%;"
                                onclick="return confirm('Ban {{ $user->name }}? They will lose access immediately.')">
                            Ban User
                        </button>
                    </form>
                @else
                    <p style="font-size:.8rem;color:var(--adm-text-muted);text-align:center;">Admin accounts cannot be banned.</p>
                @endif
            </div>
        </div>

    </div>

@endsection
