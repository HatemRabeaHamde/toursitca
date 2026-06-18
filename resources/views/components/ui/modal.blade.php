@props(['id', 'title' => null])

<div x-data="{ open: false }" x-on:open-modal.window="open = $event.detail === '{{ $id }}'" x-show="open" x-cloak class="fixed inset-0 z-50">
    <div class="absolute inset-0 bg-slate-950/50" x-on:click="open = false"></div>
    <section class="relative mx-auto mt-20 w-full max-w-lg rounded-md bg-white p-6 shadow-xl">
        @if ($title)
            <h2 class="text-base font-semibold text-slate-950">{{ $title }}</h2>
        @endif
        <div class="mt-4">{{ $slot }}</div>
    </section>
</div>
