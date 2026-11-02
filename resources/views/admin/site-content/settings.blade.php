@extends('layouts.admin')
@section('page-title', $settings['title'])
@section('page-subtitle', $settings['subtitle'])
@push('scripts')
<script>window.siteSettingsData = @js($previewData);</script>
<script src="{{ asset('vendor/trix/settings.js') }}"></script>
@endpush
@section('content')
<div class="ss-workspace" x-data="siteSettings" @site-content-language="locale = $event.detail.locale" @input="update($event)" @change="update($event)" @beforeunload.window="if (dirty) { $event.preventDefault(); $event.returnValue = ''; }">
 <div class="ss-breadcrumb"><a href="{{ route('admin.site-content.index') }}">{{ __('content.site_content') }}</a><span>/ {{ $definition['label'] }}</span><span>/ {{ $settings['title'] }}</span></div>
 <header class="ss-heading"><div><p class="ss-kicker">{{ $settings['purpose'] }}</p><h2>{{ $settings['title'] }}</h2><p>{{ $settings['subtitle'] }}</p></div><span class="ss-scope">{{ __('content.home_scope') }}</span></header>
 <nav class="ss-tabs" aria-label="{{ __('content.settings_navigation') }}">
  @foreach(config('site_content.panels') as $key=>$item)<a href="{{ route('admin.site-content.settings', [$page,$key]) }}" @if($panel===$key) aria-current="page" @endif>{{ $item['title'] }}</a>@endforeach
 </nav>
 @if(session('success'))<p class="ss-notice" role="status">{{ session('success') }}</p>@endif
 @if($errors->any())<div class="ss-errors" role="alert"><strong>{{ __('content.save_errors') }}</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
 <form method="POST" enctype="multipart/form-data" action="{{ route('admin.site-content.settings.update', [$page,$panel]) }}" @submit="dirty=false">
 @csrf
 <div class="ss-actions"><div><strong>{{ $hasDraft ? __('content.saved_draft') : __('content.no_draft') }}</strong><p>{{ __('content.scoped_publish') }}</p><span x-show="dirty" x-cloak>{{ __('content.unsaved') }}</span></div><div><button class="adm-btn adm-btn--ghost" name="mode" value="draft">{{ __('content.save_draft') }}</button><button class="adm-btn adm-btn--primary" name="mode" value="publish">{{ $settings['publish'] }}</button></div></div>
 <div class="ss-layout">
  <div class="ss-fields">
   <section class="ss-language"><label for="settings-language">{{ __('content.editing_language') }}</label><select id="settings-language" x-model="locale"><option value="en">English</option><option value="fr">Français</option><option value="nl">Nederlands</option></select><p>{{ __('content.locale_hint') }}</p></section>
   @foreach($groups as $group=>$groupFields)
   <h3 class="ss-group-title">{{ __('content.'.$group) }}</h3>
   @foreach($groupFields as $key=>$field)
    <section class="ss-field" id="field-{{ $field['encoded'] }}">@include('admin.site-content.field', ['field'=>$field])
     @if(!empty($field['advice']))<p class="ss-advice">{{ $field['advice'] }}</p>@endif
     @if(!empty($field['example']))<details class="ss-example"><summary>{{ __('content.example_label') }}</summary><p>{{ $field['example'] }}</p></details>@endif
    </section>
   @endforeach
   @endforeach
  </div>
  <aside class="ss-rail" aria-label="{{ __('content.preview_guidance') }}">
   @include('admin.site-content.settings-preview')
   @include('admin.site-content.settings-guidance')
  </aside>
 </div>
 </form>
 <details class="ss-history"><summary>{{ __('content.revisions') }} — {{ $settings['title'] }}</summary>@foreach($fields as $field)@include('admin.site-content.history', ['field'=>$field])@endforeach</details>
</div>
@endsection
