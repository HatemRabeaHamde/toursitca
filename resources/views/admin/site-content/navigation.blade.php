<span class="adm-nav-section">{{ __('content.workspace') }}</span>
<a href="{{ route('admin.site-content.index') }}" class="adm-nav-link {{ request()->routeIs('admin.site-content.index') ? 'is-active' : '' }}">{{ __('content.site_content') }}</a>
@foreach(['hero.image'=>'home','seo.title'=>'search','geo.name'=>'publisher','aeo.summary'=>'answers'] as $field=>$label)
<a href="{{ route('admin.site-content.edit', ['page'=>'home','field'=>$field]) }}" class="adm-nav-link adm-studio-link {{ request()->routeIs('admin.site-content.edit') && request('field', 'hero.image') === $field ? 'is-active' : '' }}">{{ __('content.'.$label) }}</a>
@endforeach
