{{-- Interest chips · experience / host / rating selects · with photos · sort · masonry cards · "Show more". Live-filtered in place. --}}
<section id="reviews" class="lv-rdirectory" aria-label="{{ __('reviews.filters.label') }}" x-data="reviewDirectory" @click="navigate($event)" :aria-busy="loading.toString()">
    <div class="lv-container">
        <form id="rv-filters" method="GET" action="{{ $page->indexUrl }}#reviews" role="search" @submit.prevent="submit($el)"
              class="flex flex-col gap-4">
            <div data-fragment="chips">@include('site.reviews.partials.chips')</div>

            <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2.5">
                <x-site.molecules.select-menu name="experience" :options="$page->experiences" :selected="$page->filters->experience"
                    :label="__('reviews.filters.experience')" :placeholder="__('reviews.filters.all_experiences')" />
                <x-site.molecules.select-menu name="host" :options="$page->hosts" :selected="$page->filters->host"
                    :label="__('reviews.filters.host')" :placeholder="__('reviews.filters.all_hosts')" />
                <x-site.molecules.select-menu name="rating" :options="$page->ratings()" :selected="$page->filters->rating ? (string) $page->filters->rating : null"
                    :label="__('reviews.filters.rating')" :placeholder="__('reviews.filters.any_rating')" />
                <label class="lv-check lv-t-ui-s">
                    <input type="checkbox" name="photos" value="1" @checked($page->filters->withPhotos) @change="$el.form.requestSubmit()">
                    <span>{{ __('reviews.filters.photos') }}</span>
                </label>
                <noscript><button type="submit" class="lv-filter-chip lv-t-ui-xs">{{ __('reviews.filters.apply') }}</button></noscript>
            </div>
            <x-site.molecules.select-menu variant="plain" name="sort" :options="$page->sorts()" :selected="$page->filters->sort" :label="__('reviews.sort.label')" />
            </div>

            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="flex items-center gap-2 lv-t-caption lv-tone-muted">
                    <x-site.atoms.icon name="shield-success-15" :size="15" />
                    <span>{{ __('reviews.verified_only') }}</span>
                    <span aria-hidden="true">·</span>
                    <span class="lv-tone-ink" data-fragment="count" aria-live="polite">@include('site.reviews.partials.count')</span>
                </p>
            </div>
        </form>

        <div class="lv-rdirectory__results mt-7" data-fragment="results" :class="{ 'is-loading': loading }" data-reveal="stagger" data-reveal-start="top 90%">
            @include('site.reviews.partials.results')
        </div>

        <div class="lv-xmore" data-fragment="more" :class="{ 'is-loading': appending }">
            @include('site.reviews.partials.more')
        </div>
    </div>
</section>
