{{--
    Day-by-day itinerary (Experience detail · Itinerary). The first day starts open.
    items: [['label' => 'Day 1', 'title' => …, 'description' => …|null, 'image' => …|null], …]
--}}
@props(['items'])

<ol {{ $attributes->class(['lv-itinerary']) }} x-data="{ open: 0 }">
    @foreach($items as $item)
        @php($hasBody = $item['description'] || $item['image'])
        <li class="lv-itinerary__day" :class="{ 'is-open': open === {{ $loop->index }} }" @class(['is-open' => $loop->first])>
            @if($hasBody)
                <button type="button" class="lv-itinerary__head" id="day-{{ $loop->index }}-head"
                        aria-controls="day-{{ $loop->index }}-body" :aria-expanded="(open === {{ $loop->index }}).toString()" aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                        @click="open = open === {{ $loop->index }} ? null : {{ $loop->index }}">
            @else
                <div class="lv-itinerary__head">
            @endif
                <span class="lv-itinerary__label lv-t-label-s lv-tone-terracotta">{{ $item['label'] }}</span>
                <span class="lv-itinerary__title lv-t-h6 lv-serif lv-tone-ink">{{ $item['title'] }}</span>
                @if($hasBody)<x-site.atoms.icon name="chevron-down-muted-18" :size="18" class="lv-itinerary__chevron" />@endif
            @if($hasBody)</button>@else</div>@endif

            @if($hasBody)
                <div class="lv-itinerary__panel" id="day-{{ $loop->index }}-body" role="region" aria-labelledby="day-{{ $loop->index }}-head">
                    <div class="lv-itinerary__inner">
                        <div class="lv-itinerary__body">
                            @if($item['image'])
                                <img src="{{ $item['image'] }}" alt="" width="120" height="84" loading="lazy" class="lv-itinerary__image">
                            @endif
                            @if($item['description'])<p class="lv-t-body-s lv-tone-muted">{{ $item['description'] }}</p>@endif
                        </div>
                    </div>
                </div>
            @endif
        </li>
    @endforeach
</ol>
