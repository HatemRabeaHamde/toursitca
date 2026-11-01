{{--
    Host profile form, shared by the agency dashboard and the admin panel.
    $admin adds ownership, visibility and verification. Translatable fields use language tabs.
--}}
@props([
    'action',
    'method' => 'POST',
    'host' => null,
    'destinations' => [],
    'interests' => [],
    'languages' => [],
    'admin' => false,
    'agencies' => collect(),
])

@php
    $locales = array_keys(config('locales.supported'));
    $value = fn (string $field, string $locale) => old("$field.$locale", $host?->getTranslation($field, $locale, false));
    $lines = fn (string $field, string $locale, string $glue) => old("{$field}_text.$locale", implode($glue, (array) ($host?->getTranslation($field, $locale, false) ?: [])));
    $spoken = old('languages', $host?->languages ?? []);
    $paragraphBreak = "\n\n";
    $lineBreak = "\n";
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="xp-form-stack" x-data="{ locale: 'en' }">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    @if ($errors->any())
        <x-ui.alert type="error">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </x-ui.alert>
    @endif

    @if ($admin)
        <x-ui.card :title="__('hosts_admin.form.review')">
            <div class="xp-form-grid-3">
                <x-ui.select name="agency_id" :label="__('hosts_admin.form.agency')" required
                    :options="$agencies->mapWithKeys(fn ($a) => [$a->id => $a->name.($a->is_platform ? ' — Platform' : '')])->all()"
                    :selected="old('agency_id', $host?->agency_id)" />
                <x-ui.input name="sort_order" type="number" min="0" :label="__('hosts_admin.form.sort_order')" :value="$host?->sort_order ?? 0" />
            </div>
            <div class="mt-4 flex flex-col gap-3">
                <label class="flex items-start gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $host?->is_active))><span><strong>{{ __('hosts_admin.form.active') }}</strong><br><span class="help">{{ __('hosts_admin.form.active_help') }}</span></span></label>
                <label class="flex items-start gap-2 text-sm"><input type="checkbox" name="is_verified" value="1" @checked(old('is_verified', $host?->is_verified))><span><strong>{{ __('hosts_admin.form.verified') }}</strong><br><span class="help">{{ __('hosts_admin.form.verified_help') }}</span></span></label>
            </div>
        </x-ui.card>
    @endif

    <x-ui.card :title="__('hosts_admin.form.who')">
        <div class="xp-form-grid-3">
            <x-ui.input name="name" :label="__('hosts_admin.form.name')" :value="$host?->name" required maxlength="120" />
            <x-ui.input name="city" :label="__('hosts_admin.form.city')" :value="$host?->city" maxlength="80" />
            <x-ui.input name="partner_since" type="number" :label="__('hosts_admin.form.partner_since')" :value="$host?->partner_since" min="1950" max="{{ now()->year }}" />
            <x-ui.select name="destination_id" :label="__('hosts_admin.form.region')" :options="$destinations" :selected="$host?->destination_id" :placeholder="'—'" />
            <x-ui.select name="interest_id" :label="__('hosts_admin.form.specialty')" :options="$interests" :selected="$host?->interest_id" :placeholder="'—'" />
            <x-ui.input name="licence_number" :label="__('hosts_admin.form.licence')" :value="$host?->licence_number" maxlength="40" :help="__('hosts_admin.form.licence_help')" />
        </div>

        <fieldset class="mt-5">
            <legend class="label">{{ __('hosts_admin.form.languages') }}</legend>
            <div class="flex flex-wrap gap-x-5 gap-y-2">
                @foreach ($languages as $code => $label)
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="languages[]" value="{{ $code }}" @checked(in_array($code, (array) $spoken, true))>{{ $label }}</label>
                @endforeach
            </div>
        </fieldset>
    </x-ui.card>

    <x-ui.card :title="__('hosts_admin.form.photos')">
        <div class="xp-form-grid-2">
            @foreach (['photo' => 'photo_url', 'cover' => 'cover_url'] as $field => $column)
                <div>
                    <label for="{{ $field }}" class="label">{{ __('hosts_admin.form.'.$field) }}</label>
                    @if ($host?->{$column})
                        <img src="{{ \Illuminate\Support\Str::startsWith($host->{$column}, ['http', '/']) ? $host->{$column} : asset($host->{$column}) }}" alt="" class="mb-2 h-24 w-24 rounded-md object-cover">
                    @endif
                    <input id="{{ $field }}" name="{{ $field }}" type="file" accept="image/jpeg,image/png,image/webp" class="input">
                    <span class="help">{{ __('hosts_admin.form.'.$field.'_help') }}</span>
                    @error($field)<span class="error">{{ $message }}</span>@enderror
                </div>
            @endforeach
        </div>
    </x-ui.card>

    <x-ui.card :title="__('hosts_admin.form.profile')">
        <div class="mb-4 flex gap-2" role="tablist" aria-label="{{ __('hosts_admin.form.language_tabs') }}">
            @foreach ($locales as $locale)
                <button type="button" class="badge" :class="locale === '{{ $locale }}' ? 'badge-green' : 'badge-slate'" @click="locale = '{{ $locale }}'" :aria-pressed="(locale === '{{ $locale }}').toString()">{{ strtoupper($locale) }}</button>
            @endforeach
        </div>

        @foreach ($locales as $locale)
            <div x-show="locale === '{{ $locale }}'" @if (! $loop->first) x-cloak @endif class="flex flex-col gap-4">
                <x-ui.input name="role[{{ $locale }}]" :label="__('hosts_admin.form.role')" :value="$value('role', $locale)" :required="$locale === 'en'" maxlength="120" :help="$locale === 'en' ? __('hosts_admin.form.role_help') : __('hosts_admin.form.fallback_help')" />
                <x-ui.textarea name="bio[{{ $locale }}]" :label="__('hosts_admin.form.bio')" rows="3" :value="$value('bio', $locale)" :help="__('hosts_admin.form.bio_help')" />
                <x-ui.textarea name="quote[{{ $locale }}]" :label="__('hosts_admin.form.quote')" rows="2" :value="$value('quote', $locale)" :help="__('hosts_admin.form.quote_help')" />
                <x-ui.textarea name="story_text[{{ $locale }}]" :label="__('hosts_admin.form.story')" rows="6" :value="$lines('story', $locale, $paragraphBreak)" :help="__('hosts_admin.form.story_help')" />
                <x-ui.textarea name="skills_text[{{ $locale }}]" :label="__('hosts_admin.form.skills')" rows="4" :value="$lines('skills', $locale, $lineBreak)" :help="__('hosts_admin.form.skills_help')" />
            </div>
        @endforeach
    </x-ui.card>

    <div class="flex items-center gap-3">
        <x-ui.button type="submit">{{ __('hosts_admin.form.save') }}</x-ui.button>
        @unless ($admin)<span class="help">{{ __('hosts_admin.form.review_note') }}</span>@endunless
    </div>
</form>
