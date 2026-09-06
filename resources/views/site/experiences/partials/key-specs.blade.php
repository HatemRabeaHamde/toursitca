@php
    $durationHours = (float) $experience->duration_hours;
    $durationLabel = trans_choice('ui.units.hours', (int) ceil($durationHours), [
        'count' => rtrim(rtrim(number_format($durationHours, 1), '0'), '.'),
    ]);
    $groupLabel = trans_choice('ui.labels.max_people', $experience->max_group_size, ['count' => $experience->max_group_size]);

    $specs = array_values(array_filter([
        [
            'icon' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
            'label' => __('ui.experience_detail.key_specs.duration_label'),
            'value' => $durationLabel,
        ],
        [
            'icon' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
            'label' => __('ui.experience_detail.key_specs.group_size_label'),
            'value' => $groupLabel,
        ],
        [
            'icon' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="9 16 11 18 15 14"/></svg>',
            'label' => __('ui.experience_detail.key_specs.cancellation_label'),
            'value' => __('ui.experience_detail.key_specs.free_cancellation_short'),
        ],
        [
            'icon' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
            'label' => __('ui.experience_detail.key_specs.languages_label'),
            'value' => __('ui.experience_detail.key_specs.languages_value'),
        ],
        $experience->pickup_enabled ? [
            'icon' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 6v2h-7z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
            'label' => __('ui.experience_detail.key_specs.pickup_label'),
            'value' => __('ui.experience_detail.key_specs.pickup_value'),
        ] : null,
    ]));

    $specCount = count($specs);
@endphp

<div class="xp-key-specs">
    @foreach ($specs as $i => $spec)
        <div class="xp-ks-item">
            <span class="xp-ks-icon" aria-hidden="true">{!! $spec['icon'] !!}</span>
            <span class="xp-ks-text">
                <strong>{{ $spec['label'] }}</strong>
                <em>{{ $spec['value'] }}</em>
            </span>
        </div>
        @if ($i < $specCount - 1)
            <div class="xp-ks-divider" aria-hidden="true"></div>
        @endif
    @endforeach
</div>
