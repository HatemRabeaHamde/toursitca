@props(['title', 'description' => null])

<section {{ $attributes->merge(['class' => 'rounded-md border border-dashed border-slate-300 bg-white px-6 py-10 text-center']) }}>
    <h2 class="text-base font-semibold text-slate-950">{{ $title }}</h2>
    @if ($description)
        <p class="mx-auto mt-2 max-w-md text-sm text-slate-600">{{ $description }}</p>
    @endif
    @if (trim($slot) !== '')
        <div class="mt-4">{{ $slot }}</div>
    @endif
</section>
