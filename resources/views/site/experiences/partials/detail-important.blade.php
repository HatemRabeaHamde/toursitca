<section id="important" class="xp-detail-section">
    <h2 class="xp-section-heading">{{ __('ui.experience_detail.important_info_title') }}</h2>

    <div class="xp-accordion">
        @foreach (__('ui.experience_detail.important_items') as $index => $item)
            <div class="xp-accordion-item" x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }">
                <button type="button" class="xp-accordion-trigger" @click="open = ! open" :aria-expanded="open">
                    <span>{{ $item['title'] }}</span>
                    <span class="xp-accordion-chevron" :class="{ 'is-open': open }" aria-hidden="true">▾</span>
                </button>
                <div class="xp-accordion-body" x-show="open" x-cloak x-transition>
                    {{ $item['body'] }}
                </div>
            </div>
        @endforeach
    </div>
</section>
