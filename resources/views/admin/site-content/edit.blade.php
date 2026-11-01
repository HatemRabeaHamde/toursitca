@extends('layouts.admin')
@section('page-title', $definition['label'].' content')
@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/trix/trix.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('vendor/trix/site-content.js') }}" defer></script>
<script src="{{ asset('vendor/trix/trix.umd.min.js') }}" defer></script>
@endpush
@section('page-subtitle', 'Edit words and images. Save a draft, preview, then publish.')
@section('content')
<div class="p-4 sm:p-6" x-data="{ dirty: false }" @input="dirty = true" @change="dirty = true" @beforeunload.window="if (dirty) { $event.preventDefault(); $event.returnValue = ''; }">
 @if(session('success'))<p role="status" class="mb-4 rounded border border-green-700 p-4">{{ session('success') }}</p>@endif
 @if($errors->any())<div role="alert" class="mb-4 rounded border border-red-700 p-4"><p>Content could not be saved:</p><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
 <form method="POST" enctype="multipart/form-data" action="{{ route('admin.site-content.update', $page) }}" @submit="dirty = false">
 @csrf
 <div class="sticky top-0 z-10 mb-6 flex flex-wrap items-center gap-3 border bg-white p-4">
  <button class="adm-btn adm-btn--ghost" name="mode" value="draft">Save draft</button>
  <button class="adm-btn adm-btn--primary" name="mode" value="publish">Publish</button>
  <a target="_blank" rel="noopener" class="px-3 py-2 underline" href="{{ route($definition['route'], ['locale'=>'en', 'content_preview'=>1]) }}" @click="if (dirty && !confirm('Preview shows the last saved draft. Continue?')) $event.preventDefault()">Preview saved draft ↗</a>
  <span x-show="dirty" class="text-sm" role="status">Unsaved changes</span>
 </div>
 @foreach($definition['sections'] as $sectionKey => $section)
 <section class="mb-6 rounded border bg-white p-4 sm:p-6" aria-labelledby="section-{{ $sectionKey }}">
  <h2 id="section-{{ $sectionKey }}" class="mb-6 text-xl font-semibold">{{ $section['label'] }}</h2>
  @foreach($section['fields'] as $fieldKey => $unused)
   @include('admin.site-content.field', ['field'=>$fields[$sectionKey.'.'.$fieldKey]])
  @endforeach
 </section>
 @endforeach
 </form>
 @foreach($fields as $field)
  @include('admin.site-content.history', ['field'=>$field])
 @endforeach
</div>
@endsection
