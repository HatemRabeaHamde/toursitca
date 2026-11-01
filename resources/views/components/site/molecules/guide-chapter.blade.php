{{--
    One chapter in a guide's main column (Destination): anchor target for the
    sticky "In this guide" nav, eyebrow + H2, then the chapter content.
--}}
@props(['id', 'eyebrow', 'title'])

<section id="{{ $id }}" aria-labelledby="{{ $id }}-title" {{ $attributes->class(['lv-guide-chapter']) }} data-reveal-group>
    <x-site.atoms.eyebrow data-reveal="fade">{{ $eyebrow }}</x-site.atoms.eyebrow>
    <h2 id="{{ $id }}-title" class="lv-t-h2 lv-tone-ink mt-4" data-reveal="heading" data-reveal-at="<0.1">{{ $title }}</h2>
    <div class="mt-6">{{ $slot }}</div>
</section>
