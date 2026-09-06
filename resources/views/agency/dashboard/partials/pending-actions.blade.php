<div class="ag-card" style="animation: agFadeUp .4s .2s both;">

    <div class="ag-card-header">
        <span class="ag-card-title">{{ __('ui.agency_dashboard.pending_actions') }}</span>
        @if ($draftExperiences->isNotEmpty() || $upcomingIn48h->isNotEmpty())
            <span class="ag-badge ag-badge--pending" style="font-size:10px;padding:2px 8px;">
                {{ $draftExperiences->count() + $upcomingIn48h->count() }}
            </span>
        @endif
    </div>

    @if ($draftExperiences->isEmpty() && $upcomingIn48h->isEmpty())
        <div class="ag-empty">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ __('ui.agency_dashboard.no_pending_actions') }}
        </div>
    @else
        <ul class="ag-action-list ag-card-body--flush">

            @foreach ($draftExperiences as $exp)
                <li class="ag-action-item">
                    <div class="ag-action-icon ag-action-icon--amber">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="ag-action-text">{{ __('ui.agency_dashboard.draft_needs_publish') }}</div>
                        <div class="ag-action-sub">{{ $exp->getTranslation('title', app()->getLocale(), false) }}</div>
                    </div>
                    <a href="{{ route('agency.experiences.edit', $exp) }}" class="ag-action-cta">
                        {{ __('ui.agency_dashboard.edit') }}
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </li>
            @endforeach

            @foreach ($upcomingIn48h as $slot)
                <li class="ag-action-item">
                    <div class="ag-action-icon ag-action-icon--sage">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="ag-action-text">{{ __('ui.agency_dashboard.experience_in_48h') }}</div>
                        <div class="ag-action-sub">
                            {{ $slot->experience->getTranslation('title', app()->getLocale(), false) }}
                            · {{ $slot->date->format('M j') }} {{ substr($slot->time_slot, 0, 5) }}
                        </div>
                    </div>
                    <a href="{{ route('agency.availability.index') }}" class="ag-action-cta ag-action-cta--sage">
                        {{ __('ui.agency_dashboard.view') }}
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </li>
            @endforeach

        </ul>
    @endif

</div>
