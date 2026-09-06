@php
    $description = trim((string) ($detail->description ?? ''));
    $summary = mb_strlen($description) > 220 ? mb_substr($description, 0, 220).'...' : $description;
@endphp

@if ($summary !== '')
    <section class="xp-detail-intro" aria-label="{{ __('ui.experience_detail.description_title') }}">
        <p>{{ $summary }}</p>
    </section>
@endif
