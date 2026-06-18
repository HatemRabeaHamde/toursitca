@props([
    'action',
    'agencies' => collect(),
    'experience' => null,
    'method' => 'POST',
    'showAgencySelect' => false,
    'submitLabel' => __('ui.nav.create_experience'),
])

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    @php
        $inclusionsText = old('inclusions_text');
        if ($inclusionsText === null && $experience?->inclusions) {
            $inclusionsText = implode(PHP_EOL, (array) $experience->getTranslation('inclusions', app()->getLocale(), false));
        }

        $exclusionsText = old('exclusions_text');
        if ($exclusionsText === null && $experience?->exclusions) {
            $exclusionsText = implode(PHP_EOL, (array) $experience->getTranslation('exclusions', app()->getLocale(), false));
        }
    @endphp

    @if ($showAgencySelect)
        <label class="block">
            <span class="label">{{ __('ui.fields.agency_name') }}</span>
            <select name="agency_id" class="input">
                <option value="">{{ __('ui.fields.platform_agency_default') }}</option>
                @foreach ($agencies as $agency)
                    <option value="{{ $agency->id }}" @selected(old('agency_id', $experience?->agency_id) == $agency->id)>
                        {{ $agency->name }}{{ $agency->is_platform ? ' - '.__('ui.status.platform') : '' }}
                    </option>
                @endforeach
            </select>
            @error('agency_id')
                <span class="error">{{ $message }}</span>
            @enderror
        </label>
    @endif

    <x-ui.card :title="__('ui.fields.title')">
        <div class="grid gap-4 md:grid-cols-3">
            @foreach (config('locales.supported') as $locale => $details)
                <x-ui.input name="title[{{ $locale }}]" :label="__('ui.fields.title').' ('.strtoupper($locale).')'" :value="old('title.'.$locale, $experience?->getTranslation('title', $locale, false))" />
            @endforeach
        </div>
    </x-ui.card>

    <x-ui.card :title="__('ui.fields.description')">
        <div class="grid gap-4 md:grid-cols-3">
            @foreach (config('locales.supported') as $locale => $details)
                <x-ui.textarea name="description[{{ $locale }}]" :label="__('ui.fields.description').' ('.strtoupper($locale).')'" :value="old('description.'.$locale, $experience?->getTranslation('description', $locale, false))" />
            @endforeach
        </div>
    </x-ui.card>

    <x-ui.card title="Basics">
        <div class="grid gap-4 md:grid-cols-3">
            <x-ui.input name="category" :label="__('ui.fields.category')" :value="old('category', $experience?->category)" required />
            <x-ui.select name="difficulty" :label="__('ui.fields.difficulty')" :options="[
                'easy' => __('ui.difficulty.easy'),
                'moderate' => __('ui.difficulty.moderate'),
                'hard' => __('ui.difficulty.hard'),
            ]" :selected="old('difficulty', $experience?->difficulty ?? 'easy')" required />
            <x-ui.input name="duration_hours" type="number" step="0.5" min="0.5" :label="__('ui.fields.duration_hours')" :value="old('duration_hours', $experience?->duration_hours)" required />
            <x-ui.input name="max_group_size" type="number" min="1" :label="__('ui.fields.max_group_size')" :value="old('max_group_size', $experience?->max_group_size)" required />
            <x-ui.input name="price_per_person" type="number" step="0.01" min="0" :label="__('ui.fields.price_per_person')" :value="old('price_per_person', $experience?->price_per_person)" required />
            <x-ui.input name="original_price" type="number" step="0.01" min="0" :label="__('ui.fields.original_price')" :value="old('original_price', $experience?->original_price)" />
            <x-ui.input name="deal_starts_at" type="datetime-local" :label="__('ui.fields.deal_starts_at')" :value="old('deal_starts_at', $experience?->deal_starts_at?->format('Y-m-d\\TH:i'))" />
            <x-ui.input name="deal_ends_at" type="datetime-local" :label="__('ui.fields.deal_ends_at')" :value="old('deal_ends_at', $experience?->deal_ends_at?->format('Y-m-d\\TH:i'))" />
            <x-ui.input name="private_price" type="number" step="0.01" min="0" :label="__('ui.fields.private_price')" :value="old('private_price', $experience?->private_price)" />
        </div>
        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                <input type="checkbox" name="pickup_enabled" value="1" class="rounded border-slate-300 text-brand-600" @checked(old('pickup_enabled', $experience?->pickup_enabled ?? false))>
                {{ __('ui.fields.pickup_enabled') }}
            </label>
            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                <input type="checkbox" name="is_top_rated" value="1" class="rounded border-slate-300 text-brand-600" @checked(old('is_top_rated', $experience?->is_top_rated ?? false))>
                {{ __('ui.fields.is_top_rated') }}
            </label>
        </div>
    </x-ui.card>

    <x-ui.card title="Location">
        <div class="grid gap-4 md:grid-cols-3">
            <x-ui.input name="location_city" :label="__('ui.fields.location_city')" :value="old('location_city', $experience?->location_city)" required />
            <x-ui.input name="location_lat" type="number" step="0.0000001" :label="__('ui.fields.location_lat')" :value="old('location_lat', $experience?->location_lat)" />
            <x-ui.input name="location_lng" type="number" step="0.0000001" :label="__('ui.fields.location_lng')" :value="old('location_lng', $experience?->location_lng)" />
        </div>
        <div class="mt-4">
            <x-ui.textarea name="meeting_point" :label="__('ui.fields.meeting_point')" :value="old('meeting_point', $experience?->meeting_point)" />
        </div>
    </x-ui.card>

    <x-ui.card title="Details">
        <div class="grid gap-4 md:grid-cols-2">
            <x-ui.textarea name="inclusions_text" :label="__('ui.fields.inclusions')" :value="$inclusionsText" />
            <x-ui.textarea name="exclusions_text" :label="__('ui.fields.exclusions')" :value="$exclusionsText" />
        </div>
    </x-ui.card>

    <x-ui.card title="Media">
        <div class="grid gap-4 md:grid-cols-3">
            <label class="block">
                <span class="label">{{ __('ui.fields.images') }}</span>
                <input name="images[]" type="file" multiple accept="image/*" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
                @error('images.*')
                    <span class="error">{{ $message }}</span>
                @enderror
            </label>

            <label class="block">
                <span class="label">{{ __('ui.fields.video_file') }}</span>
                <input name="video_file" type="file" accept="video/mp4,video/webm,video/quicktime" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
                @error('video_file')
                    <span class="error">{{ $message }}</span>
                @enderror
            </label>

            <x-ui.input name="video_url" type="url" :label="__('ui.fields.video_url')" :value="old('video_url')" />
        </div>
    </x-ui.card>

    <div class="flex justify-end">
        <x-ui.button type="submit">{{ $submitLabel }}</x-ui.button>
    </div>
</form>
