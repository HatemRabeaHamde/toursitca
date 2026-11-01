@extends('layouts.admin')

@section('title', __('hosts_admin.admin.title'))
@section('page-title', __('hosts_admin.admin.title'))
@section('page-subtitle', __('hosts_admin.admin.subtitle'))

@section('topbar-actions')
    <a href="{{ route('admin.hosts.create') }}" class="adm-btn adm-btn--primary">{{ __('hosts_admin.admin.add') }}</a>
@endsection

@section('content')
    @if(session('success'))<div class="adm-flash adm-flash--success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="adm-flash adm-flash--error" role="alert">{{ session('error') }}</div>@endif

    <div class="adm-tabs">
        @foreach ([null => 'all', 'review' => 'review', 'live' => 'live', 'hidden' => 'hidden'] as $key => $label)
            <a href="{{ route('admin.hosts.index', array_filter(['status' => $key])) }}" class="adm-tab {{ $status === $key ? 'adm-tab--active' : '' }}">{{ __('hosts_admin.admin.tabs.'.$label) }}</a>
        @endforeach
    </div>

    <div class="adm-card">
        @if($hosts->isEmpty())
            <div class="adm-empty"><p>{{ __('hosts_admin.admin.empty') }}</p></div>
        @else
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>{{ __('hosts_admin.form.name') }}</th>
                            <th>{{ __('hosts_admin.form.agency') }}</th>
                            <th>{{ __('hosts_admin.form.licence') }}</th>
                            <th>{{ __('hosts_admin.status.label') }}</th>
                            <th>{{ __('hosts_admin.agency.experiences') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hosts as $host)
                            <tr class="{{ $host->reviewed_at === null ? 'adm-row--warn' : '' }}">
                                <td>
                                    <div style="font-weight:600;font-size:.875rem;">{{ $host->name }}</div>
                                    <div style="font-size:.75rem;color:var(--adm-text-muted);">{{ collect([$host->getTranslation('role', 'en', true), $host->city])->filter()->implode(' · ') }}</div>
                                </td>
                                <td style="font-size:.875rem;">{{ $host->agency?->name ?? '—' }}@if($host->agency?->is_platform) <span class="adm-badge adm-badge--confirmed" style="font-size:.65rem;">Platform</span>@endif</td>
                                <td style="font-size:.875rem;">{{ $host->licence_number ?: '—' }}</td>
                                <td>
                                    @if($host->reviewed_at === null)
                                        <span class="adm-badge adm-badge--pending">{{ __('hosts_admin.status.review') }}</span>
                                    @elseif($host->is_active)
                                        <span class="adm-badge adm-badge--active">{{ $host->is_verified ? __('hosts_admin.status.live_verified') : __('hosts_admin.status.live') }}</span>
                                    @else
                                        <span class="adm-badge adm-badge--cancelled">{{ __('hosts_admin.status.hidden') }}</span>
                                    @endif
                                </td>
                                <td style="text-align:center;font-size:.875rem;">{{ $host->experiences_count }}</td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:6px;justify-content:flex-end;">
                                        <a href="{{ route('admin.hosts.edit', $host) }}" class="adm-btn adm-btn--ghost adm-btn--sm">{{ __('hosts_admin.agency.edit') }}</a>
                                        @if($host->reviewed_at === null && filled($host->licence_number))
                                            <form method="POST" action="{{ route('admin.hosts.approve', $host) }}">@csrf<button type="submit" class="adm-btn adm-btn--success adm-btn--sm">{{ __('hosts_admin.admin.approve') }}</button></form>
                                        @endif
                                        @if($host->experiences_count === 0)
                                            <form method="POST" action="{{ route('admin.hosts.destroy', $host) }}" onsubmit="return confirm(@js(__('hosts_admin.agency.confirm_delete')))">@csrf @method('DELETE')<button type="submit" class="adm-btn adm-btn--danger adm-btn--sm">{{ __('hosts_admin.agency.delete') }}</button></form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding:12px 16px;">{{ $hosts->links() }}</div>
        @endif
    </div>
@endsection
