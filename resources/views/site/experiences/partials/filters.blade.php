<form method="GET" action="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}" class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
    <div class="grid gap-3 md:grid-cols-4">
        <x-ui.input name="search" :label="__('ui.fields.search')" :value="request('search')" />
        <x-ui.input name="city" :label="__('ui.fields.location_city')" :value="request('city')" />
        <x-ui.input name="category" :label="__('ui.fields.category')" :value="request('category')" />
        <x-ui.input name="date" type="date" :label="__('ui.fields.date')" :value="request('date')" />
        <x-ui.input name="participants" type="number" min="1" :max="config('booking.max_participants')" :label="__('ui.landing.search.travelers')" :value="request('participants')" />
        <x-ui.input name="min_price" type="number" step="0.01" min="0" :label="__('ui.fields.min_price')" :value="request('min_price')" />
        <x-ui.input name="max_price" type="number" step="0.01" min="0" :label="__('ui.fields.max_price')" :value="request('max_price')" />
        <x-ui.select name="sort" :label="__('ui.fields.sort')" :options="[
            'recommended' => __('ui.sort.recommended'),
            'price_low' => __('ui.sort.price_low'),
            'price_high' => __('ui.sort.price_high'),
            'rating' => __('ui.sort.rating'),
            'newest' => __('ui.sort.newest'),
        ]" :selected="request('sort', 'recommended')" />
    </div>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-4">
            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                <input type="checkbox" name="private_available" value="1" class="rounded border-slate-300 text-brand-600" @checked(request()->boolean('private_available'))>
                {{ __('ui.features.private_option_available') }}
            </label>
            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                <input type="checkbox" name="pickup_available" value="1" class="rounded border-slate-300 text-brand-600" @checked(request()->boolean('pickup_available'))>
                {{ __('ui.features.pickup_available') }}
            </label>
        </div>

        <div class="flex items-center gap-2">
            <x-ui.button type="submit" size="sm">{{ __('ui.nav.filter') }}</x-ui.button>
            <x-ui.button :href="route('site.experiences.index', ['locale' => app()->getLocale()])" variant="secondary" size="sm">
                {{ __('ui.actions.reset_filters') }}
            </x-ui.button>
        </div>
    </div>
</form>
