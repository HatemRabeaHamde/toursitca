{{-- Dark green "How a host gets on Tourstica" band: numbered trust steps. steps: [['number', 'title', 'body'], …] --}}
@props(['eyebrow', 'title', 'accent' => null, 'steps', 'id' => null])

<section @if($id) id="{{ $id }}" @endif {{ $attributes->class(['lv-vetting relative overflow-hidden']) }} aria-labelledby="{{ $id ?? 'vetting' }}-title">
    <span class="lv-vetting__zellige" aria-hidden="true"></span>
    <div class="lv-container relative" data-reveal-group>
        <x-site.atoms.eyebrow tone="gold" data-reveal="fade">{{ $eyebrow }}</x-site.atoms.eyebrow>
        <h2 id="{{ $id ?? 'vetting' }}-title" class="lv-t-section lv-tone-cream mt-4" data-reveal="heading" data-reveal-at="<0.1">
            {{ $title }}
            @if($accent)<em class="lv-italic">{{ $accent }}</em>@endif
        </h2>

        <ol class="mt-10 grid gap-5 md:grid-cols-3 lg:mt-12 lg:gap-6" data-reveal="stagger" data-reveal-at="<0.3">
            @foreach($steps as $step)
                <li class="lv-vetting__step">
                    <span class="lv-vetting__number lv-t-label-s" aria-hidden="true">{{ $step['number'] }}</span>
                    <h3 class="lv-t-h4 lv-tone-cream">{{ $step['title'] }}</h3>
                    <p class="lv-t-body-s lv-vetting__text">{{ $step['body'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
