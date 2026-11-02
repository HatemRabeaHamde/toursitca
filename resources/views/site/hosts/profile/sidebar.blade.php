@if($trip = $page->featured())
    <div class="lv-hp-book">
        <x-site.atoms.eyebrow>{{ __('hosts.profile.travel_with', ['name' => $page->name]) }}</x-site.atoms.eyebrow>
        <p class="lv-t-h4 lv-serif lv-tone-ink">{{ $trip['title'] }}</p>
        <p class="flex flex-wrap items-baseline gap-1.5">
            <span class="lv-t-body-m lv-tone-muted">{{ __('hosts.profile.from') }}</span>
            <span class="lv-bprice">{{ $trip['price'] }}</span>
            <span class="lv-t-caption lv-tone-muted">{{ __('hosts.profile.per_person') }}</span>
        </p>
        <x-site.atoms.button :href="$trip['href'].'#book'" class="w-full">{{ __('hosts.profile.check_dates') }}</x-site.atoms.button>
    </div>
@endif

@if($page->trust)
    <div class="lv-hp-trust">
        <h2 class="lv-t-h4 lv-serif lv-tone-ink">{{ __('hosts.profile.trust', ['name' => $page->name]) }}</h2>
        <ul class="flex flex-col gap-3.5">
            @foreach($page->trust as $item)
                <li class="flex items-start gap-2.5">
                    <x-site.atoms.icon :name="$item['icon']" :size="17" class="mt-0.5" />
                    <span class="flex min-w-0 flex-col gap-0.5">
                        <span class="lv-t-label-s lv-tone-ink">{{ $item['title'] }}</span>
                        @if($item['body'])<span class="lv-t-caption lv-tone-muted">{{ $item['body'] }}</span>@endif
                    </span>
                </li>
            @endforeach
        </ul>
        <x-site.atoms.link-arrow :href="$page->vettingUrl" arrow="terracotta" class="self-start">{{ __('hosts.vetted_link') }}</x-site.atoms.link-arrow>
    </div>
@endif
