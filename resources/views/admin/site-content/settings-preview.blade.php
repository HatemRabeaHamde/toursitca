<section class="ss-panel">
<h3>{{ __('content.preview_'.$panel) }}</h3><p class="ss-muted">{{ __('content.preview_note_'.$panel) }}</p>
@if($panel === 'search')
 <div class="ss-search-preview"><small x-text="urls[locale]"></small><h4 x-text="text('seo.title')"></h4><p x-text="text('seo.description')"></p></div>
 <p class="ss-count"><span x-text="text('seo.title').length"></span> {{ __('content.title_chars') }} · <span x-text="text('seo.description').length"></span> {{ __('content.description_chars') }}</p>
 <p x-show="text('seo.title').length > 60 || text('seo.description').length > 160" class="ss-advice">{{ __('content.truncation_hint') }}</p>
 <p x-show="text('seo.robots').includes('noindex')" class="ss-warning">{{ __('content.noindex_hint') }}</p>
 <div class="ss-social-preview" x-data="{ failed: false }" x-effect="image('seo.social_image'); failed=false"><p class="ss-warning" x-show="failed" x-cloak>{{ __('content.image_unavailable') }}</p><img x-on:error="failed=true" x-on:load="failed=false" x-show="image('seo.social_image') && !failed" :src="image('seo.social_image')" :alt="alt('seo.social_image')"><div><strong x-text="text('seo.social_title') || text('seo.title')"></strong><p x-text="text('seo.social_description') || text('seo.description')"></p></div></div>
@elseif($panel === 'publisher')
 <div class="ss-entity-preview" x-data="{ failed: false }" x-effect="image('geo.logo'); failed=false"><p class="ss-warning" x-show="failed" x-cloak>{{ __('content.image_unavailable') }}</p><img x-on:error="failed=true" x-on:load="failed=false" x-show="image('geo.logo') && !failed" :src="image('geo.logo')" :alt="alt('geo.logo')"><h4 x-text="text('geo.name')"></h4><p x-text="text('geo.description')"></p><p x-text="text('geo.url')"></p></div>
 <p class="ss-muted">{{ __('content.entity_preview_note') }}</p>
@else
 <div class="ss-answer-preview"><h4 x-text="text('aeo.question')"></h4><p x-text="text('aeo.summary') || @js(__('content.empty_answer'))"></p><small x-text="text('aeo.reviewer')"></small><small x-text="text('aeo.reviewed_on')"></small><ul><template x-for="index in [1, 2]" :key="index"><li x-show="safeUrl('aeo.source_'+index+'_url')"><a :href="safeUrl('aeo.source_'+index+'_url')" rel="noopener noreferrer" x-text="text('aeo.source_'+index+'_label') || text('aeo.source_'+index+'_url')"></a></li></template></ul></div>
 <a class="adm-btn adm-btn--ghost" href="{{ route('admin.landing.faqs.index') }}">{{ __('content.manage_faqs') }} →</a>
@endif
<a class="ss-preview-link" href="{{ route($definition['route'], ['locale'=>'en','content_preview'=>1]) }}" target="_blank" rel="noopener">{{ __('content.open_saved_preview') }} ↗</a>
</section>
@if($panel === 'search')
<section class="ss-panel"><h3>{{ __('content.automatic_settings') }}</h3><p class="ss-muted">{{ __('content.automatic_hint') }}</p><dl class="ss-auto"><dt>Canonical</dt><dd>{{ $metadata['canonical'] }}</dd>@foreach($metadata['hreflang'] as $language=>$url)<dt>{{ $language }}</dt><dd>{{ $url }}</dd>@endforeach</dl></section>
@endif
<details class="ss-panel"><summary>{{ __('content.schema_preview') }}</summary><p class="ss-muted">{{ __('content.schema_preview_note') }}</p><pre x-text="JSON.stringify(schemas[locale], null, 2)"></pre></details>
