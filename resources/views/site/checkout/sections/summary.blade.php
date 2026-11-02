<div class="lv-flow-summary">
    <img src="{{ $trip['image'] }}" alt="" width="316" height="163" class="lv-flow-summary__image">
    <p class="flex items-center gap-1.5 lv-t-caption lv-tone-muted"><x-site.atoms.icon name="pin" :size="14" />{{ $trip['place'] }}</p>
    <p class="lv-t-h4 lv-serif lv-tone-ink">{{ $trip['title'] }}</p>
    @if($trip['hostLine'])
        <div class="flex items-center gap-2.5">
            @if($trip['hostAvatar'])<img src="{{ $trip['hostAvatar'] }}" alt="" width="38" height="38" class="lv-avatar lv-avatar--38">@endif
            <div class="flex min-w-0 flex-col">
                <span class="lv-t-label-s lv-tone-ink">{{ $trip['hostLine'] }}</span>
                @if($trip['hostVerified'])
                    <x-site.atoms.verified-badge>{{ collect([__('hosts.profile.verified'), $trip['hostLicence']])->filter()->implode(' · ') }}</x-site.atoms.verified-badge>
                @endif
            </div>
        </div>
    @endif
    @if($trip['time'])<p class="lv-t-caption lv-tone-muted">{{ __('experience_review.time') }}: {{ $trip['time'] }}</p>@endif
    @if($trip['cancellationHours'] !== null)<p class="lv-t-caption lv-tone-muted">{{ __('booking_flow.options.cancellation', ['hours' => $trip['cancellationHours']]) }}</p>@endif
    <hr class="lv-flow-rule">
    <p class="flex items-center justify-between gap-4 lv-t-body-s"><span class="lv-tone-muted">{{ $trip['priceLine'] }}</span><span class="lv-t-label-m lv-tone-ink">{{ $trip['total'] }}</span></p>
    <hr class="lv-flow-rule">
    <div class="flex items-center justify-between gap-4">
        <span class="lv-t-label-m lv-tone-ink">{{ __('experiences.detail.booking.total') }}</span>
        <span class="lv-flow-summary__total">{{ $trip['total'] }}</span>
    </div>
    <p class="lv-t-caption lv-tone-muted">{{ __('account.checkout.final_note') }}</p>
</div>

<ul class="lv-flow-trust lv-t-caption lv-tone-ink">
    <li><x-site.atoms.icon name="card-ink" :size="17" />{{ __('account.checkout.trust_card') }}</li>
    @if($checkoutSession->reserved_until?->isFuture())
        <li><x-site.atoms.icon name="clock" :size="17" />{{ __('account.checkout.trust_hold', ['minutes' => max(1, (int) ceil(now()->diffInSeconds($checkoutSession->reserved_until) / 60))]) }}</li>
    @endif
    @if($trip['hostVerified'])
        <li><x-site.atoms.icon name="shield-success" :size="17" />{{ __('account.checkout.trust_host') }}</li>
    @endif
</ul>
