{{-- Row of filter chips that are plain links (work without JS). chips: [['label', 'href', 'active'], …]
     linkAttribute: optional data attribute a parent script uses to take over the click. --}}
@props(['chips', 'label', 'linkAttribute' => null])

<nav {{ $attributes->class(['flex flex-wrap gap-2']) }} aria-label="{{ $label }}">
    @foreach($chips as $chip)
        <a href="{{ $chip['href'] }}" @class(['lv-filter-chip lv-t-ui-xs', 'is-active' => $chip['active']]) @if($chip['active']) aria-current="true" @endif @if($linkAttribute) {{ $linkAttribute }} @endif>{{ $chip['label'] }}</a>
    @endforeach
</nav>
