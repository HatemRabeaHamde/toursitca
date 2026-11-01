<x-site.molecules.guide-chapter id="culture" :eyebrow="__('discover.destination.eyebrows.culture')" :title="$page->copy('culture.title')">
    <div class="grid items-center gap-8 md:grid-cols-[minmax(0,420px)_1fr] lg:gap-14">
        <div class="flex flex-col gap-4">
            @foreach($page->copy('culture.paragraphs', []) as $paragraph)
                <p @class(['lv-t-body-m', 'lv-tone-ink' => $loop->first, 'lv-tone-muted' => ! $loop->first]) data-reveal="text" data-reveal-at="<0.15">{{ $paragraph }}</p>
            @endforeach
        </div>
        @if($page->crafts)
            <div class="grid grid-cols-2 gap-4 lg:gap-6">
                @foreach(array_slice($page->crafts, 0, 2) as $craft)
                    <div class="lv-culture-image" data-reveal="image" data-reveal-at="<0.15">
                        <img src="{{ $craft['image'] }}" alt="{{ $craft['title'] ?? '' }}" loading="lazy" width="240" height="300">
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-site.molecules.guide-chapter>
