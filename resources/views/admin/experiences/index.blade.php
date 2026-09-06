@extends('layouts.admin')

@section('page-title', __('ui.nav.experiences'))
@section('page-subtitle', 'All experiences across every agency')

@section('topbar-actions')
    <a href="{{ route('admin.experiences.create') }}" class="adm-btn adm-btn--primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        {{ __('ui.nav.create_experience') }}
    </a>
@endsection

@section('content')
    <div class="adm-card">
        @if($experiences->isEmpty())
            <div class="adm-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <p>No experiences yet.</p>
            </div>
        @else
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Experience</th>
                            <th>Agency</th>
                            <th>City</th>
                            <th>Category</th>
                            <th>Group</th>
                            <th>Private</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($experiences as $experience)
                            @php
                                $sc = match($experience->status) {
                                    'published'   => 'adm-badge--published',
                                    'unpublished' => 'adm-badge--cancelled',
                                    default       => 'adm-badge--draft',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-size:.875rem;color:var(--adm-text);max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $experience->getTranslation('title', app()->getLocale(), false) }}
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.agencies.show', $experience->agency) }}"
                                       style="font-size:.8rem;color:var(--adm-navy);font-weight:500;">
                                        {{ $experience->agency->name }}
                                    </a>
                                </td>
                                <td style="font-size:.8rem;color:var(--adm-text-muted);">{{ $experience->location_city }}</td>
                                <td style="font-size:.8rem;color:var(--adm-text-muted);">{{ ucfirst($experience->category ?? '—') }}</td>
                                <td style="font-size:.875rem;font-weight:600;color:var(--adm-navy);white-space:nowrap;">
                                    {{ number_format((float)$experience->price_per_person, 0) }} MAD
                                </td>
                                <td style="font-size:.8rem;color:var(--adm-text-muted);">
                                    {{ $experience->private_price ? number_format((float)$experience->private_price, 0).' MAD' : '—' }}
                                </td>
                                <td><span class="adm-badge {{ $sc }}">{{ __('ui.status.'.$experience->status) }}</span></td>
                                <td>
                                    <div style="display:flex;gap:5px;justify-content:flex-end;">
                                        <a href="{{ route('admin.experiences.edit', $experience) }}" class="adm-btn adm-btn--ghost adm-btn--sm">Edit</a>
                                        <a href="{{ route('admin.experiences.options.index', $experience) }}" class="adm-btn adm-btn--ghost adm-btn--sm">Options</a>
                                        @if($experience->status === 'published')
                                            <form method="POST" action="{{ route('admin.experiences.unpublish', $experience) }}">
                                                @csrf
                                                <button class="adm-btn adm-btn--danger adm-btn--sm">Unpublish</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.experiences.publish', $experience) }}">
                                                @csrf
                                                <button class="adm-btn adm-btn--success adm-btn--sm">Publish</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.experiences.destroy', $experience) }}" onsubmit="return confirm('{{ __('ui.messages.confirm_delete_experience') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="adm-btn adm-btn--danger adm-btn--sm">{{ __('ui.nav.delete') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($experiences->hasPages())
                <div style="padding:16px 20px;border-top:1px solid var(--adm-border);">
                    {{ $experiences->links() }}
                </div>
            @endif
        @endif
    </div>

@endsection
