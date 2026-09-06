@props(['label', 'value', 'hint' => null])

<section {{ $attributes->merge(['class' => 'rounded-md border border-slate-200 bg-white p-4 shadow-sm']) }}>
    <p class="text-sm text-slate-600">{{ $label }}</p>
    <p class="mt-2 text-2xl font-semibold text-slate-950">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</section>
