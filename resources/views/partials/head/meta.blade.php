<title>{{ $seo['title'] ?? config('seo.default_title') }}</title>
<meta name="description" content="{{ $seo['description'] ?? config('seo.default_description') }}">
<meta name="robots" content="{{ $seo['robots'] ?? 'index, follow' }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
