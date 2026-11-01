@if($page->filters->interest)<input type="hidden" name="interest" value="{{ $page->filters->interest }}" form="rv-filters">@endif
<x-site.molecules.filter-chips :chips="$page->chips" :label="__('reviews.filters.interests')" link-attribute="data-filter-link" />
