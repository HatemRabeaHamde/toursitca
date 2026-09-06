@if ($recommendations->isNotEmpty())
    <section class="xp-recs">
        <div class="xp-recs-inner">

            <div class="xp-recs-header">
                <div>
                    <p class="xp-recs-kicker">{{ __('ui.experience_detail.overview_label') }}</p>
                    <h2 class="xp-recs-title">{{ __('ui.pages.recommendations_title') }}</h2>
                </div>
            </div>

            <div class="xp-recs-grid">
                @foreach ($recommendations as $card)
                    @include('site.experiences.partials.card-grid', ['card' => $card])
                @endforeach
            </div>

        </div>
    </section>
@endif
