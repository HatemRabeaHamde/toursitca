{{-- Five stars, `value` of them filled (Figma "Stars / 5"). --}}
@props(['value', 'size' => 18, 'label' => null])

<span {{ $attributes->class(['inline-flex gap-[3px]']) }} role="img" aria-label="{{ $label ?? __('reviews.card.stars', ['rating' => $value]) }}">
    @for($i = 1; $i <= 5; $i++)
        <x-site.atoms.icon :name="$i <= $value ? 'star' : 'star-off'" :size="$size" />
    @endfor
</span>
