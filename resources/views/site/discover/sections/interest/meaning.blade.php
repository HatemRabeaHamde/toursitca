{{-- What it means — the idea behind the interest + a host's voice. --}}
<x-site.organisms.section id="meaning" bg="cream" ornament="leaves-center" labelledby="meaning-title">
    <div class="grid items-center gap-12 lg:grid-cols-[minmax(0,560px)_minmax(0,1fr)] lg:gap-[100px]">
        <div class="flex flex-col gap-6" data-reveal-group>
            <x-site.atoms.eyebrow data-reveal="fade">{{ __('discover.interest.eyebrows.meaning') }}</x-site.atoms.eyebrow>
            <h2 id="meaning-title" class="lv-t-h1 lv-tone-ink" data-reveal="heading" data-reveal-at="<0.1">
                {{ $page->copy('meaning.title') }}
                @if($page->copy('meaning.accent'))<em class="lv-italic">{{ $page->copy('meaning.accent') }}</em>@endif
            </h2>
            @foreach($page->copy('meaning.paragraphs', []) as $paragraph)
                <p @class(['lv-t-body-l', 'lv-tone-ink' => $loop->first, 'lv-tone-muted' => ! $loop->first]) data-reveal="text" data-reveal-at="<0.15">{{ $paragraph }}</p>
            @endforeach
        </div>

        @if($page->copy('meaning.quote'))
            <x-site.molecules.quote-card :quote="$page->copy('meaning.quote')" :by="$page->copy('meaning.quote_by')" :avatar="$page->copy('meaning.quote_avatar')" data-reveal="fade" />
        @endif
    </div>
</x-site.organisms.section>
