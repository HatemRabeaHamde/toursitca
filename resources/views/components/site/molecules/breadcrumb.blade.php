{{-- Home › Discover Morocco › … (last item = current page). Items: [['label' => …, 'href' => …|null], …] --}}
@props(['items'])

<nav {{ $attributes->class(['lv-breadcrumb lv-t-caption']) }} aria-label="{{ __('site.breadcrumb') }}">
    <ol class="flex flex-wrap items-center gap-x-2">
        @foreach($items as $item)
            <li class="flex items-center gap-2">
                @if(! $loop->first)<span aria-hidden="true">›</span>@endif
                @if($item['href'] ?? null)
                    <a href="{{ $item['href'] }}" class="hover:underline">{{ $item['label'] }}</a>
                @else
                    <span aria-current="page">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
