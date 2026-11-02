@php($host = $page->host)

<section id="host" class="lv-xsection" aria-labelledby="host-title">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 id="host-title" class="lv-t-h2 lv-serif lv-tone-ink">{{ __('experiences.detail.v1.meet_host') }}</h2>
        <x-site.atoms.link-arrow :href="$host['href']" arrow="terracotta">{{ __('experiences.detail.host.view') }}</x-site.atoms.link-arrow>
    </div>
    <div class="lv-v1-host">
        @if($host['photo'])
            <img src="{{ $host['photo'] }}" alt="" width="176" height="196" loading="lazy" class="lv-v1-host__photo">
        @endif
        <div class="flex min-w-0 flex-col gap-1.5">
            <p class="lv-t-h3 lv-serif lv-tone-ink">{{ $host['name'] }}</p>
            @if($host['role'])<p class="lv-t-body-s lv-tone-muted">{{ $host['role'] }}</p>@endif
            <ul class="mt-2 flex flex-col gap-2 lv-t-body-s lv-tone-ink">
                @if($host['verified'])
                    <li class="flex items-center gap-2"><x-site.atoms.icon name="shield-success" :size="17" />{{ __('experiences.detail.host.verified') }}</li>
                @endif
                @if($host['licence'])
                    <li class="flex items-center gap-2"><x-site.atoms.icon name="card-ink" :size="17" />{{ __('experiences.detail.host.licence', ['number' => $host['licence']]) }}</li>
                @endif
                @if($host['languages'])
                    <li class="flex items-center gap-2"><x-site.atoms.icon name="globe" :size="17" class="lv-icon--ink" />{{ $host['languages'] }}</li>
                @endif
            </ul>
        </div>
        @if($host['bio'])<p class="lv-v1-host__bio lv-t-quote-s lv-tone-ink">{{ $host['bio'] }}</p>@endif
    </div>
</section>
