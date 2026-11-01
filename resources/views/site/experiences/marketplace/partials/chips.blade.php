{{-- Interest chips + the current interest/host the form keeps while searching. Re-rendered on every filter change. --}}
@foreach($page->filters->categories as $category)
    <input type="hidden" name="category[]" value="{{ $category }}" form="xp-filters">
@endforeach
@if($page->filters->host)<input type="hidden" name="host" value="{{ $page->filters->host }}" form="xp-filters">@endif
<x-site.molecules.filter-chips :chips="$page->chips" :label="__('experiences.filters.interests')" link-attribute="data-filter-link" />
