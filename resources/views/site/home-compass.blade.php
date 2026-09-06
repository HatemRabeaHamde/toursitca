@extends('layouts.site')

@push('styles')
    @vite(['resources/css/landing.css', 'resources/css/experience-card.css'])
@endpush

@section('content')
    <main class="mx-auto max-w-6xl px-6 py-16">
        <header class="mb-10">
            <p class="text-sm font-semibold uppercase tracking-[.18em] text-[var(--clay)]">TourstiCa</p>
            <h1 class="mt-3 font-serif text-5xl text-[var(--ink)]">Morocco Compass</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-[var(--ink-70)]">
                A focused view of curated Morocco experiences for travelers comparing cities, styles, and starting points.
            </p>
        </header>

        <section class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($landing->trendingCards as $card)
                @include('site.experiences.partials.card-grid', ['card' => $card])
            @endforeach
        </section>
    </main>
@endsection
