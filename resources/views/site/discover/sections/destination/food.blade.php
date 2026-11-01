<x-site.molecules.guide-chapter id="food" :eyebrow="__('discover.destination.eyebrows.food')" :title="$page->copy('food.title', __('discover.destination.chapters.food'))">
    <div class="flex flex-col gap-3" data-reveal="stagger">
        @foreach($page->dishes as $dish)
            <x-site.molecules.dish-row :title="$dish['title']" :body="$dish['body']" :image="$dish['image']" />
        @endforeach
    </div>
</x-site.molecules.guide-chapter>
