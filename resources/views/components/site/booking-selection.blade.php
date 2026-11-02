@props(['snapshot' => []])

@if($snapshot)
    <section class="rounded-xl border border-slate-200 bg-white p-4 mb-5 space-y-3">
        <h2 class="font-semibold">{{ __('account.checkout.your_trip') }}</h2>
        <dl class="grid gap-3 sm:grid-cols-2">
            <div><dt class="text-sm text-slate-500">{{ __('ui.fields.title') }}</dt><dd>{{ $snapshot['title'][app()->getLocale()] ?? $snapshot['title']['en'] ?? '' }}</dd></div>
            @if($snapshot['option'])<div><dt class="text-sm text-slate-500">{{ __('booking_flow.card.option') }}</dt><dd>{{ $snapshot['option'][app()->getLocale()] ?? $snapshot['option']['en'] ?? '' }}</dd></div>@endif
            <div><dt class="text-sm text-slate-500">{{ __('account.sent.host') }}</dt><dd>{{ $snapshot['host_name'] ?? $snapshot['agency_name'] }}</dd></div>
            <div><dt class="text-sm text-slate-500">{{ __('account.checkout.dates') }}</dt><dd>{{ $snapshot['date'] }} · {{ $snapshot['time'] }} · {{ $snapshot['timezone'] ?? '' }}</dd></div>
            @if($snapshot['cancellation_hours'] !== null)<div><dt class="text-sm text-slate-500">{{ __('ui.fields.cancellation_hours') }}</dt><dd>{{ __('booking_flow.options.cancellation', ['hours' => $snapshot['cancellation_hours']]) }}</dd></div>@endif
            @if($snapshot['meeting_point'])<div><dt class="text-sm text-slate-500">{{ __('ui.fields.meeting_point') }}</dt><dd>{{ $snapshot['meeting_point'] }}</dd></div>@endif
        </dl>
    </section>
@endif
