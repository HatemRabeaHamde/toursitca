<section id="description" class="ed-reference-card">
    <h2>{{ __('ui.experience_detail.the_experience_title') }}</h2>
    <p>{{ $detail->description }}</p>
</section>

<section id="overview" class="ed-reference-card">
    <h2>{{ __('ui.experience_detail.what_you_will_do_title') }}</h2>
    <div class="ed-step-list">
        @foreach ($detail->trustItems as $item)
            <div class="ed-step">
                <span>{{ $loop->iteration }}</span>
                <div>
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['body'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

@if ($experience->inclusions || $experience->exclusions)
    <section id="included" class="ed-inclusion-row">
        @include('site.experiences.partials.inclusions', ['experience' => $experience])
    </section>
@endif
