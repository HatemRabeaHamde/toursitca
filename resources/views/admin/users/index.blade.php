@extends('layouts.admin')

@section('page-title', __('ui.nav.users'))
@section('page-subtitle', 'Manage platform users')

@section('content')

    @if(session('success'))
        <div class="adm-flash adm-flash--success" x-data x-init="setTimeout(() => $el.remove(), 4000)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:2;min-width:200px;">
            <label class="adm-label">Search</label>
            <input type="text" name="search" class="adm-input" value="{{ $search }}" placeholder="Name or email…">
        </div>
        <div style="flex:1;min-width:140px;">
            <label class="adm-label">Role</label>
            <select name="role" class="adm-select">
                <option value="">All roles</option>
                <option value="admin" @selected($role==='admin')>Admin</option>
                <option value="travel_agency" @selected($role==='travel_agency')>Agency</option>
                <option value="user" @selected($role==='user')>Tourist</option>
            </select>
        </div>
        <button type="submit" class="adm-btn adm-btn--primary">Search</button>
        @if($search || $role)
            <a href="{{ route('admin.users.index') }}" class="adm-btn adm-btn--ghost">Clear</a>
        @endif
    </form>

    <div class="adm-card">
        @if($users->isEmpty())
            <div class="adm-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <p>No users found.</p>
            </div>
        @else
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Bookings</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i => $user)
                            @php
                                $roles = $user->getRoleNames();
                                $isBanned = $user->status === 'banned';
                            @endphp
                            <tr class="{{ $isBanned ? 'adm-row--warn' : '' }}">
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div class="adm-avatar adm-avatar--{{ $i % 6 }}" style="width:34px;height:34px;font-size:.75rem;flex-shrink:0;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600;font-size:.875rem;color:var(--adm-text);">{{ $user->name }}</div>
                                            <div style="font-size:.75rem;color:var(--adm-text-muted);">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @foreach($roles as $r)
                                        <span class="adm-badge adm-badge--{{ $r === 'admin' ? 'confirmed' : ($r === 'travel_agency' ? 'published' : 'pending') }}" style="font-size:.65rem;">
                                            {{ $r === 'travel_agency' ? 'Agency' : ucfirst($r) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td style="text-align:center;font-weight:600;color:var(--adm-navy);">{{ $user->bookings_count }}</td>
                                <td>
                                    @if($isBanned)
                                        <span class="adm-badge adm-badge--cancelled">Banned</span>
                                    @else
                                        <span class="adm-badge adm-badge--active">Active</span>
                                    @endif
                                </td>
                                <td style="font-size:.8rem;color:var(--adm-text-muted);">{{ $user->created_at->format('M j, Y') }}</td>
                                <td>
                                    <div style="display:flex;gap:5px;justify-content:flex-end;">
                                        <a href="{{ route('admin.users.show', $user) }}" class="adm-btn adm-btn--ghost adm-btn--sm">View</a>
                                        @if($isBanned)
                                            <form method="POST" action="{{ route('admin.users.restore', $user) }}">
                                                @csrf
                                                <button class="adm-btn adm-btn--success adm-btn--sm">Restore</button>
                                            </form>
                                        @elseif(!$roles->contains('admin'))
                                            <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                                                @csrf
                                                <button class="adm-btn adm-btn--danger adm-btn--sm"
                                                        onclick="return confirm('Ban {{ $user->name }}?')">Ban</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div style="padding:16px 20px;border-top:1px solid var(--adm-border);">
                    {{ $users->appends(['search'=>$search,'role'=>$role])->links() }}
                </div>
            @endif
        @endif
    </div>

@endsection
