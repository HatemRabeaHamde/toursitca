{{-- Overall score · star breakdown · detail ratings (all computed from visible reviews). --}}
<section class="lv-container lv-rsummary" aria-label="{{ __('reviews.summary.breakdown') }}" data-reveal="stagger">
    <div class="lv-rsummary__card lv-rsummary__overall">
        <p class="lv-rsummary__score">{{ $page->summary['average'] }}</p>
        <x-site.atoms.stars :value="(int) round((float) $page->summary['average'])" :size="20" />
        <p class="lv-t-caption lv-tone-muted">{{ __('reviews.summary.average') }}</p>
        <p class="lv-t-micro lv-tone-muted">{{ trans_choice('reviews.summary.count', $page->summary['count'], ['count' => $page->summary['count']]) }}</p>
    </div>

    <div class="lv-rsummary__card">
        <p class="lv-t-label-m lv-tone-ink">{{ __('reviews.summary.breakdown') }}</p>
        <dl class="flex flex-col gap-2.5">
            @foreach($page->summary['breakdown'] as $row)
                <div class="lv-rbar">
                    <dt class="lv-t-caption lv-tone-muted">{{ __('reviews.summary.stars', ['stars' => $row['stars']]) }}</dt>
                    <dd class="lv-rbar__track"><span class="lv-rbar__fill" style="--w: {{ $row['percent'] }}%"></span></dd>
                    <dd class="lv-t-caption lv-tone-muted lv-rbar__value">{{ $row['percent'] }}%</dd>
                </div>
            @endforeach
        </dl>
    </div>

    @if($page->summary['details'])
        <div class="lv-rsummary__card">
            <p class="lv-t-label-m lv-tone-ink">{{ __('reviews.summary.details') }}</p>
            <dl class="flex flex-col gap-3">
                @foreach($page->summary['details'] as $detail)
                    <div class="flex items-center justify-between gap-4">
                        <dt class="lv-t-body-s lv-tone-ink">{{ __('reviews.summary.detail.'.$detail['key']) }}</dt>
                        <dd class="inline-flex items-center gap-1.5 lv-t-label-s lv-tone-ink"><x-site.atoms.icon name="star-13" :size="13" />{{ $detail['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    @endif
</section>
