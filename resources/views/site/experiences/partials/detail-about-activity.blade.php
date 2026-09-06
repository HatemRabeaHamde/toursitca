@php
    $activityItems = collect([
        [
            'icon' => 'manual',
            'title' => __('ui.experience_detail.trust.manual_confirmation_title'),
            'body' => __('ui.experience_detail.trust.manual_confirmation_body'),
        ],
    ])->merge($detail->trustItems)->values();
@endphp

@if ($activityItems->isNotEmpty())
    <section id="activity-info" class="xp-detail-section xp-detail-section--flush">
        <h2 class="xp-section-heading">{{ __('ui.experience_detail.activity_info_title') }}</h2>

        <div class="xp-activity-facts">
            @foreach ($activityItems as $item)
                <article class="xp-activity-fact">
                    <span class="xp-activity-icon" aria-hidden="true">
                        @switch($item['icon'] ?? '01')
                            @case('pay_later')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M7 15h3"/></svg>
                                @break
                            @case('02')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                @break
                            @case('03')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="14" height="10" rx="2"/><path d="M16 9h3l3 4v2h-6z"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></svg>
                                @break
                            @case('04')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.8"/></svg>
                                @break
                            @default
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="m9 16 2 2 4-4"/></svg>
                        @endswitch
                    </span>
                    <span>
                        <strong>{{ $item['title'] }}</strong>
                        <em>{{ $item['body'] }}</em>
                    </span>
                </article>
            @endforeach
        </div>
    </section>
@endif
