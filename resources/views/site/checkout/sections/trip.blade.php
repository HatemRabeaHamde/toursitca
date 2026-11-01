@php
    $bookUrl = $trip['href'].'#book';
    $rows = array_filter([
        ['icon' => 'calendar-terracotta', 'label' => __('account.checkout.dates'), 'value' => $trip['dates'], 'edit' => $bookUrl],
        ['icon' => 'user-terracotta', 'label' => __('experiences.detail.booking.travellers'), 'value' => $trip['travellers'], 'edit' => $bookUrl],
        ['icon' => 'route-terracotta', 'label' => __('experiences.detail.v1.booking.group_type'), 'value' => $trip['groupLine'], 'edit' => $bookUrl],
        $checkoutSession->experience->pickup_enabled ? [
            'icon' => 'pin-terracotta-18',
            'label' => __('experiences.detail.booking.pickup'),
            'value' => $checkoutSession->pickup_status === 'add_now'
                ? ($checkoutSession->pickup_address ?: __('account.checkout.pickup_pinned'))
                : __('account.checkout.pickup_later'),
            'edit' => route('site.checkout.activity', ['locale' => app()->getLocale(), 'checkoutSession' => $checkoutSession]),
        ] : null,
    ]);
@endphp

<section class="lv-flow-card" aria-labelledby="trip-title">
    <h2 id="trip-title" class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.checkout.your_trip') }}</h2>
    <dl class="lv-trip-rows">
        @foreach($rows as $row)
            <div class="lv-trip-row">
                <x-site.atoms.icon :name="$row['icon']" :size="18" />
                <div class="min-w-0 flex-1">
                    <dt class="lv-t-caption lv-tone-muted">{{ $row['label'] }}</dt>
                    <dd class="lv-t-label-m lv-tone-ink">{{ $row['value'] }}</dd>
                </div>
                <a href="{{ $row['edit'] }}" class="lv-t-label-s lv-tone-terracotta" aria-label="{{ __('account.checkout.edit').' '.$row['label'] }}">{{ __('account.checkout.edit') }}</a>
            </div>
        @endforeach
    </dl>
</section>
