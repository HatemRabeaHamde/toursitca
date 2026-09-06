<div class="ed-inclusions-grid">
    @if ($experience->inclusions)
        <section class="ed-inclusion-box">
            <h3>{{ __('ui.fields.inclusions') }}</h3>
            <ul>
                @foreach ((array) $experience->getTranslation('inclusions', app()->getLocale(), false) as $item)
                    <li>
                        <span class="yes">✓</span>
                        <span>{{ $item }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($experience->exclusions)
        <section class="ed-inclusion-box">
            <h3>{{ __('ui.fields.exclusions') }}</h3>
            <ul>
                @foreach ((array) $experience->getTranslation('exclusions', app()->getLocale(), false) as $item)
                    <li>
                        <span class="no">×</span>
                        <span>{{ $item }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
