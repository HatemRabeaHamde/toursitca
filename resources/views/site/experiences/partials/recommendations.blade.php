@if ($recommendations->isNotEmpty())
    <section class="rounded-[20px] bg-[var(--ink-06)] px-5 py-7 sm:px-7">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="ed-kicker">{{ __('ui.experience_detail.overview_label') }}</p>
                <h2 class="ed-section-title !mb-0">{{ __('ui.pages.recommendations_title') }}</h2>
            </div>
        </div>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($recommendations as $card)
                @include('site.experiences.partials.card', ['card' => $card])
            @endforeach
        </div>
    </section>
@endif
