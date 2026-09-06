<section class="nl-section" id="newsletter">
    <div class="nl-inner">
        <p class="nl-eyebrow">{{ __('ui.landing.sections.newsletter_title') }}</p>
        <h2 class="nl-h">{{ __('ui.landing.sections.newsletter_title') }}</h2>
        <p class="nl-p">{{ __('ui.landing.sections.newsletter_body') }}</p>
        <form class="nl-form" method="POST" action="{{ route('site.newsletter.store', ['locale' => app()->getLocale()]) }}">
            @csrf
            <input name="email" type="email" required placeholder="{{ __('ui.fields.email') }}">
            <button type="submit">{{ __('ui.landing.sections.newsletter_cta') }}</button>
        </form>
    </div>
</section>
