@if ($experience->inclusions || $experience->exclusions)
    <section id="included" class="xp-detail-section">
        <h2 class="xp-section-heading">{{ __('ui.experience_detail.included_title') }}</h2>
        <div class="xp-inclusions-wrap">
            @include('site.experiences.partials.inclusions', ['experience' => $experience])
        </div>
    </section>
@endif
