<article class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
    <a href="{{ $card->showUrl }}" class="block">
        <div class="relative aspect-[4/3] bg-slate-100">
            @if ($card->thumbnailUrl)
                <img src="{{ $card->thumbnailUrl }}" alt="{{ $card->title }}" class="h-full w-full object-cover" loading="lazy">
            @endif
            @if ($card->badges)
                <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                    @foreach ($card->badges as $badge)
                        <span class="rounded bg-slate-950 px-2 py-1 text-xs font-semibold text-white">{{ $badge }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </a>

    <div class="p-4">
        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $card->city }} · {{ $card->category }}</p>
        <h2 class="mt-2 text-base font-semibold text-slate-950">
            <a href="{{ $card->showUrl }}" class="hover:text-brand-700">{{ $card->title }}</a>
        </h2>

        <div class="mt-3 space-y-1 text-sm text-slate-600">
            <p>{{ $card->durationLabel }}</p>
            @if ($card->features)
                <p>{{ implode(' · ', $card->features) }}</p>
            @endif
        </div>

        <div class="mt-4 flex items-end justify-between gap-3">
            <div>
                @if ($card->reviewsCount > 0)
                    <p class="text-sm font-medium text-slate-950">
                        {{ $card->ratingAvg }} ★
                        <span class="text-slate-500">({{ number_format($card->reviewsCount) }})</span>
                    </p>
                @endif
                <p class="mt-1 text-sm text-slate-500">{{ __('ui.labels.from') }}</p>
                <p class="text-base font-semibold text-slate-950">
                    @if ($card->originalPrice)
                        <span class="mr-1 text-sm font-medium text-slate-400 line-through">{{ $card->currency }} {{ $card->originalPrice }}</span>
                    @endif
                    {{ $card->currency }} {{ $card->priceFrom }}
                </p>
            </div>

            <x-ui.button href="{{ $card->showUrl }}" size="sm">
                {{ __('ui.nav.book_now') }}
            </x-ui.button>
        </div>
    </div>
</article>
