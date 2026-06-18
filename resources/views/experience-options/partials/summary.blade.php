<dl class="mb-4 grid gap-3 border-b border-slate-200 pb-4 text-sm md:grid-cols-2">
    <div>
        <dt class="font-medium text-slate-950">{{ __('ui.fields.price_type') }}</dt>
        <dd class="text-slate-600">{{ __('ui.price_type.'.$option->price_type) }}</dd>
    </div>
    <div>
        <dt class="font-medium text-slate-950">{{ __('ui.fields.status') }}</dt>
        <dd class="text-slate-600">{{ __('ui.status.'.$option->status) }}</dd>
    </div>
    <div>
        <dt class="font-medium text-slate-950">{{ __('ui.fields.prices') }}</dt>
        <dd class="text-slate-600">
            @foreach ($option->prices as $price)
                <span>{{ ucfirst($price->participant_type) }}: {{ $price->currency }} {{ $price->price }}</span>@if (! $loop->last)<br>@endif
            @endforeach
        </dd>
    </div>
    <div>
        <dt class="font-medium text-slate-950">{{ __('ui.fields.languages') }}</dt>
        <dd class="text-slate-600">{{ $option->languages->pluck('language_code')->join(', ') ?: '-' }}</dd>
    </div>
</dl>
