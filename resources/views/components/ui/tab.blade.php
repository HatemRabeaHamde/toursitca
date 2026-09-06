@props(['active' => false, 'href' => '#'])

<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center border-b-2 px-3 py-2 text-sm font-medium '.($active ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-900')]) }}
>
    {{ $slot }}
</a>
