{{-- Global footer (organism). Class: App\View\Components\Site\Organisms\Footer --}}
@php
    $icons = config('landing_v2.icons');
    $brand = $brand();
    $socials = $socials();
@endphp

<footer class="lv-footer mt-0 overflow-hidden text-[#f6f1e4]">
    <div class="lv-footer-pattern" data-content-key="footer.pattern" style="background-image: url('{{ (request()->attributes->get('site_content') ? site_content('home.footer.pattern')['url'] : asset(config('landing_v2.images.footer_pattern'))) }}')" aria-hidden="true"></div>

    <div class="lv-container relative pt-16 lg:pt-[68px]" data-reveal-group data-reveal-start="top 92%">
        <div class="grid gap-12 md:grid-cols-2 lg:grid-cols-[373px_1fr_270px] lg:gap-10" data-reveal="stagger">
            <div>
                <img src="{{ landing_artwork('logo_footer', $icons['logo_footer']) }}" alt="{{ $brand }}" width="173" height="31" loading="lazy">
                <p class="lv-footer-muted mt-9 max-w-[300px]"><span data-content-key="footer.tagline">{{ landing_text('site.footer.tagline') }}</span></p>
                @if($socials)
                    <ul class="mt-6 flex gap-3">
                        @foreach($socials as $network => $url)
                            <li>
                                <a href="{{ $url }}" @if($url !== '#') target="_blank" rel="noopener noreferrer" @endif class="lv-glass flex h-10 w-10 items-center justify-center" aria-label="{{ landing_text('site.footer.social', ['network' => ucfirst($network)]) }}">
                                    <img src="{{ landing_artwork('social.'.$network, $icons['social'][$network]) }}" alt="" width="16" height="16" aria-hidden="true">
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-10 sm:grid-cols-3">
                @foreach($columns() as $heading => $links)
                    <nav aria-label="{{ landing_text('site.footer.'.$heading) }}">
                        <p class="lv-footer-head"><span data-content-key="footer.">{{ landing_text('site.footer.'.$heading) }}</span></p>
                        <ul class="mt-5 flex flex-col gap-3">
                            @foreach($links as $link)
                                <li><a href="{{ $link['href'] }}" class="lv-footer-link"><span data-content-key="{{ $link['content_key'] }}">{{ $link['label'] }}</span></a></li>
                            @endforeach
                        </ul>
                    </nav>
                @endforeach
            </div>

            <div id="contact" x-data="lvNewsletter({ url: @js(route('site.newsletter.store', ['locale' => app()->getLocale()])), errorMessage: @js(landing_text('site.footer.error')) })">
                <p class="lv-footer-head"><span data-content-key="footer.newsletter_title">{{ landing_text('site.footer.newsletter_title') }}</span></p>
                <p class="lv-footer-muted mt-5"><span data-content-key="footer.newsletter_body">{{ landing_text('site.footer.newsletter_body') }}</span></p>
                <form class="mt-5" @submit.prevent="submit" novalidate>
                    <label for="lv-newsletter-email" class="sr-only"><span data-content-key="footer.email_label">{{ landing_text('site.footer.email_label') }}</span></label>
                    <div class="lv-glass flex h-[50px] items-center gap-2 pe-1.5 ps-5">
                        <input id="lv-newsletter-email" type="email" name="email" x-model="email" required autocomplete="email" placeholder="{{ landing_text('site.footer.email_placeholder') }}" class="lv-input min-w-0 flex-1 p-0">
                        <button type="submit" class="lv-send flex h-9 w-9 shrink-0 items-center justify-center disabled:opacity-60" :disabled="status === 'loading'" aria-label="{{ landing_text('site.footer.subscribe') }}">
                            <img src="{{ landing_artwork('send', $icons['send']) }}" alt="" width="16" height="16" aria-hidden="true">
                        </button>
                    </div>
                    <p class="mt-2 min-h-4 text-xs" :class="status === 'error' ? 'text-[#f2a48a]' : 'text-[#b9c4bc]'" x-text="message" aria-live="polite"></p>
                </form>
            </div>
        </div>

        <p class="lv-footer-copy mt-9 pb-8 pt-8" data-reveal="fade" data-reveal-at="<0.4"><span data-content-key="footer.copyright">{{ landing_text('site.footer.copyright', ['year' => now()->year, 'brand' => $brand]) }}</span></p>
    </div>
</footer>
