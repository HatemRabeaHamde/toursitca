{{-- Host grid, pagination or empty state. Re-rendered on every filter change (no data-reveal inside). --}}
@if($page->hosts)
    <ul class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-list>
        @foreach($page->hosts as $host)
            <li data-reveal-item data-card>
                <x-site.organisms.host-directory-card :href="$host['href']" :name="$host['name']" :role="$host['role']" :image="$host['image']"
                    :rating="$host['rating']" :reviews="$host['reviews']" :licence="$host['licence']" :experiences="$host['experiences']" />
            </li>
        @endforeach
    </ul>

    @if($page->paginator->hasPages())
        <div class="mt-12" data-pagination>{{ $page->paginator->fragment('directory')->links() }}</div>
    @endif
@else
    <div class="lv-directory__empty" data-card>
        <p class="lv-t-h4 lv-tone-ink">{{ __('hosts.empty.title') }}</p>
        <p class="lv-t-body-m lv-tone-muted">{{ __('hosts.empty.body') }}</p>
        <a href="{{ $page->indexUrl }}#directory" class="lv-arrow-link lv-t-label-m lv-tone-terracotta" data-filter-link>
            <span>{{ __('hosts.empty.reset') }}</span>
            <x-site.atoms.icon name="arrow-right-ink" :size="16" />
        </a>
    </div>
@endif
