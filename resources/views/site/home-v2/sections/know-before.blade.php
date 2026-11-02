@if($answer->summary)
<section class="lv-container lv-body py-8" aria-label="{{ __('content.answer_summary') }}">
 @if($answer->question)<h2 class="lv-serif lv-h2 lv-h2--sm">{{ $answer->question }}</h2>@endif
 <p>{{ $answer->summary }}</p>
 @if($answer->reviewer)<p>{{ __('content.reviewed_by') }} {{ $answer->reviewer }}</p>@endif
 @if($answer->reviewedOn)<p>{{ __('content.reviewed_date') }} <time datetime="{{ $answer->reviewedOn }}">{{ $answer->reviewedOn }}</time></p>@endif
 @if($answer->sources)<ul>@foreach($answer->sources as $source)<li><a href="{{ $source['url'] }}" rel="noopener noreferrer">{{ $source['label'] }}</a></li>@endforeach</ul>@endif
</section>
@endif
<x-site.organisms.know-before :faqs="$page->faqs()" :content="$content" :link-href="landing_link('know.link_url', $page->discoverUrl())" />
