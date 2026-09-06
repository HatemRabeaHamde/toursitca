@extends('layouts.agency')

@section('title', __('ui.nav.profile'))

@section('page-title', __('ui.nav.profile'))

@section('content')

    <form method="POST" action="{{ route('agency.profile.update') }}" class="ag-form-stack">
        @csrf
        @method('PATCH')

        {{-- ── Basic info ──────────────────────────────────────── --}}
        <x-ui.card :title="__('ui.fields.agency_name')">
            <div class="ag-form-grid">

                <div>
                    <label for="name" class="label">{{ __('ui.fields.agency_name') }}</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name', $agency->name) }}"
                        class="input @error('name') is-invalid @enderror"
                        required
                        maxlength="120"
                    >
                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="city" class="label">{{ __('ui.fields.city') }}</label>
                    <input
                        id="city"
                        type="text"
                        name="city"
                        value="{{ old('city', $agency->city) }}"
                        class="input @error('city') is-invalid @enderror"
                        required
                        maxlength="80"
                    >
                    @error('city')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="label">{{ __('ui.fields.phone') }}</label>
                    <input
                        id="phone"
                        type="text"
                        name="phone"
                        value="{{ old('phone', $agency->phone) }}"
                        class="input @error('phone') is-invalid @enderror"
                        maxlength="30"
                    >
                    @error('phone')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </x-ui.card>

        {{-- ── Description ─────────────────────────────────────── --}}
        <x-ui.card :title="__('ui.fields.description')">
            <div class="ag-form-stack-inner">

                <div>
                    <label for="description_en" class="label">{{ __('ui.fields.description') }} (English)</label>
                    <textarea
                        id="description_en"
                        name="description[en]"
                        rows="5"
                        class="input @error('description.en') is-invalid @enderror"
                        maxlength="2000"
                    >{{ old('description.en', $agency->getTranslation('description', 'en', false)) }}</textarea>
                    @error('description.en')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description_fr" class="label">{{ __('ui.fields.description') }} (Français)</label>
                    <textarea
                        id="description_fr"
                        name="description[fr]"
                        rows="5"
                        class="input @error('description.fr') is-invalid @enderror"
                        maxlength="2000"
                    >{{ old('description.fr', $agency->getTranslation('description', 'fr', false)) }}</textarea>
                    @error('description.fr')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </x-ui.card>

        {{-- ── Languages ───────────────────────────────────────── --}}
        <x-ui.card :title="__('ui.fields.languages')">
            @php
                $currentLanguages = old('languages', $agency->languages ?? []);
                $allLanguages = [
                    'en' => 'English',
                    'fr' => 'Français',
                    'ar' => 'العربية',
                    'es' => 'Español',
                    'de' => 'Deutsch',
                    'it' => 'Italiano',
                    'nl' => 'Nederlands',
                ];
            @endphp
            <div class="ag-lang-checkboxes">
                @foreach ($allLanguages as $code => $label)
                    <label class="ag-checkbox-label">
                        <input
                            type="checkbox"
                            name="languages[]"
                            value="{{ $code }}"
                            @checked(in_array($code, (array) $currentLanguages))
                            class="ag-checkbox"
                        >
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            @error('languages')
                <p class="field-error mt-2">{{ $message }}</p>
            @enderror
        </x-ui.card>

        {{-- ── Submit ───────────────────────────────────────────── --}}
        <div class="ag-form-actions">
            <x-ui.button type="submit" variant="primary">
                {{ __('ui.nav.save_changes') }}
            </x-ui.button>
        </div>

    </form>

@endsection
