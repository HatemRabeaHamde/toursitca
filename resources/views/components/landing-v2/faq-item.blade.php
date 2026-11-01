{{-- Accordion row. Parent provides x-data="{ open: <index> }". --}}
@props(['faq', 'index'])

@php($icons = config('landing_v2.icons'))

<div class="lv-faq">
    <h3>
        <button type="button" class="flex min-h-[84px] w-full items-center justify-between gap-6 py-5 text-start"
            @click="open = open === {{ $index }} ? null : {{ $index }}"
            :aria-expanded="(open === {{ $index }}).toString()"
            aria-controls="lv-faq-{{ $index }}">
            <span class="lv-faq-q">{{ $faq['question'] }}</span>
            <span :class="{ 'is-open': open === {{ $index }} }" @class(['lv-faq-icon', 'is-open' => $index === 0]) aria-hidden="true">
                <img src="{{ asset($icons['faq_plus']) }}" alt="" width="18" height="18" class="lv-faq-icon-plus">
                <img src="{{ asset($icons['faq_open']) }}" alt="" width="25" height="25" class="lv-faq-icon-open">
            </span>
        </button>
    </h3>
    <div id="lv-faq-{{ $index }}" @class(['lv-faq-panel', 'is-open' => $index === 0]) :class="{ 'is-open': open === {{ $index }} }" role="region">
        <div><p class="lv-faq-a pb-6">{{ $faq['answer'] }}</p></div>
    </div>
</div>
