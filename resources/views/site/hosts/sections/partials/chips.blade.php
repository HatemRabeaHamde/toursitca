{{-- Region chips + the region the form keeps while searching. Re-rendered on every filter change. --}}
@if($page->filters->region)<input type="hidden" name="region" value="{{ $page->filters->region }}">@endif
<x-site.molecules.filter-chips :chips="$page->regions" :label="__('hosts.filters.regions')" link-attribute="data-filter-link" />
