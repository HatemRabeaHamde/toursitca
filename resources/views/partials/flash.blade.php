@php
    $flashClasses = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'info' => 'border-slate-200 bg-slate-50 text-slate-800',
    ];
@endphp

@foreach (['success', 'error', 'warning', 'info'] as $type)
    @if (session($type))
        <div
            class="rounded-xl border px-4 py-3 text-sm font-medium shadow-sm {{ $flashClasses[$type] }}"
            role="{{ $type === 'error' ? 'alert' : 'status' }}"
            x-data
            x-init="setTimeout(() => $el.remove(), {{ $type === 'error' ? 6500 : 4500 }})"
        >
            <div class="flex items-start justify-between gap-4">
                <span>{{ session($type) }}</span>
                <button type="button" class="text-current opacity-60 transition hover:opacity-100" aria-label="{{ __('ui.actions.close') }}" x-on:click="$el.closest('[role]').remove()">×</button>
            </div>
        </div>
    @endif
@endforeach
