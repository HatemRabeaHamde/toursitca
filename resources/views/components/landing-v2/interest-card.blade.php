@props(['interest'])

<a href="{{ $interest['url'] }}" class="lv-tile lv-tile--interest flex aspect-[185/335] items-end justify-center p-4 pb-7 text-center">
    <img src="{{ $interest['image_url'] }}" alt="" loading="lazy" width="185" height="335">
    <span class="flex flex-col gap-[3px]">
        <span class="lv-interest-name">{{ $interest['name'] }}</span>
        <span class="lv-interest-count">{{ trans_choice('landing_v2.discover.count', $interest['experiences_count'], ['count' => $interest['experiences_count']]) }}</span>
    </span>
</a>
