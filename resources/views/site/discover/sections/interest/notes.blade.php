{{-- Practical notes — table manners and good-to-knows. --}}
<x-site.organisms.section id="notes" bg="cream" :divider="true" labelledby="notes-title">
    <x-site.molecules.section-header id="notes-title" size="h1"
        :eyebrow="__('discover.interest.eyebrows.notes')"
        :before="$page->copy('notes.title')"
        :accent="$page->copy('notes.accent')" />

    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-reveal="stagger">
        @foreach($page->notes as $note)
            <x-site.molecules.info-card :title="$note['title']" :body="$note['body']" />
        @endforeach
    </div>
</x-site.organisms.section>
