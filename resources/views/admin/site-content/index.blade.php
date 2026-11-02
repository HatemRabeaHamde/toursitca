@extends('layouts.admin')
@section('page-title', __('content.studio'))
@section('page-subtitle', __('content.subtitle'))
@section('content')
<div class="sc-library">
 <section class="sc-library-panel">
  <p>{{ __('content.site_content') }}</p><h2>{{ __('content.page_title') }}</h2>
  <p>{{ __('content.intro') }}</p>
  @foreach($pages as $key=>$definition)
  <div class="sc-page-row"><div><strong>{{ $definition['label'] }}</strong><p>{{ __('content.sections', ['count'=>count($definition['sections'])]) }}</p></div><a class="adm-btn adm-btn--primary" href="{{ route('admin.site-content.edit', $key) }}">{{ __('content.open_editor') }} →</a></div>
  @endforeach
 </section>
 <aside class="sc-library-panel"><h2>{{ __('content.details_title') }}</h2><p>{{ __('content.details') }}</p><p>{{ __('content.search_details') }}</p><p>{{ __('content.managed') }}</p></aside>
</div>
@endsection
