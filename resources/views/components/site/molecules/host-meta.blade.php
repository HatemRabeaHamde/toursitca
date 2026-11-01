{{-- Avatar + name + role (used inside the experience card). --}}
@props(['name', 'role' => null, 'avatar' => null])

<div {{ $attributes->class(['flex items-center gap-2.5 min-w-0']) }}>
    @if($avatar)
        <img src="{{ $avatar }}" alt="" width="40" height="40" loading="lazy" class="lv-avatar">
    @endif
    <div class="flex min-w-0 flex-col gap-px">
        <span class="lv-t-label-l lv-tone-ink truncate">{{ $name }}</span>
        @if($role)<span class="lv-t-caption lv-tone-muted truncate">{{ $role }}</span>@endif
    </div>
</div>
