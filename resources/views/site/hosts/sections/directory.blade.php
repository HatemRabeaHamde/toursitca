{{--
    Filters (region chips · search · specialty · sort) + host grid.
    Works as a plain GET form; with JS, Alpine `hostDirectory` swaps only the
    chips, count and results in place (no reload, no scroll jump).
--}}
<section id="directory" class="lv-directory" aria-label="{{ __('hosts.filters.label') }}"
         x-data="hostDirectory" @click="navigate($event)" :aria-busy="loading.toString()">
    <div class="lv-container">
        <form method="GET" action="{{ $page->indexUrl }}#directory" role="search" @submit.prevent="submit($el)"
              class="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-center lg:justify-between">
            <div data-fragment="chips">@include('site.hosts.sections.partials.chips')</div>

            <div class="flex flex-wrap items-center gap-2.5">
                <x-site.molecules.search-field name="q" :value="$page->filters->search"
                    :placeholder="__('hosts.filters.search')" :label="__('hosts.filters.search_label')" class="w-full sm:w-[240px]" />
                <x-site.molecules.select-menu name="specialty" :options="$page->specialties" :selected="$page->filters->specialty"
                    :label="__('hosts.filters.specialty')" :placeholder="__('hosts.filters.all_specialties')" />
                <noscript><button type="submit" class="lv-filter-chip lv-t-ui-xs">{{ __('hosts.filters.apply') }}</button></noscript>
            </div>

            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-between lg:order-last">
                <p class="lv-t-label-s lv-tone-ink" data-fragment="count" aria-live="polite">@include('site.hosts.sections.partials.count')</p>
                <p class="flex items-center gap-1.5 lv-t-caption lv-tone-muted">
                    <x-site.atoms.icon name="shield-success-12" :size="12" class="lv-icon--ink" />
                    <span>{{ __('hosts.vetted') }}</span>
                    <a href="#vetting" class="lv-t-label-xs lv-tone-terracotta hover:underline">{{ __('hosts.vetted_link') }}</a>
                </p>
                <x-site.molecules.select-menu variant="plain" name="sort" :options="$page->sorts()" :selected="$page->filters->sort" :label="__('hosts.sort.label')" />
            </div>
        </form>

        <div class="lv-directory__results mt-6" data-fragment="results" :class="{ 'is-loading': loading }" data-reveal="stagger" data-reveal-start="top 90%">
            @include('site.hosts.sections.partials.results')
        </div>
    </div>
</section>
