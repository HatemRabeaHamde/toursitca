@props(['type' => 'info'])

@php
    $classes = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'info' => 'border-slate-200 bg-slate-50 text-slate-800',
    ][$type] ?? 'border-slate-200 bg-slate-50 text-slate-800';
@endphp

<div {{ $attributes->merge(['class' => 'rounded-md border px-4 py-3 text-sm '.$classes]) }} role="status">
    {{ $slot }}
</div>
