@extends('layouts.admin')

@section('page-title', __('ui.nav.newsletter'))
@section('page-subtitle', 'Trip inquiries and newsletter signups from the site')

@section('content')

    {{-- Tabs --}}
    <div class="adm-tabs">
        <a href="{{ route('admin.newsletter.index') }}" class="adm-tab {{ !$filter ? 'adm-tab--active' : '' }}">All</a>
        <a href="{{ route('admin.newsletter.index', ['filter'=>'inquiries']) }}" class="adm-tab {{ $filter==='inquiries' ? 'adm-tab--active' : '' }}">Inquiries</a>
        <a href="{{ route('admin.newsletter.index', ['filter'=>'unread']) }}" class="adm-tab {{ $filter==='unread' ? 'adm-tab--active' : '' }}">Unread</a>
    </div>

    <div class="adm-card">
        @if($subscribers->isEmpty())
            <div class="adm-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 6l-10 7L2 6"/></svg>
                <p>No signups yet.</p>
            </div>
        @else
            <div class="adm-card-body--flush">
                @foreach($subscribers as $subscriber)
                    <div style="padding:20px;border-bottom:1px solid var(--adm-border);display:flex;gap:16px;{{ $subscriber->message && ! $subscriber->read_at ? 'background:var(--adm-navy-tint,#F7F5F2);' : '' }}">
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                                <span style="font-weight:600;font-size:.875rem;color:var(--adm-text);">
                                    {{ $subscriber->email }}
                                </span>
                                @if($subscriber->message)
                                    <span class="adm-badge adm-badge--pending" style="font-size:.65rem;">Inquiry</span>
                                @endif
                                @if($subscriber->message && ! $subscriber->read_at)
                                    <span class="adm-badge adm-badge--cancelled" style="font-size:.65rem;">Unread</span>
                                @endif
                                <span style="font-size:.7rem;color:var(--adm-text-muted);">
                                    · {{ strtoupper($subscriber->locale ?? '—') }}
                                    · {{ $subscriber->source }}
                                    · {{ $subscriber->subscribed_at->format('M j, Y H:i') }}
                                </span>
                            </div>
                            @if($subscriber->message)
                                <p style="font-size:.875rem;color:var(--adm-text-muted);line-height:1.5;margin:0;">
                                    {{ $subscriber->message }}
                                </p>
                            @endif
                        </div>
                        <div style="flex-shrink:0;display:flex;flex-direction:column;gap:8px;align-items:flex-end;">
                            <a href="mailto:{{ $subscriber->email }}" class="adm-btn adm-btn--ghost adm-btn--sm" style="white-space:nowrap;">Reply</a>
                            @if($subscriber->message && ! $subscriber->read_at)
                                <form method="POST" action="{{ route('admin.newsletter.mark-read', $subscriber) }}">
                                    @csrf
                                    <button class="adm-btn adm-btn--ghost adm-btn--sm" style="white-space:nowrap;">Mark read</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if($subscribers->hasPages())
                <div style="padding:16px 20px;border-top:1px solid var(--adm-border);">
                    {{ $subscribers->links() }}
                </div>
            @endif
        @endif
    </div>

@endsection
