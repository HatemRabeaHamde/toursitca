{{-- Interest chips · Filters drawer · sort · count · compact cards · "Show more". Live-filtered in place (Alpine liveFilters). --}}
<section id="results" class="lv-xresults" aria-label="{{ __('experiences.filters.label') }}" :aria-busy="loading.toString()">
    <div class="lv-container">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <p class="lv-t-label-m lv-tone-ink" data-fragment="count" aria-live="polite">@include('site.experiences.marketplace.partials.count')</p>
            <p class="flex items-center gap-2 lv-t-caption lv-tone-muted">
                <x-site.atoms.icon name="shield-success-15" :size="15" />
                <span>{{ __('experiences.trust') }}</span>
                <a href="{{ $page->hostsUrl }}#vetting" class="lv-t-label-xs lv-tone-terracotta hover:underline">{{ __('experiences.trust_link') }}</a>
            </p>
        </div>

        <div class="lv-xresults__grid mt-8" data-fragment="results" :class="{ 'is-loading': loading }" data-reveal="stagger" data-reveal-start="top 90%">
            @include('site.experiences.marketplace.partials.results')
        </div>

        <div class="lv-xmore" data-fragment="more" :class="{ 'is-loading': appending }">
            @include('site.experiences.marketplace.partials.more')
        </div>
    </div>
</section>
