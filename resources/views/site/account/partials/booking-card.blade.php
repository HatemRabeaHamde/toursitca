{{-- One trip in My bookings. A pending request is outlined and can be cancelled. --}}
<article @class(['lv-bkcard', 'is-pending' => $card['status'] === 'pending'])>
    <img src="{{ $card['image'] }}" alt="" width="236" height="204" loading="lazy" class="lv-bkcard__image">
    <div class="flex min-w-0 flex-col items-start gap-2.5">
        <div class="flex w-full flex-wrap items-center justify-between gap-2">
            <span class="lv-pill lv-pill--{{ $card['status'] }} lv-t-micro">{{ __('account.bookings.status.'.$card['status']) }}</span>
            <span class="lv-t-caption lv-tone-muted">{{ $card['stamp'] }}</span>
        </div>
        <h3 class="lv-t-h4 lv-serif lv-tone-ink"><a href="{{ $card['href'] }}">{{ $card['title'] }}</a></h3>
        @if($card['hostLine'])
            <p class="flex items-center gap-2 lv-t-label-s lv-tone-ink">
                @if($card['hostAvatar'])<img src="{{ $card['hostAvatar'] }}" alt="" width="28" height="28" class="lv-avatar lv-avatar--28" loading="lazy">@endif{{ $card['hostLine'] }}
            </p>
        @endif
        <ul class="flex flex-wrap items-center gap-x-4 gap-y-1 lv-t-caption lv-tone-muted">
            <li class="flex items-center gap-1.5"><x-site.atoms.icon name="calendar" :size="15" />{{ $card['dates'] }}</li>
            <li class="flex items-center gap-1.5"><x-site.atoms.icon name="users" :size="15" />{{ $card['travellersShort'] }} · {{ $card['group'] }}</li>
            <li class="flex items-center gap-1.5"><x-site.atoms.icon name="card-muted" :size="15" />{{ $card['total'] }}@if($card['status'] === 'pending') · {{ __('account.bookings.not_charged') }}@endif</li>
        </ul>

        @if($card['status'] === 'pending')
            <p class="lv-bkcard__note lv-t-caption">{{ __('account.bookings.pending_note') }}</p>
        @elseif($card['reviewRating'])
            <p class="lv-bkcard__note lv-bkcard__note--ok lv-t-caption">{{ __('account.bookings.reviewed', ['rating' => number_format($card['reviewRating'], 1)]) }}</p>
        @endif

        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 pt-1">
            @if($card['reviewUrl'])
                <x-site.atoms.button :href="$card['reviewUrl']">{{ __('account.bookings.leave_review') }}</x-site.atoms.button>
            @else
                <x-site.atoms.button :href="$card['showUrl']" :variant="$card['status'] === 'pending' ? 'primary' : 'outline'">{{ __($card['status'] === 'pending' ? 'account.bookings.view_request' : 'account.bookings.view') }}</x-site.atoms.button>
            @endif
            @if(in_array($card['status'], ['completed', 'cancelled'], true))
                <x-site.atoms.link-arrow :href="$card['href']" arrow="terracotta">{{ __('account.bookings.book_again') }}</x-site.atoms.link-arrow>
            @endif
            @if($card['cancelUrl'])
                <form method="POST" action="{{ $card['cancelUrl'] }}" x-data @submit="if (! confirm(@js(__('account.bookings.cancel_confirm')))) $event.preventDefault()">
                    @csrf
                    <button type="submit" class="lv-t-label-s lv-tone-muted lv-link-button">{{ __('account.bookings.cancel') }}</button>
                </form>
            @endif
        </div>
    </div>
</article>
