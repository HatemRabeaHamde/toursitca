<section class="sc-record-modal" x-show="recordUrl" x-cloak x-trap.inert.noscroll="recordUrl" role="dialog" aria-modal="true" aria-labelledby="record-dialog-title" @keydown.escape.stop="closeRecord()">
 <header><div><p>{{ __('content.source_record') }}</p><h2 id="record-dialog-title" x-text="recordLabel"></h2></div><button type="button" class="adm-btn adm-btn--ghost" @click="closeRecord()">{{ __('content.close_record') }} ×</button></header>
 <p class="sc-record-notice">{{ __('content.record_save_notice') }}</p>
 <p class="sc-record-saved" x-show="recordChanged" role="status">{{ __('content.record_saved_notice') }}</p>
 <iframe x-ref="recordFrame" :src="recordUrl || 'about:blank'" title="{{ __('content.record_form') }}"></iframe>
 <footer><span x-show="recordDirty">{{ __('content.record_unsaved') }}</span><button type="button" class="adm-btn adm-btn--primary" @click="closeRecord(true)">{{ __('content.close_refresh') }}</button></footer>
</section>
