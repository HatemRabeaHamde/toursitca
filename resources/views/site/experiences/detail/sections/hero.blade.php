{{-- Hero: location, title, meta (duration · stops · group), rating + trust, photos. --}}
<x-site.organisms.page-hero
    class="lv-page-hero--detail"
    :image="$page->hero['image']"
    :location="$page->hero['location']"
    :title="$page->title"
    :breadcrumb="$page->breadcrumb">

    @if($page->hero['meta'])
        <ul class="lv-xhero__row">
            @foreach($page->hero['meta'] as $meta)
                <li class="lv-xhero__item lv-t-body-m"><x-site.atoms.icon :name="$meta['icon']" :size="18" />{{ $meta['label'] }}</li>
            @endforeach
        </ul>
    @endif

    @if($page->hero['rating'] || $page->hero['verified'])
        <div class="lv-xhero__row lv-xhero__row--trust">
            @if($page->hero['rating'])
                <a href="#reviews" class="lv-xhero__item lv-t-body-m">
                    <x-site.atoms.icon name="star-gold" :size="18" />
                    <strong class="lv-t-label-l">{{ $page->hero['rating'] }}</strong>
                    <span>{{ trans_choice('experiences.detail.reviews_count', $page->hero['reviews'], ['count' => $page->hero['reviews']]) }}</span>
                </a>
            @endif
            @if($page->hero['rating'] && $page->hero['verified'])<span class="lv-xhero__divider" aria-hidden="true"></span>@endif
            @if($page->hero['verified'])
                <span class="lv-xhero__item lv-t-body-m"><x-site.atoms.icon name="shield-cream" :size="18" />{{ __('experiences.detail.verified') }}</span>
            @endif
        </div>
    @endif

    @if($page->gallery)
        <button type="button" class="lv-xhero__photos lv-t-label-s" @click="show(0)" aria-haspopup="dialog">
            {{ trans_choice('experiences.detail.photos', count($page->gallery), ['count' => count($page->gallery)]) }}
        </button>
    @endif
</x-site.organisms.page-hero>
