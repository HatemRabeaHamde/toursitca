<section class="ss-panel"><h3>{{ __('content.practical_guidance') }}</h3>
@foreach($settings['tips'] as $tip)
<article class="ss-tip"><h4>{{ $tip['title'] }}</h4><p>{{ $tip['body'] }}</p>@if(!empty($tip['example']))<blockquote>{{ $tip['example'] }}</blockquote>@endif</article>
@endforeach
<a href="{{ $settings['source'] }}" target="_blank" rel="noopener">{{ __('content.official_guidance') }} ↗</a>
</section>
<section class="ss-panel"><h3>{{ __('content.launch_checks') }}</h3><p class="ss-muted">{{ __('content.manual_checks') }}</p><ul class="ss-checklist">
<li>{{ __('content.check_crawl') }} <a href="{{ url('/robots.txt') }}" target="_blank" rel="noopener">robots.txt ↗</a></li>
<li>{{ __('content.check_sitemap') }} <a href="{{ url('/sitemap.xml') }}" target="_blank" rel="noopener">sitemap.xml ↗</a></li>
<li>{{ __('content.check_console') }} <a href="https://search.google.com/search-console" target="_blank" rel="noopener">Search Console ↗</a></li>
<li>{{ __('content.check_mobile') }} <a href="https://pagespeed.web.dev/" target="_blank" rel="noopener">PageSpeed Insights ↗</a></li>
<li>{{ __('content.check_schema') }} <a href="https://validator.schema.org/" target="_blank" rel="noopener">Schema validator ↗</a></li>
</ul><p class="ss-muted">{{ __('content.no_guarantee') }}</p></section>
