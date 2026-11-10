<title>{{ $seo['title'] ?? config('seo.default_title') }}</title>
<meta name="description" content="{{ $seo['description'] ?? config('seo.default_description') }}">
<meta name="robots" content="{{ $seo['robots'] ?? 'index, follow' }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('partials.head.icons')

@if(!empty($seo['canonical']))
<link rel="canonical" href="{{ $seo['canonical'] }}">
@endif
@foreach($seo['hreflang'] ?? [] as $language => $url)
<link rel="alternate" hreflang="{{ $language }}" href="{{ $url }}">
@endforeach

@if(!empty($seo['google_verification']))
<meta name="google-site-verification" content="{{ $seo['google_verification'] }}">
@endif
