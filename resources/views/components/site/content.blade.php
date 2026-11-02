@props(['key'])
@php
    $value = site_content($key);
    [$pageName, $fieldPath] = explode('.', $key, 2);
    $type = request()->attributes->get('site_content')?->types[$fieldPath] ?? 'text';
@endphp
@if($type === 'richtext' && is_string($value))
    {{-- Raw output is allowed here only because SiteContentReader prepared this output using the server-side allowlist sanitizer. --}}
    <div data-content-key="{{ $fieldPath }}">{!! $value !!}</div>
@elseif(is_string($value))
    {{ $value }}
@endif
