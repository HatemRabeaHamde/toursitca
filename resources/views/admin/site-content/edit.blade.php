@extends('layouts.admin')
@section('page-title', $definition['label'].' content')
@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/trix/visual-editor.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/trix/trix.css') }}">
@endpush
@push('scripts')
<script>window.siteContentFields = @js(array_keys($fields)); window.siteContentFields = Object.fromEntries(window.siteContentFields.map(key => [key, true])); window.siteContentPreview = @js(route($definition['route'], ['locale'=>'en','content_preview'=>1,'content_edit'=>1]));</script>
<script src="{{ asset('vendor/trix/visual-editor.js') }}"></script>
<script src="{{ asset('vendor/trix/site-content.js') }}" defer></script>
<script src="{{ asset('vendor/trix/trix.umd.min.js') }}" defer></script>
@endpush
@section('page-subtitle', 'Click any highlighted text or image on the page to edit it.')
@section('content')
<div class="sc-workspace" x-data="siteContentEditor" @input="update($event)" @trix-change="update($event)" @beforeunload.window="if (dirty) { $event.preventDefault(); $event.returnValue = ''; }">
 @if(session('success'))<p role="status" class="mb-4 rounded border border-green-700 p-4">{{ session('success') }}</p>@endif
 @if($errors->any())<div role="alert" class="mb-4 rounded border border-red-700 p-4"><p>Content could not be saved:</p><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
 <form method="POST" enctype="multipart/form-data" action="{{ route('admin.site-content.update', $page) }}" @submit="dirty = false">
 @csrf
 <div class="sc-toolbar">
  <button class="adm-btn adm-btn--ghost" name="mode" value="draft">Save draft</button>
  <button class="adm-btn adm-btn--primary" name="mode" value="publish">Publish</button>
  <a target="_blank" rel="noopener" class="px-3 py-2 underline" href="{{ route($definition['route'], ['locale'=>'en', 'content_preview'=>1]) }}" @click="if (dirty && !confirm('Preview shows the last saved draft. Continue?')) $event.preventDefault()">Preview saved draft ↗</a>
  <span x-show="dirty" class="text-sm" role="status">Unsaved changes</span>
 </div>
 <div class="sc-toolbar">
  <span class="sc-help">Click text or an image to edit. Layout is locked.</span>
  <label>Preview language <select x-model="locale" @change="changeLocale()"><option value="en">EN</option><option value="fr">FR</option><option value="nl">NL</option></select></label>
  <label>Content &amp; search settings <select x-model="selected"><option value="">Choose a field…</option>@foreach($fields as $key=>$field)<option value="{{ $key }}">{{ $definition['sections'][explode('.', $key)[0]]['label'] }} — {{ $field['label'] }}</option>@endforeach</select></label>
 </div>
 <iframe x-ref="canvas" class="sc-frame" title="Editable homepage preview" src="{{ route($definition['route'], ['locale'=>'en','content_preview'=>1,'content_edit'=>1]) }}"></iframe>
 <aside class="sc-inspector" x-show="selected" x-cloak aria-label="Selected content">
  <button type="button" class="adm-btn adm-btn--ghost" @click="selected = ''">Close editor ×</button>
  @foreach($fields as $key=>$field)
   <div x-show="selected === @js($key)">
    <p class="sc-help">{{ $definition['sections'][explode('.', $key)[0]]['label'] }}</p>
    @include('admin.site-content.field', ['field'=>$field])
   </div>
  @endforeach
 </aside>
 </form>
 <details class="p-4"><summary>Revision history</summary>
 @foreach($fields as $field)
  @include('admin.site-content.history', ['field'=>$field])
 @endforeach
 </details>
</div>
@endsection
