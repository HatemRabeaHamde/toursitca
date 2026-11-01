{{-- "Your host" card on the experience page: avatar, role · city · languages, licence, profile link. --}}
@props(['host'])

<div {{ $attributes->class(['lv-hprofile']) }}>
    @if($host['avatar'])
        <img src="{{ $host['avatar'] }}" alt="" width="96" height="96" loading="lazy" class="lv-hprofile__avatar">
    @endif
    <div class="flex min-w-0 flex-col items-start gap-1.5">
        <p class="lv-t-h4 lv-serif lv-tone-ink">{{ $host['name'] }}</p>
        @if($host['line'])<p class="lv-t-body-s lv-tone-muted">{{ $host['line'] }}</p>@endif
        @if($host['verified'])
            <x-site.atoms.verified-badge>
                {{ collect([__('experiences.detail.host.verified'), $host['licence'] ? __('experiences.detail.host.licence', ['number' => $host['licence']]) : null])->filter()->implode(' · ') }}
            </x-site.atoms.verified-badge>
        @endif
        <x-site.atoms.link-arrow :href="$host['href']" arrow="terracotta" class="pt-1">{{ __('experiences.detail.host.view') }}</x-site.atoms.link-arrow>
    </div>
</div>
