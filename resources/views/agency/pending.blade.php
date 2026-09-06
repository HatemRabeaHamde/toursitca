@extends('layouts.agency')

@section('title', __('ui.pages.agency_pending_title'))

@section('content')
    <section class="rounded-md border border-slate-200 bg-white p-6">
        <h2 class="text-base font-semibold text-slate-950">{{ __('ui.pages.agency_pending_title') }}</h2>
        <p class="mt-2 text-sm text-slate-600">{{ __('ui.pages.agency_pending_body') }}</p>
    </section>
@endsection
