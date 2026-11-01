<x-site.molecules.guide-chapter id="overview" :eyebrow="__('discover.destination.eyebrows.overview')" :title="$page->copy('overview.title')">
    <div class="flex max-w-[780px] flex-col gap-5">
        @foreach($page->copy('overview.paragraphs', []) as $paragraph)
            <p @class(['lv-t-body-l', 'lv-tone-ink' => $loop->first, 'lv-tone-muted' => ! $loop->first]) data-reveal="text" data-reveal-at="<0.15">{{ $paragraph }}</p>
        @endforeach
        @if($page->copy('overview.quote'))
            <x-site.molecules.pull-quote class="mt-5" :quote="$page->copy('overview.quote')" :by="$page->copy('overview.quote_by')" data-reveal="fade" data-reveal-at="<0.2" />
        @endif
    </div>
</x-site.molecules.guide-chapter>
