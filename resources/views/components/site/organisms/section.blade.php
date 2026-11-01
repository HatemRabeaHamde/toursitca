{{--
    Chapter shell: background, optional Moroccan divider on top and a decorative ornament.
    bg: cream | sand | white       ornament: none | leaves-right | leaves-center | leaves-left
--}}
@props(['bg' => 'cream', 'divider' => false, 'ornament' => 'none', 'id' => null, 'labelledby' => null])

<section @if($id) id="{{ $id }}" @endif @if($labelledby) aria-labelledby="{{ $labelledby }}" @endif
    {{ $attributes->class(['lv-chapter relative overflow-hidden', 'lv-chapter--'.$bg, 'has-divider' => $divider]) }}>
    @if($ornament !== 'none')
        <x-site.atoms.ornament :class="'lv-ornament-leaves--'.str_replace('leaves-', '', $ornament)" :flip="$ornament === 'leaves-center'" data-parallax="16" />
    @endif

    <div class="lv-container relative">
        @if($divider)<x-site.atoms.divider class="lv-chapter__divider" data-reveal="rule" />@endif
        {{ $slot }}
    </div>
</section>
