{{--
    "Know before you go" panel: intro + photo on the left, FAQ accordion on the right.
    Shared by the landing and the Discover hub. faqs: [['question' => …, 'answer' => …], …]
--}}
@props(['faqs', 'linkHref', 'id' => 'know', 'content' => null])

@php($icons = config('landing_v2.icons'))

<section id="{{ $id }}" {{ $attributes->class(['lv-bg-cream pb-24 pt-2 lg:pb-[94px]']) }} aria-labelledby="{{ $id }}-title">
    <div class="lv-container">
        <div class="lv-know grid gap-12 px-6 py-10 sm:px-10 lg:grid-cols-[382px_1fr] lg:gap-[123px] lg:px-20 lg:py-[42px]">
            <div data-reveal-group>
                <p class="lv-know-eyebrow flex items-center gap-3" data-reveal="fade">{{ ($content ? $content->get('know.eyebrow') : __('landing_v2.know.eyebrow')) }}</p>
                <h2 id="{{ $id }}-title" class="lv-know-title mt-5" data-reveal="heading" data-reveal-at="<0.1">
                    {{ ($content ? $content->get('know.title') : __('landing_v2.know.title')) }}<br><em class="ms-1">{{ ($content ? $content->get('know.title_accent') : __('landing_v2.know.title_accent')) }}</em>
                </h2>
                <div data-reveal="text" data-reveal-at="<0.35" class="mt-5 max-w-[382px] text-[15px] leading-[1.625] text-[rgba(16,32,28,.7)]">@if($content)<x-site.content key="home.know.description" />@else{{ __('landing_v2.know.description') }}@endif</div>
                <img src="{{ ($content ? $content->get('know.image')['url'] : asset(config('landing_v2.images.know_before'))) }}" alt="{{ ($content ? $content->get('know.image')['alt'] : __('landing_v2.know.image_alt')) }}" loading="lazy" width="382" height="239" data-reveal="image" data-reveal-at="<0.2" class="mt-9 aspect-[382/239] w-full rounded-[20px] object-cover">
            </div>

            <div class="flex flex-col justify-between gap-8">
                <div class="border-t border-[#e2d6c0]" x-data="{ open: 0 }" data-reveal="stagger" data-reveal-start="top 85%">
                    @foreach($faqs as $index => $faq)
                        <x-landing-v2.faq-item :faq="$faq" :index="$index" />
                    @endforeach
                </div>
                <a href="{{ $linkHref }}" class="lv-faq-link inline-flex items-center gap-2 self-start" data-reveal="fade">
                    {{ ($content ? $content->get('know.link') : __('landing_v2.know.link')) }}
                    <img src="{{ asset($icons['arrow_link']) }}" alt="" width="15" height="15" aria-hidden="true">
                </a>
            </div>
        </div>
    </div>
</section>
