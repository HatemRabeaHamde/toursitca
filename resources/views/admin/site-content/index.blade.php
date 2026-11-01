@extends('layouts.admin')
@section('page-title', 'Site content')
@section('content')
<div class="p-6">
 <p class="mb-6">Edit your site's words and images. Layouts stay fixed.</p>
 @foreach($pages as $key => $definition)
 <a class="block rounded border bg-white p-6" href="{{ route('admin.site-content.edit', $key) }}">{{ $definition['label'] }} → Edit content</a>
 @endforeach
</div>
@endsection
