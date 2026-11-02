<meta property="og:site_name" content="{{ $seo['site_name'] ?? config('seo.site_name') }}">
<meta property="og:title" content="{{ $seo['social_title'] ?? $seo['title'] ?? config('seo.default_title') }}">
<meta property="og:description" content="{{ $seo['social_description'] ?? $seo['description'] ?? config('seo.default_description') }}">
<meta property="og:image" content="{{ $seo['image'] ?? config('seo.default_image') }}">
<meta property="og:type" content="website">
<meta name="twitter:card" content="{{ $seo['twitter_card'] ?? 'summary_large_image' }}">

@if(!empty($seo['image_alt']))
<meta property="og:image:alt" content="{{ $seo['image_alt'] }}">
@endif
@if(!empty($seo['canonical']))
<meta property="og:url" content="{{ $seo['canonical'] }}">
@endif

<meta name="twitter:title" content="{{ $seo['social_title'] ?? $seo['title'] ?? config('seo.default_title') }}">
<meta name="twitter:description" content="{{ $seo['social_description'] ?? $seo['description'] ?? config('seo.default_description') }}">
<meta name="twitter:image" content="{{ $seo['image'] ?? config('seo.default_image') }}">
@if(!empty($seo['image_alt']))<meta name="twitter:image:alt" content="{{ $seo['image_alt'] }}">@endif
