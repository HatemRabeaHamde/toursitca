<section id="itinerary" class="lv-xsection" aria-labelledby="itinerary-title">
    <h2 id="itinerary-title" class="lv-t-h2 lv-serif lv-tone-ink">{{ __('experiences.detail.v1.day_by_day') }}</h2>
    <ol class="lv-v1-days">
        @foreach($page->itinerary as $day)
            <li @class(['lv-v1-day', 'lv-v1-day--no-thumb' => ! $day['image']])>
                <p class="lv-t-label-m lv-tone-ink">{{ $day['label'] }}</p>
                <span class="lv-v1-day__line" aria-hidden="true"></span>
                @if($day['image'])
                    <img src="{{ $day['image'] }}" alt="" width="128" height="88" loading="lazy" class="lv-v1-day__thumb">
                @endif
                <div class="flex min-w-0 flex-col gap-1.5">
                    <h3 class="lv-t-label-l lv-tone-ink">{{ $day['title'] }}</h3>
                    @if($day['description'])<p class="lv-t-body-s lv-tone-muted">{{ $day['description'] }}</p>@endif
                </div>
                @if($day['duration'])
                    <p class="flex items-center gap-2 lv-t-caption lv-tone-muted"><x-site.atoms.icon name="clock" :size="16" />{{ $day['duration'] }}</p>
                @endif
            </li>
        @endforeach
    </ol>
</section>
