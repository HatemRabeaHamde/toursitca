<section class="faq-section" id="faq">
    <div class="faq-inner" x-data="{ openIndex: null }">
        <p class="sec-label" style="justify-content:center;">{{ __('ui.landing.sections.faq_label') }}</p>
        <h2 class="sec-h2" style="text-align:center;">{{ __('ui.landing.sections.faq_title') }}</h2>
        <div class="faq-list">
            @foreach ($landing->faqs as $index => $item)
                <div class="faq-item" :class="{ open: openIndex === {{ $index }} }" @click="openIndex = (openIndex === {{ $index }} ? null : {{ $index }})">
                    <div class="faq-q">
                        <p class="faq-q-text">{{ $item['question'] }}</p>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-a">{{ $item['answer'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
