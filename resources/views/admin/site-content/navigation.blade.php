<span class="adm-nav-section">{{ __('content.workspace') }}</span>
<a href="{{ route('admin.site-content.index') }}" class="adm-nav-link {{ request()->routeIs('admin.site-content.index') ? 'is-active' : '' }}">{{ __('content.site_content') }}</a>
<a href="{{ route('admin.site-content.edit', 'home') }}" class="adm-nav-link adm-studio-link {{ request()->routeIs('admin.site-content.edit') ? 'is-active' : '' }}">{{ __('content.home') }}</a>
@foreach(['search'=>'search','publisher'=>'publisher','answers'=>'answers'] as $panel=>$label)
<a href="{{ route('admin.site-content.settings', ['page'=>'home','panel'=>$panel]) }}" class="adm-nav-link adm-studio-link {{ request()->routeIs('admin.site-content.settings') && request()->route('panel') === $panel ? 'is-active' : '' }}" @if(request()->route('panel') === $panel) aria-current="page" @endif>{{ __('content.'.$label) }}</a>
@endforeach
