@if($content->get('aeo.summary'))
<div class="lv-container lv-body py-8"><p>{{ $content->get('aeo.summary') }}</p></div>
@endif
<x-site.organisms.know-before :faqs="$page->faqs()" :content="$content" :link-href="$page->discoverUrl()" />
