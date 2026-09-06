@php
    $adultPrice = $option?->prices->firstWhere('participant_type', 'adult');
    $childPrice = $option?->prices->firstWhere('participant_type', 'child');
    $languagesText = $option?->languages->pluck('language_code')->implode(PHP_EOL);
@endphp

<form method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-3 md:grid-cols-3">
        @foreach (config('locales.supported') as $locale => $details)
            <x-ui.input
                name="title[{{ $locale }}]"
                :label="__('ui.fields.title').' ('.strtoupper($locale).')'"
                :value="old('title.'.$locale, $option?->getTranslation('title', $locale, false))"
                :required="$locale === 'en'"
            />
        @endforeach
    </div>

    <div class="grid gap-3 md:grid-cols-2">
        <x-ui.input name="duration_minutes" type="number" min="1" :label="__('ui.fields.duration_minutes')" :value="old('duration_minutes', $option?->duration_minutes)" />
        <x-ui.input name="cancellation_hours" type="number" min="0" :label="__('ui.fields.cancellation_hours')" :value="old('cancellation_hours', $option?->cancellation_hours)" />
        <x-ui.select name="price_type" :label="__('ui.fields.price_type')" :options="[
            'per_person' => __('ui.price_type.per_person'),
            'per_group' => __('ui.price_type.per_group'),
        ]" :selected="old('price_type', $option?->price_type ?? 'per_person')" required />
        <x-ui.select name="status" :label="__('ui.fields.status')" :options="[
            'active' => __('ui.status.active'),
            'inactive' => __('ui.status.inactive'),
        ]" :selected="old('status', $option?->status ?? 'active')" required />
        <x-ui.input name="adult_price" type="number" step="0.01" min="0" :label="__('ui.fields.adult_price')" :value="old('adult_price', $adultPrice?->price)" required />
        <x-ui.input name="adult_original_price" type="number" step="0.01" min="0" :label="__('ui.fields.adult_original_price')" :value="old('adult_original_price', $adultPrice?->original_price)" />
        <x-ui.input name="child_price" type="number" step="0.01" min="0" :label="__('ui.fields.child_price')" :value="old('child_price', $childPrice?->price)" />
        <x-ui.input name="child_original_price" type="number" step="0.01" min="0" :label="__('ui.fields.child_original_price')" :value="old('child_original_price', $childPrice?->original_price)" />
    </div>

    <x-ui.textarea name="languages_text" :label="__('ui.fields.languages')" :value="old('languages_text', $languagesText)" />

    <div class="grid gap-3 md:grid-cols-3">
        <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
            <input type="checkbox" name="pickup_enabled" value="1" class="rounded border-slate-300 text-brand-600" @checked(old('pickup_enabled', $option?->pickup_enabled ?? false))>
            {{ __('ui.fields.pickup_enabled') }}
        </label>
        <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
            <input type="checkbox" name="private_available" value="1" class="rounded border-slate-300 text-brand-600" @checked(old('private_available', $option?->private_available ?? false))>
            {{ __('ui.fields.private_available') }}
        </label>
        <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
            <input type="checkbox" name="pay_later_enabled" value="1" class="rounded border-slate-300 text-brand-600" @checked(old('pay_later_enabled', $option?->pay_later_enabled ?? false))>
            {{ __('ui.fields.pay_later_enabled') }}
        </label>
    </div>

    <div class="flex justify-end">
        <x-ui.button type="submit">{{ $method === 'POST' ? __('ui.nav.create_option') : __('ui.nav.update') }}</x-ui.button>
    </div>
</form>
