{{-- "At a glance" bar under a destination hero. Facts: [['label' => …, 'value' => …], …] --}}
@props(['facts'])

<dl {{ $attributes->class(['lv-fact-bar']) }} data-reveal="stagger">
    @foreach($facts as $fact)
        <div class="flex flex-col gap-1.5">
            <dt class="lv-t-eyebrow-s lv-tone-muted">{{ $fact['label'] }}</dt>
            <dd class="lv-t-h6 lv-tone-ink">{{ $fact['value'] }}</dd>
        </div>
    @endforeach
</dl>
