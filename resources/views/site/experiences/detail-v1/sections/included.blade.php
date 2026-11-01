<section id="included" class="lv-v1-included lv-xsection" aria-label="{{ __('experiences.detail.included.title') }}">
    <div class="lv-v1-included__card">
        <h2 class="lv-t-h4 lv-serif lv-tone-ink">{{ __('experiences.detail.v1.included') }}</h2>
        <ul class="flex flex-col gap-3.5">
            @foreach($page->included['items'] as $item)
                <x-site.atoms.check-item>{{ $item }}</x-site.atoms.check-item>
            @endforeach
        </ul>
    </div>
    @if($page->included['excludedItems'])
        <div class="lv-v1-included__card">
            <h2 class="lv-t-h4 lv-serif lv-tone-ink">{{ __('experiences.detail.v1.excluded') }}</h2>
            <ul class="flex flex-col gap-3.5">
                @foreach($page->included['excludedItems'] as $item)
                    <li class="flex items-start gap-2.5 lv-t-body-s lv-tone-ink">
                        <x-site.atoms.icon name="x-dot" :size="18" class="mt-px" /><span>{{ Str::ucfirst($item) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</section>
