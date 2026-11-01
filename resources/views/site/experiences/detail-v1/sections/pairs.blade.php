{{-- "Pairs well with": the two closest recommendations. Links to each trip: there is no trip builder yet. --}}
<section class="lv-v1-pairs" aria-labelledby="pairs-title">
    <h2 id="pairs-title" class="lv-t-h4 lv-serif lv-tone-ink">{{ __('experiences.detail.v1.pairs.title') }}</h2>
    <ul class="grid grid-cols-2 gap-3">
        @foreach(array_slice($page->related, 0, 2) as $card)
            <li class="lv-v1-pair">
                <img src="{{ $card['image'] }}" alt="" width="157" height="96" loading="lazy">
                <div class="flex flex-1 flex-col gap-1.5 p-3">
                    <p class="lv-t-label-m lv-tone-ink">{{ $card['title'] }}</p>
                    <p class="lv-t-micro lv-tone-muted">{{ __('experiences.detail.v1.pairs.meta', ['meta' => $card['meta'], 'price' => $card['price']]) }}</p>
                    <a href="{{ $card['href'] }}" class="lv-v1-pair__link lv-t-label-s lv-tone-terracotta">{{ __('experiences.detail.v1.pairs.view') }}</a>
                </div>
            </li>
        @endforeach
    </ul>
    <hr class="lv-bcard__rule">
    <div class="flex flex-col gap-1">
        <p class="lv-t-caption lv-tone-muted">{{ __('experiences.detail.v1.pairs.different') }}</p>
        <x-site.atoms.link-arrow :href="route('site.experiences.index', ['locale' => app()->getLocale()])" arrow="terracotta">{{ __('experiences.detail.v1.pairs.explore') }}</x-site.atoms.link-arrow>
    </div>
</section>
