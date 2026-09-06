@extends('layouts.admin')

@section('page-title', __('ui.nav.reviews'))
@section('page-subtitle', 'Moderate customer reviews')

@section('content')

    @if(session('success'))
        <div class="adm-flash adm-flash--success" x-data x-init="setTimeout(() => $el.remove(), 4000)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="adm-tabs">
        <a href="{{ route('admin.reviews.index') }}" class="adm-tab {{ !$visibility ? 'adm-tab--active' : '' }}">All</a>
        <a href="{{ route('admin.reviews.index', ['visibility'=>'visible']) }}" class="adm-tab {{ $visibility==='visible' ? 'adm-tab--active' : '' }}">Visible</a>
        <a href="{{ route('admin.reviews.index', ['visibility'=>'hidden']) }}" class="adm-tab {{ $visibility==='hidden' ? 'adm-tab--active' : '' }}">Hidden</a>
    </div>

    <div class="adm-card">
        @if($reviews->isEmpty())
            <div class="adm-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <p>No reviews yet.</p>
            </div>
        @else
            <div class="adm-card-body--flush">
                @foreach($reviews as $review)
                    <div style="padding:20px;border-bottom:1px solid var(--adm-border);display:flex;gap:16px;{{ !$review->is_visible ? 'opacity:.55;' : '' }}">
                        {{-- Stars --}}
                        <div style="flex-shrink:0;text-align:center;min-width:52px;">
                            <div style="font-size:1.3rem;font-weight:800;color:var(--gold);">{{ $review->rating }}</div>
                            <div style="font-size:.65rem;color:var(--adm-text-muted);">/ 5</div>
                            <div style="margin-top:3px;">
                                @for($i = 1; $i <= 5; $i++)
                                    <span style="color:{{ $i <= $review->rating ? 'var(--gold)' : 'var(--ink-15)' }};font-size:.6rem;">★</span>
                                @endfor
                            </div>
                        </div>
                        {{-- Content --}}
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                                <span style="font-weight:600;font-size:.875rem;color:var(--adm-text);">
                                    {{ $review->user?->name ?? 'Anonymous' }}
                                </span>
                                <span style="color:var(--adm-text-muted);font-size:.75rem;">on</span>
                                <span style="font-size:.8rem;color:var(--adm-navy);font-weight:500;">
                                    {{ Str::limit($review->experience?->getTranslation('title', app()->getLocale(), false), 36) }}
                                </span>
                                <span style="font-size:.7rem;color:var(--adm-text-muted);">· {{ $review->created_at->format('M j, Y') }}</span>
                                @if(!$review->is_visible)
                                    <span class="adm-badge adm-badge--cancelled" style="font-size:.65rem;">Hidden</span>
                                @endif
                            </div>
                            <p style="font-size:.875rem;color:var(--adm-text-muted);line-height:1.5;margin:0;">
                                {{ $review->body }}
                            </p>
                        </div>
                        {{-- Toggle --}}
                        <div style="flex-shrink:0;">
                            <form method="POST" action="{{ route('admin.reviews.toggle-visibility', $review) }}">
                                @csrf
                                <button class="adm-btn adm-btn--ghost adm-btn--sm" style="white-space:nowrap;">
                                    {{ $review->is_visible ? 'Hide' : 'Show' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($reviews->hasPages())
                <div style="padding:16px 20px;border-top:1px solid var(--adm-border);">
                    {{ $reviews->links() }}
                </div>
            @endif
        @endif
    </div>

@endsection
