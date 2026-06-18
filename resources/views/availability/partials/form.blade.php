@props([
    'action',
    'experiences',
    'availability' => null,
    'method' => 'POST',
])

<form method="POST" action="{{ $action }}" class="grid gap-4 md:grid-cols-6 md:items-end">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="md:col-span-2">
        <x-ui.select
            name="experience_id"
            :label="__('ui.fields.experience')"
            :options="$experiences->mapWithKeys(fn ($experience) => [$experience->id => $experience->getTranslation('title', app()->getLocale(), false).' · '.$experience->location_city])->all()"
            :selected="old('experience_id', $availability?->experience_id)"
            required
        />
    </div>

    <div class="md:col-span-2">
        <label for="experience_option_id_{{ $availability?->id ?? 'new' }}" class="mb-1 block text-sm font-medium text-slate-700">
            {{ __('ui.fields.experience_option') }}
        </label>
        <select
            id="experience_option_id_{{ $availability?->id ?? 'new' }}"
            name="experience_option_id"
            class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500"
        >
            <option value="">{{ __('ui.fields.general_availability') }}</option>
            @foreach ($experiences as $experience)
                @if ($experience->options->isNotEmpty())
                    <optgroup label="{{ $experience->getTranslation('title', app()->getLocale(), false) }}">
                        @foreach ($experience->options as $option)
                            <option value="{{ $option->id }}" @selected((string) old('experience_option_id', $availability?->experience_option_id) === (string) $option->id)>
                                {{ $option->getTranslation('title', app()->getLocale(), false) }}
                            </option>
                        @endforeach
                    </optgroup>
                @endif
            @endforeach
        </select>
        @error('experience_option_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <x-ui.input name="date" type="date" :label="__('ui.fields.date')" :value="old('date', $availability?->date?->toDateString())" required />
    <x-ui.input name="time_slot" type="time" :label="__('ui.fields.time_slot')" :value="old('time_slot', $availability?->time_slot ? substr($availability->time_slot, 0, 5) : null)" required />
    <x-ui.input name="max_seats" type="number" min="1" :label="__('ui.fields.max_seats')" :value="old('max_seats', $availability?->max_seats)" required />

    <label class="flex items-center gap-2 pb-2 text-sm text-slate-700">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-brand-600" @checked(old('is_active', $availability?->is_active ?? true))>
        {{ __('ui.fields.active') }}
    </label>

    <div class="md:col-span-6 flex justify-end">
        <x-ui.button type="submit">{{ $method === 'POST' ? __('ui.nav.create_availability') : __('ui.nav.update_availability') }}</x-ui.button>
    </div>
</form>
