@php
    $user = auth()->user();
    [$firstName, $lastName] = array_pad(preg_split('/\s+/', trim((string) $user->name), 2), 2, '');
    $host = $trip['hostName'] ?? __('account.sent.your_host');
@endphp

<form method="POST" action="{{ route('site.checkout.contact.store', ['locale' => app()->getLocale(), 'checkoutSession' => $checkoutSession]) }}" class="flex flex-col gap-6" data-checkout-form>
    @csrf

    <section class="lv-flow-card" aria-labelledby="contact-title">
        <div>
            <h2 id="contact-title" class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.checkout.contact') }}</h2>
            <p class="lv-t-body-s lv-tone-muted">{{ __('account.checkout.contact_help') }}</p>
        </div>
        <div class="lv-form-grid">
            <x-site.molecules.form-field name="contact_first_name" :label="__('account.fields.first_name')" :value="old('contact_first_name', $checkoutSession->contact_first_name ?: $firstName)" autocomplete="given-name" required />
            <x-site.molecules.form-field name="contact_last_name" :label="__('account.fields.last_name')" :value="old('contact_last_name', $checkoutSession->contact_last_name ?: $lastName)" autocomplete="family-name" required />
            <x-site.molecules.form-field name="contact_email" type="email" :label="__('account.fields.email')" :value="old('contact_email', $checkoutSession->contact_email ?: $user->email)" autocomplete="email" required />
            <x-site.molecules.form-field name="contact_phone" type="tel" :label="__('account.fields.phone')" :value="old('contact_phone', $checkoutSession->contact_phone ?: $user->phone)" autocomplete="tel" />
        </div>
        <x-site.molecules.form-field name="contact_country" :label="__('account.fields.country')" :value="old('contact_country', $checkoutSession->contact_country ?: $user->country)" autocomplete="country-name" />
    </section>

    <section class="lv-flow-card" aria-labelledby="message-title">
        <div>
            <h2 id="message-title" class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.checkout.message', ['name' => $host]) }}</h2>
            <p class="lv-t-body-s lv-tone-muted">{{ __('account.checkout.message_help') }}</p>
        </div>
        <div class="lv-form-field">
            <label for="special_requests" class="lv-t-label-s lv-tone-ink">{{ __('account.checkout.message_label') }}</label>
            <textarea id="special_requests" name="special_requests" rows="4" maxlength="1000" class="lv-form-input lv-form-input--area" placeholder="{{ __('account.checkout.message_placeholder') }}">{{ old('special_requests', $checkoutSession->special_requests) }}</textarea>
            @error('special_requests')<p class="lv-form-error lv-t-caption" role="alert">{{ $message }}</p>@enderror
        </div>
    </section>

    <section class="lv-flow-card">
        <div>
            <label class="lv-check lv-t-body-s"><input type="checkbox" name="terms" value="1" required @checked(old('terms'))>{{ __('account.checkout.terms') }}</label>
            @error('terms')<p class="lv-form-error lv-t-caption" role="alert">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-wrap items-center gap-5">
            <x-site.atoms.button type="submit">{{ __('account.checkout.send', ['name' => $host]) }}</x-site.atoms.button>
            <p class="flex items-center gap-2 lv-t-caption lv-tone-muted"><x-site.atoms.icon name="card-muted" :size="15" />{{ __('account.checkout.no_card') }}</p>
        </div>
    </section>
</form>
