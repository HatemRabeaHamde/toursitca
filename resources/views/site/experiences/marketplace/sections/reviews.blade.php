{{-- Traveller reviews: only real, booking-linked reviews (section hidden when there are none). --}}
<x-site.organisms.section id="reviews" bg="cream" :divider="true" labelledby="reviews-title" class="lv-xreviews">
    <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
        <x-site.molecules.section-header id="reviews-title" class="lg:flex-1"
            :eyebrow="__('experiences.reviews.eyebrow')"
            :before="__('experiences.reviews.title')"
            :accent="__('experiences.reviews.accent')" />

        <div class="flex shrink-0 items-center gap-4" data-reveal="fade">
            <p class="lv-xreviews__score">{{ $page->reviews['average'] }}</p>
            <div class="flex flex-col gap-1">
                <p class="lv-xreviews__stars" aria-hidden="true">★★★★★</p>
                <p class="lv-t-caption lv-tone-muted">{{ __('experiences.reviews.average') }}</p>
                <p class="lv-t-caption lv-tone-muted">{{ __('experiences.reviews.only_travellers') }}</p>
            </div>
        </div>
    </div>

    @if($page->reviews['reviews'])
        <div class="mt-10 grid gap-6 md:grid-cols-3" data-reveal="stagger">
            @foreach($page->reviews['reviews'] as $review)
                <x-site.molecules.review-card :rating="$review['rating']" :body="$review['body']" :author="$review['author']"
                    :initials="$review['initials']" :avatar="$review['avatar']" :context="$review['context']" />
            @endforeach
        </div>
    @endif

    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" data-reveal="fade">
        <p class="flex items-center gap-2 lv-t-caption lv-tone-muted">
            <x-site.atoms.icon name="shield-success-15" :size="15" />
            <span>{{ __('experiences.reviews.verified') }}</span>
        </p>
        <x-site.atoms.link-arrow :href="route('site.reviews.index', ['locale' => app()->getLocale()])">{{ __('experiences.reviews.link') }}</x-site.atoms.link-arrow>
    </div>
</x-site.organisms.section>
