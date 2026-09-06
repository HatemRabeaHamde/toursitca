<section class="xp-cta"
         x-data="{
             state: 'idle',
             error: '',
             email: '',
             message: '',
             async submit() {
                 if (this.state === 'sending') return;
                 this.state = 'sending';
                 this.error = '';
                 try {
                     const res = await fetch('{{ route('site.newsletter.store', ['locale' => app()->getLocale()]) }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             Accept: 'application/json',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                         },
                         body: JSON.stringify({ email: this.email, message: this.message, source: 'experiences_cta' }),
                     });
                     if (! res.ok) {
                         const data = await res.json().catch(() => null);
                         this.error = data?.errors?.email?.[0] ?? data?.message ?? '{{ __('ui.messages.generic_error') }}';
                         this.state = 'idle';
                         return;
                     }
                     this.state = 'sent';
                 } catch (e) {
                     this.error = '{{ __('ui.messages.generic_error') }}';
                     this.state = 'idle';
                 }
             },
         }">
    <div class="xp-cta-glow" aria-hidden="true"></div>
    <div class="xp-cta-inner">
        <p class="xp-cta-eyebrow">{{ __('ui.experiences_page.cta_label') }}</p>
        <h2 class="xp-cta-title">
            {!! str_replace(
                ':emphasis',
                '<em>'.__('ui.experiences_page.cta_title_emphasis').'</em>',
                e(__('ui.experiences_page.cta_title'))
            ) !!}
        </h2>

        <p class="xp-cta-body" x-show="state !== 'sent'">{{ __('ui.experiences_page.cta_body') }}</p>

        <form class="xp-cta-form"
              :class="{ 'has-error': error }"
              x-show="state !== 'sent'"
              @submit.prevent="submit()">
            <textarea x-model="message" rows="2"
                      placeholder="{{ __('ui.experiences_page.cta_message_placeholder') }}"
                      aria-label="{{ __('ui.experiences_page.cta_message_placeholder') }}"
                      :disabled="state === 'sending'"></textarea>
            <div class="xp-cta-form-row">
                <input type="email" x-model="email" required
                       placeholder="{{ __('ui.experiences_page.cta_placeholder') }}"
                       aria-label="{{ __('ui.fields.email') }}"
                       :disabled="state === 'sending'">
                <button type="submit" :disabled="state === 'sending'">
                    <span x-show="state !== 'sending'">{{ __('ui.experiences_page.cta_submit') }}</span>
                    <span x-show="state === 'sending'" x-cloak>{{ __('ui.experiences_page.cta_sending') }}</span>
                    <svg x-show="state !== 'sending'" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    <svg x-show="state === 'sending'" x-cloak class="xp-cta-spin" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9" stroke-opacity=".25"/><path d="M21 12a9 9 0 0 0-9-9"/></svg>
                </button>
            </div>
        </form>

        <p class="xp-cta-error" x-cloak x-show="error" x-text="error"></p>

        <div class="xp-cta-success" x-cloak x-show="state === 'sent'" x-transition:enter="xp-cta-success-enter" x-transition:enter-start="xp-cta-success-enter-start">
            <span class="xp-cta-check">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </span>
            <span>{{ __('ui.experiences_page.cta_success') }}</span>
        </div>
    </div>
</section>
