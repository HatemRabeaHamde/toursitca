{{-- Score card with star breakdown + the top review cards. --}}
@props(['reviews'])

<div {{ $attributes->class(['lv-rsum']) }}>
    <div class="lv-rsum__score">
        <p class="lv-rsum__value">{{ $reviews['average'] }}</p>
        <x-site.atoms.stars :value="(int) round((float) $reviews['average'])" :size="15" :label="__('experiences.detail.reviews.stars', ['rating' => $reviews['average']])" />
        <p class="lv-t-caption lv-tone-muted">{{ trans_choice('experiences.detail.v1.reviews.based_on', $reviews['count'], ['count' => $reviews['count']]) }}</p>
        <ul class="mt-2 flex flex-col gap-[7px]">
            @foreach($reviews['breakdown'] as $row)
                <li class="lv-rsum__bar lv-t-micro lv-tone-ink">
                    <span>{{ $row['stars'] }} ★</span>
                    <span class="lv-rsum__track"><span class="lv-rsum__fill" style="width: {{ $row['percent'] }}%"></span></span>
                    <span>{{ $row['percent'] }}%</span>
                </li>
            @endforeach
        </ul>
    </div>
    @foreach($reviews['cards'] as $card)
        <x-site.molecules.review-card :rating="$card['rating']" :body="$card['body']" :author="$card['author']"
            :avatar="$card['avatar']" :initials="$card['initials']" :context="$card['context']" />
    @endforeach
</div>
